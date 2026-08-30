<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Series;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;

class SampleSeriesSeeder extends Seeder
{
    public function run(): void
    {
        $omronBrand = Brand::where('name', 'like', '%Omron%')->first();
        $autonicsBrand = Brand::where('name', 'like', '%Autonics%')->first();
        $sensorCategory = Category::where('name', 'like', '%cảm biến%')->first() ?? Category::first();

        // 1. Tạo Series 1: Omron E3Z-D
        $series1 = Series::updateOrCreate(
            ['slug' => 'omron-e3z-d'],
            [
                'name' => 'Omron E3Z-D',
                'description' => 'Dòng cảm biến quang điện phản xạ khuếch tán chuẩn công nghiệp Omron E3Z-D với khoảng cách phát hiện từ 100mm đến 1m, tích hợp sẵn bộ khuếch đại, độ tin cậy và chống nhiễu vượt trội.',
                'content' => '<h3>Tổng quan dòng sản phẩm Omron E3Z-D</h3>
<p>Dòng <strong>Omron E3Z-D</strong> là tiêu chuẩn toàn cầu cho cảm biến quang điện nhỏ gọn, tích hợp sẵn bộ khuếch đại với các ưu điểm vượt trội:</p>
<ul>
    <li><strong>Chống nhiễu quang học và điện từ hàng đầu:</strong> Khả năng chống ánh sáng môi trường và xung nhiễu từ biến tần, động cơ.</li>
    <li><strong>Cấp độ bảo vệ IP67 & IP69K:</strong> Hoạt động bền bỉ trong môi trường rửa trôi, bụi bẩn công nghiệp nặng.</li>
    <li><strong>Tiêu chuẩn quốc tế:</strong> Đạt chứng nhận CE, UL, RoHS.</li>
    <li><strong>Đa dạng tùy chọn kết nối:</strong> Cáp liền 2m hoặc đầu nối giắc cắm M8.</li>
</ul>',
                'brand_id' => $omronBrand?->id,
                'category_id' => $sensorCategory?->id,
                'sort_order' => 1,
                'status' => 'active',
                'meta_title' => 'Dòng cảm biến quang Omron E3Z-D chính hãng | MISUTECH',
                'meta_description' => 'Phân phối dòng cảm biến quang Omron E3Z-D chính hãng, đầy đủ các model E3Z-D61, E3Z-D62, E3Z-D66, E3Z-D81 giá tốt nhất.',
            ]
        );

        // Các model trong Series Omron E3Z-D
        $omronModels = [
            [
                'name' => 'Cảm biến quang Omron E3Z-D61 2M',
                'slug' => 'omron-e3z-d61-2m',
                'sku' => 'E3Z-D61',
                'price' => 650000,
                'sale_price' => 590000,
                'short_description' => 'Khoảng cách phát hiện 5mm – 100mm (giấy trắng 100x100mm), ngõ ra NPN Open Collector, cáp liền 2m, nguồn cấp 12-24VDC.',
                'content' => '<p><strong>Cảm biến quang Omron E3Z-D61 2M</strong> là model phản xạ khuếch tán tiêu chuẩn:</p>
<table class="table table-bordered">
    <tr><th>Mã sản phẩm</th><td>E3Z-D61 2M</td></tr>
    <tr><th>Hãng sản xuất</th><td>OMRON</td></tr>
    <tr><th>Khoảng cách phát hiện</th><td>5 đến 100 mm</td></tr>
    <tr><th>Ngõ ra điều khiển</th><td>NPN Open Collector (Light-ON / Dark-ON chọn bằng switch)</td></tr>
    <tr><th>Nguồn cấp</th><td>12 đến 24 VDC ±10%</td></tr>
    <tr><th>Kiểu kết nối</th><td>Cáp nối sẵn 2 mét (Pre-wired)</td></tr>
    <tr><th>Cấp bảo vệ</th><td>IEC IP67</td></tr>
</table>',
                'status' => 'active',
                'meta_title' => 'Cảm biến quang Omron E3Z-D61 2M chính hãng',
                'meta_description' => 'Mua cảm biến quang Omron E3Z-D61 2M ngõ ra NPN chính hãng, bảo hành 12 tháng, giao hàng toàn quốc.',
            ],
            [
                'name' => 'Cảm biến quang Omron E3Z-D62 2M',
                'slug' => 'omron-e3z-d62-2m',
                'sku' => 'E3Z-D62',
                'price' => 720000,
                'sale_price' => 680000,
                'short_description' => 'Khoảng cách phát hiện mở rộng lên tới 1m (giấy trắng 300x300mm), ngõ ra NPN, cáp liền 2m.',
                'content' => '<p><strong>Cảm biến quang Omron E3Z-D62 2M</strong> với khoảng cách phát hiện xa đến 1 mét:</p>
<table class="table table-bordered">
    <tr><th>Mã sản phẩm</th><td>E3Z-D62 2M</td></tr>
    <tr><th>Hãng sản xuất</th><td>OMRON</td></tr>
    <tr><th>Khoảng cách phát hiện</th><td>1 mét (1000 mm)</td></tr>
    <tr><th>Ngõ ra điều khiển</th><td>NPN Open Collector</td></tr>
    <tr><th>Nguồn cấp</th><td>12 đến 24 VDC ±10%</td></tr>
    <tr><th>Kiểu kết nối</th><td>Cáp liền 2 mét</td></tr>
    <tr><th>Cấp bảo vệ</th><td>IEC IP67</td></tr>
</table>',
                'status' => 'active',
                'meta_title' => 'Cảm biến quang Omron E3Z-D62 khoảng cách 1m',
                'meta_description' => 'Cảm biến quang điện Omron E3Z-D62 phát hiện khoảng cách xa 1 mét, ngõ ra NPN, chính hãng Omron.',
            ],
            [
                'name' => 'Cảm biến quang Omron E3Z-D66 (Giắc M8)',
                'slug' => 'omron-e3z-d66-giac-m8',
                'sku' => 'E3Z-D66',
                'price' => 780000,
                'sale_price' => null,
                'short_description' => 'Khoảng cách phát hiện 5mm – 100mm, ngõ ra NPN, kiểu kết nối giắc cắm M8 4-pin tháo lắp nhanh.',
                'content' => '<p><strong>Cảm biến quang Omron E3Z-D66</strong> sử dụng đầu nối Connector M8 tiêu chuẩn tiện lợi cho việc thay thế bảo trì:</p>
<table class="table table-bordered">
    <tr><th>Mã sản phẩm</th><td>E3Z-D66</td></tr>
    <tr><th>Hãng sản xuất</th><td>OMRON</td></tr>
    <tr><th>Khoảng cách phát hiện</th><td>5 đến 100 mm</td></tr>
    <tr><th>Ngõ ra điều khiển</th><td>NPN</td></tr>
    <tr><th>Kiểu kết nối</th><td>Giắc cắm M8 4-Pin Connector</td></tr>
    <tr><th>Cấp bảo vệ</th><td>IEC IP67</td></tr>
</table>',
                'status' => 'active',
                'meta_title' => 'Cảm biến quang Omron E3Z-D66 giắc cắm M8',
                'meta_description' => 'Cảm biến quang Omron E3Z-D66 giắc nối M8 chính hãng, tiện lợi thay thế, độ nhạy cao.',
            ],
            [
                'name' => 'Cảm biến quang Omron E3Z-D81 2M (PNP)',
                'slug' => 'omron-e3z-d81-2m-pnp',
                'sku' => 'E3Z-D81',
                'price' => 660000,
                'sale_price' => 610000,
                'short_description' => 'Khoảng cách phát hiện 5mm – 100mm, ngõ ra PNP Open Collector, cáp liền 2m, tương thích chuẩn điều khiển PLC châu Âu.',
                'content' => '<p><strong>Cảm biến quang Omron E3Z-D81 2M</strong> sử dụng ngõ ra PNP:</p>
<table class="table table-bordered">
    <tr><th>Mã sản phẩm</th><td>E3Z-D81 2M</td></tr>
    <tr><th>Hãng sản xuất</th><td>OMRON</td></tr>
    <tr><th>Khoảng cách phát hiện</th><td>5 đến 100 mm</td></tr>
    <tr><th>Ngõ ra điều khiển</th><td>PNP Open Collector</td></tr>
    <tr><th>Nguồn cấp</th><td>12 đến 24 VDC ±10%</td></tr>
    <tr><th>Kiểu kết nối</th><td>Cáp liền 2 mét</td></tr>
    <tr><th>Cấp bảo vệ</th><td>IEC IP67</td></tr>
</table>',
                'status' => 'active',
                'meta_title' => 'Cảm biến quang Omron E3Z-D81 2M ngõ ra PNP',
                'meta_description' => 'Cảm biến quang Omron E3Z-D81 2M ngõ ra PNP chính hãng OMRON, bảo hành 12 tháng.',
            ],
        ];

        foreach ($omronModels as $m) {
            Product::updateOrCreate(
                ['sku' => $m['sku']],
                array_merge($m, [
                    'series_id' => $series1->id,
                    'brand_id' => $omronBrand?->id,
                    'category_id' => $sensorCategory?->id,
                ])
            );
        }

        // 2. Tạo Series 2: Autonics BEN
        $series2 = Series::updateOrCreate(
            ['slug' => 'autonics-ben-series'],
            [
                'name' => 'Autonics BEN Series',
                'description' => 'Dòng cảm biến quang tích hợp nguồn cấp tự do AC/DC (24-240VAC/DC), kích thước nhỏ gọn, khoảng cách phát hiện xa.',
                'content' => '<h3>Tổng quan dòng sản phẩm Autonics BEN</h3>
<p>Dòng <strong>Autonics BEN Series</strong> là loại cảm biến quang đa năng tích hợp sẵn bộ nguồn dải rộng:</p>
<ul>
    <li>Nguồn cấp AC/DC tự do (24-240VAC/DC) hoặc DC (12-24VDC).</li>
    <li>Có công tắc chuyển đổi chế độ Light-ON / Dark-ON.</li>
    <li>Đèn chỉ thị LED trạng thái hoạt động trực quan.</li>
</ul>',
                'brand_id' => $autonicsBrand?->id,
                'category_id' => $sensorCategory?->id,
                'sort_order' => 2,
                'status' => 'active',
                'meta_title' => 'Dòng cảm biến quang Autonics BEN Series chính hãng',
                'meta_description' => 'Cung cấp cảm biến quang Autonics BEN500-DFR, BEN300-DFR nguồn AC/DC chính hãng Autonics.',
            ]
        );

        $autonicsModels = [
            [
                'name' => 'Cảm biến quang Autonics BEN500-DFR',
                'slug' => 'autonics-ben500-dfr',
                'sku' => 'BEN500-DFR',
                'price' => 520000,
                'sale_price' => 480000,
                'short_description' => 'Loại phản xạ khuếch tán, khoảng cách phát hiện 500mm, nguồn cấp tự do 24-240VAC/24-240VDC, ngõ ra Relay.',
                'content' => '<p>Thông số kỹ thuật Autonics BEN500-DFR nguồn AC/DC ngõ ra Relay.</p>',
                'status' => 'active',
                'meta_title' => 'Cảm biến quang Autonics BEN500-DFR chính hãng',
                'meta_description' => 'Autonics BEN500-DFR phản xạ khuếch tán 500mm nguồn 24-240VAC/VDC.',
            ],
            [
                'name' => 'Cảm biến quang Autonics BEN300-DFR',
                'slug' => 'autonics-ben300-dfr',
                'sku' => 'BEN300-DFR',
                'price' => 490000,
                'sale_price' => null,
                'short_description' => 'Loại phản xạ khuếch tán, khoảng cách phát hiện 300mm, nguồn cấp 24-240VAC/DC, ngõ ra tiếp điểm Relay.',
                'content' => '<p>Thông số kỹ thuật Autonics BEN300-DFR nguồn AC/DC ngõ ra Relay.</p>',
                'status' => 'active',
                'meta_title' => 'Cảm biến quang Autonics BEN300-DFR chính hãng',
                'meta_description' => 'Autonics BEN300-DFR phản xạ khuếch tán 300mm nguồn tự do AC/DC.',
            ],
        ];

        foreach ($autonicsModels as $m) {
            Product::updateOrCreate(
                ['sku' => $m['sku']],
                array_merge($m, [
                    'series_id' => $series2->id,
                    'brand_id' => $autonicsBrand?->id,
                    'category_id' => $sensorCategory?->id,
                ])
            );
        }
    }
}
