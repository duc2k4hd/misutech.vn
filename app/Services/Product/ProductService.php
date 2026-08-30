<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Series;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * Create or Update a product with media relationships.
     * Tự động bỏ qua các ô để trống và gán NULL / giá trị mặc định an toàn.
     * Tự động tạo Brand và Series nếu điền tên mới (CHỈ KHI TẠO MỚI).
     */
    public function saveProduct(array $data, ?int $id = null): Product
    {
        return DB::transaction(function () use ($data, $id) {
            $productData = collect($data)->except(['thumbnail_id', 'gallery_ids', 'catalog_ids'])->toArray();

            // 1. Chuẩn hóa các trường rỗng chuỗi "" thành null
            foreach ($productData as $key => $value) {
                if (is_string($value) && trim($value) === '') {
                    $productData[$key] = null;
                }
            }

            // 2. Set default slug nếu để trống
            if (empty($productData['slug']) && !empty($productData['name'])) {
                $baseSlug = Str::slug($productData['name']);
                $slug = $baseSlug;
                $counter = 1;
                while (Product::where('slug', $slug)->when($id, fn($q) => $q->where('id', '!=', $id))->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $productData['slug'] = $slug;
            }

            // 3. Set default SKU nếu để trống
            if (empty($productData['sku'])) {
                $productData['sku'] = 'SP-' . strtoupper(Str::random(6));
                while (Product::where('sku', $productData['sku'])->when($id, fn($q) => $q->where('id', '!=', $id))->exists()) {
                    $productData['sku'] = 'SP-' . strtoupper(Str::random(6));
                }
            }

            // 4. Giá tiền mặc định là 0 nếu để trống
            if (!isset($productData['price']) || $productData['price'] === null) {
                $productData['price'] = 0.00;
            } else {
                $productData['price'] = (float)$productData['price'];
            }

            if (isset($productData['sale_price']) && $productData['sale_price'] !== null) {
                $productData['sale_price'] = (float)$productData['sale_price'];
            } else {
                $productData['sale_price'] = null;
            }

            // 5. Trạng thái mặc định là active nếu để trống
            if (empty($productData['status'])) {
                $productData['status'] = 'active';
            }

            // 6. Xử lý Brand & Series (CHỈ KHI TẠO MỚI SẢN PHẨM: $id === null)
            if ($id === null) {
                // Brand
                if (!empty($productData['brand_id'])) {
                    $brandVal = $productData['brand_id'];
                    if (is_numeric($brandVal) && Brand::where('id', (int)$brandVal)->exists()) {
                        $productData['brand_id'] = (int)$brandVal;
                    } else {
                        $brandName = trim((string)$brandVal);
                        $existingBrand = Brand::whereRaw('LOWER(name) = ?', [mb_strtolower($brandName)])->first();
                        if ($existingBrand) {
                            $productData['brand_id'] = $existingBrand->id;
                        } else {
                            $newBrand = Brand::create([
                                'name' => $brandName,
                                'slug' => Str::slug($brandName),
                            ]);
                            $productData['brand_id'] = $newBrand->id;
                        }
                    }
                } else {
                    $productData['brand_id'] = null;
                }

                // Series
                if (!empty($productData['series_id'])) {
                    $seriesVal = $productData['series_id'];
                    if (is_numeric($seriesVal) && Series::where('id', (int)$seriesVal)->exists()) {
                        $productData['series_id'] = (int)$seriesVal;
                    } else {
                        $seriesName = trim((string)$seriesVal);
                        $existingSeries = Series::whereRaw('LOWER(name) = ?', [mb_strtolower($seriesName)])->first();
                        if ($existingSeries) {
                            $productData['series_id'] = $existingSeries->id;
                        } else {
                            $newSeries = Series::create([
                                'name' => $seriesName,
                                'slug' => Str::slug($seriesName),
                                'brand_id' => $productData['brand_id'] ?? null,
                                'category_id' => $productData['category_id'] ?? null,
                                'status' => 'active',
                            ]);
                            $productData['series_id'] = $newSeries->id;
                        }
                    }
                } else {
                    $productData['series_id'] = null;
                }
            } else {
                // Khi Update: Chuẩn hóa int ID hoặc null
                $productData['brand_id'] = !empty($productData['brand_id']) && is_numeric($productData['brand_id']) ? (int)$productData['brand_id'] : null;
                $productData['series_id'] = !empty($productData['series_id']) && is_numeric($productData['series_id']) ? (int)$productData['series_id'] : null;
            }

            // Category ID: Nếu rỗng gán null
            $productData['category_id'] = !empty($productData['category_id']) && is_numeric($productData['category_id']) ? (int)$productData['category_id'] : null;

            // 7. Create or Update
            if ($id) {
                $product = Product::findOrFail($id);
                $product->update($productData);
            } else {
                $product = Product::create($productData);
            }

            // 8. Sync media
            $this->syncMedia($product, $data['thumbnail_id'] ?? null, $data['gallery_ids'] ?? [], $data['catalog_ids'] ?? []);

            return $product;
        });
    }

    /**
     * Delete a product (Soft Delete).
     */
    public function deleteProduct(int $id): bool
    {
        $product = Product::findOrFail($id);
        return $product->delete();
    }

    /**
     * Delete multiple products (Soft Delete).
     */
    public function deleteProducts(array $ids): int
    {
        return Product::whereIn('id', $ids)->delete();
    }

    /**
     * Sync thumbnail, gallery, and catalog media.
     */
    protected function syncMedia(Product $product, ?int $thumbnailId, array $galleryIds = [], array $catalogIds = []): void
    {
        $syncData = [];

        // Add Thumbnail
        if ($thumbnailId) {
            $syncData[$thumbnailId] = [
                'role' => 'thumbnail',
                'position' => 0,
            ];
        }

        // Add Gallery (ensure uniqueness and position)
        $position = 1;
        foreach ($galleryIds as $mediaId) {
            if ($mediaId && $mediaId != $thumbnailId) {
                $syncData[$mediaId] = [
                    'role' => 'gallery',
                    'position' => $position++,
                ];
            }
        }
        
        // Add Catalogs (ensure uniqueness and position)
        $position = 1;
        foreach ($catalogIds as $mediaId) {
            if ($mediaId && !isset($syncData[$mediaId])) {
                $syncData[$mediaId] = [
                    'role' => 'catalog',
                    'position' => $position++,
                ];
            }
        }
        
        // Detach all product_media for this product
        DB::table('product_media')->where('product_id', $product->id)->delete();

        // Attach new data
        if (!empty($syncData)) {
            $product->images()->attach($syncData);
        }
    }
}
