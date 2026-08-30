<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = 'C:\Users\ASUS\.gemini\antigravity-ide\brain\857a1731-9235-4bfa-9e6f-120790719433\scratch\categories_utf8.json';
        
        if (!file_exists($jsonPath)) {
            $this->command->error("File JSON không tồn tại!");
            return;
        }

        $json = file_get_contents($jsonPath);
        $data = json_decode($json, true);

        if (!$data) {
            $this->command->error("Lỗi khi đọc file JSON!");
            return;
        }

        // Bước 1: Thu thập tất cả các Parent Slug bị thiếu để tạo Parent ảo
        $allSlugs = array_column($data, 'Slug');
        $missingParents = [];

        foreach ($data as $item) {
            $parentSlug = trim($item['Parent Slug'] ?? '');
            if (!empty($parentSlug) && !in_array($parentSlug, $allSlugs) && !isset($missingParents[$parentSlug])) {
                $missingParents[$parentSlug] = [
                    'Name' => ucwords(str_replace('-', ' ', $parentSlug)),
                    'Slug' => $parentSlug,
                    'Parent Slug' => ''
                ];
            }
        }

        // Gộp Parent bị thiếu vào danh sách Data
        $data = array_merge(array_values($missingParents), $data);

        // Bước 2: Tạo tất cả các danh mục trước (để lấy ID)
        $this->command->info("Đang tạo " . count($data) . " danh mục...");
        foreach ($data as $item) {
            $slug = trim($item['Slug']);
            if (empty($slug)) continue;

            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => trim($item['Name']),
                    'type' => 'product', // Mặc định là danh mục sản phẩm
                    'status' => 'active'
                ]
            );
        }

        // Bước 3: Cập nhật Parent ID
        $this->command->info("Đang cập nhật phân cấp Parent-Child...");
        foreach ($data as $item) {
            $slug = trim($item['Slug']);
            $parentSlug = trim($item['Parent Slug'] ?? '');

            if (empty($slug) || empty($parentSlug)) continue;

            $parent = Category::where('slug', $parentSlug)->first();
            $child = Category::where('slug', $slug)->first();

            if ($parent && $child) {
                $child->parent_id = $parent->id;
                $child->save();
            }
        }

        $this->command->info("Hoàn tất tạo Danh mục!");
    }
}
