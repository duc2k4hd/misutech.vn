<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Autonics',
                'meta_title' => 'Autonics - Cảm biến & Bộ điều khiển chính hãng | Misutech',
                'meta_description' => 'Misutech tự hào là nhà cung cấp các thiết bị tự động hóa Autonics chính hãng tại Việt Nam. Phân phối cảm biến, bộ điều khiển nhiệt độ, encoder với giá tốt nhất thị trường.',
                'content' => '<h2>Giới thiệu thương hiệu Autonics</h2>
<p>Autonics là thương hiệu hàng đầu đến từ Hàn Quốc, chuyên cung cấp các giải pháp tự động hóa công nghiệp đáng tin cậy và tiết kiệm chi phí. Các sản phẩm của Autonics được ứng dụng rộng rãi trong mọi lĩnh vực sản xuất từ đóng gói, nhựa, cao su đến công nghiệp nặng.</p>
<h3>Sản phẩm Autonics tại Misutech</h3>
<p>Là nhà cung cấp uy tín, <strong>Misutech</strong> phân phối đầy đủ các dòng sản phẩm của Autonics bao gồm:</p>
<ul>
    <li>Cảm biến quang, cảm biến tiệm cận, cảm biến áp suất.</li>
    <li>Bộ điều khiển nhiệt độ, bộ đếm, bộ định thời.</li>
    <li>Thiết bị điều khiển chuyển động: Động cơ bước, Encoder.</li>
</ul>
<p>Mua Autonics tại Misutech, quý khách hoàn toàn yên tâm về chất lượng chính hãng, dịch vụ hỗ trợ kỹ thuật tận tâm và chính sách bảo hành dài hạn.</p>'
            ],
            [
                'name' => 'Omron',
                'meta_title' => 'Đại lý phân phối thiết bị tự động hóa Omron chính hãng | Misutech',
                'meta_description' => 'Tìm mua thiết bị tự động hóa Omron? Misutech cung cấp PLC, Inverter, Sensor, Relay Omron chính hãng, báo giá dự án nhanh chóng, hỗ trợ kỹ thuật chuyên nghiệp.',
                'content' => '<h2>Thương hiệu Omron - Dẫn đầu công nghệ tự động hóa</h2>
<p>Đến từ Nhật Bản, Omron là tên tuổi lớn trong ngành tự động hóa toàn cầu với các sản phẩm mang tính biểu tượng về độ bền và công nghệ tiên tiến. Triết lý của Omron là mang công nghệ vào cuộc sống để giải quyết các bài toán sản xuất hóc búa nhất.</p>
<h3>Misutech - Đối tác tin cậy cung cấp thiết bị Omron</h3>
<p>Tại <strong>Misutech</strong>, chúng tôi tự hào mang đến cho khách hàng các giải pháp tự động hóa toàn diện từ Omron:</p>
<ul>
    <li>Hệ thống điều khiển: PLC, màn hình HMI, bộ điều khiển nhiệt độ.</li>
    <li>Thiết bị cảm biến: Cảm biến quang, tiệm cận, đo lường chính xác cao.</li>
    <li>Linh kiện chuyển mạch: Relay công nghiệp, công tắc hành trình.</li>
</ul>
<p>Với kho hàng phong phú và đội ngũ kỹ sư giàu kinh nghiệm, Misutech cam kết đáp ứng nhanh chóng mọi nhu cầu thay thế, nâng cấp và triển khai hệ thống Omron của doanh nghiệp bạn.</p>'
            ],
            [
                'name' => 'Panasonic',
                'meta_title' => 'Thiết bị công nghiệp & Tự động hóa Panasonic | Misutech',
                'meta_description' => 'Misutech phân phối cảm biến, Servo motor, PLC mang thương hiệu Panasonic chính hãng. Giải pháp tự động hóa hiệu suất cao, tối ưu chi phí cho nhà máy.',
                'content' => '<h2>Panasonic Industrial Devices - Giải pháp bền bỉ từ Nhật Bản</h2>
<p>Không chỉ nổi tiếng trong mảng điện tử tiêu dùng, Panasonic còn là một thế lực lớn trong ngành tự động hóa công nghiệp. Các sản phẩm công nghiệp của Panasonic nổi tiếng với thiết kế siêu nhỏ gọn, độ chính xác cao và tuổi thọ hoạt động bền bỉ.</p>
<h3>Mua thiết bị Panasonic tại Misutech</h3>
<p><strong>Misutech</strong> cung cấp đa dạng các thiết bị tự động hóa Panasonic phục vụ lắp ráp và chế tạo máy:</p>
<ul>
    <li>Động cơ AC Servo MINAS A6/A5, động cơ bước.</li>
    <li>Cảm biến sợi quang, cảm biến laser độ chính xác cao (Micro-spot).</li>
    <li>PLC cỡ nhỏ mạnh mẽ và màn hình cảm ứng HMI.</li>
</ul>
<p>Đến với Misutech, bạn sẽ nhận được sự tư vấn lựa chọn thiết bị Panasonic tối ưu nhất cho bài toán tự động hóa của mình.</p>'
            ],
            [
                'name' => 'Keyence',
                'meta_title' => 'Thiết bị đo lường & Cảm biến Keyence cao cấp | Misutech',
                'meta_description' => 'Nhà cung cấp giải pháp đo lường, cảm biến và hệ thống Machine Vision Keyence tại Việt Nam. Misutech mang đến công nghệ kiểm tra tự động hàng đầu từ Nhật Bản.',
                'content' => '<h2>Keyence - Đỉnh cao công nghệ đo lường và kiểm tra</h2>
<p>Keyence là thương hiệu đắt giá nhất Nhật Bản, chuyên cung cấp các thiết bị cảm biến, hệ thống kiểm tra ngoại quan (Machine Vision), máy đọc mã vạch và thiết bị đo lường độ chính xác micromet. Các sản phẩm của Keyence thường xuyên đi đầu về công nghệ sáng tạo.</p>
<h3>Misutech phân phối thiết bị Keyence</h3>
<p>Nhằm đáp ứng yêu cầu khắt khe của các nhà máy sản xuất điện tử, ô tô và y tế, <strong>Misutech</strong> cung cấp các thiết bị Keyence chính hãng:</p>
<ul>
    <li>Cảm biến quang điện, cảm biến laser, cảm biến khoảng cách.</li>
    <li>Hệ thống xử lý ảnh công nghiệp (Vision System) & Đầu đọc mã vạch 1D/2D.</li>
    <li>Thiết bị đo lường tĩnh điện và hệ thống khử tĩnh điện (Ionizer).</li>
</ul>
<p>Lựa chọn Misutech là bạn chọn sự an tâm về chất lượng và độ chính xác tuyệt đối mà thiết bị Keyence mang lại.</p>'
            ],
            [
                'name' => 'Mitsubishi',
                'meta_title' => 'Nhà cung cấp thiết bị tự động hóa Mitsubishi Electric | Misutech',
                'meta_description' => 'Misutech cung cấp PLC MELSEC, biến tần Inverter, AC Servo và HMI GOT của thương hiệu Mitsubishi Electric. Hàng chính hãng, giá cạnh tranh nhất thị trường.',
                'content' => '<h2>Mitsubishi Electric - Chuẩn mực tự động hóa nhà máy</h2>
<p>Mitsubishi Electric là biểu tượng của ngành công nghiệp tự động hóa với hệ sinh thái sản phẩm vô cùng phong phú và bền bỉ. Hầu hết các nhà máy sản xuất tại Việt Nam đều đang sử dụng ít nhất một thiết bị của Mitsubishi.</p>
<h3>Giải pháp Mitsubishi từ Misutech</h3>
<p>Là nhà cung cấp thiết bị công nghiệp uy tín, <strong>Misutech</strong> mang đến danh mục sản phẩm Mitsubishi đa dạng:</p>
<ul>
    <li>Bộ điều khiển lập trình PLC (dòng FX, Q, iQ-R series).</li>
    <li>Màn hình giao diện người máy HMI (GOT2000, GOT1000).</li>
    <li>Biến tần (Inverter) FR series và Hệ thống điều khiển Servo MELSERVO.</li>
</ul>
<p>Misutech luôn cam kết cung cấp thiết bị Mitsubishi Electric với nguồn gốc rõ ràng, CO/CQ đầy đủ và chính sách giá tốt nhất cho khách hàng dự án lẫn khách mua lẻ.</p>'
            ],
            [
                'name' => 'Proface',
                'meta_title' => 'Màn hình HMI Proface chính hãng | Misutech',
                'meta_description' => 'Misutech là đối tác phân phối màn hình cảm ứng công nghiệp HMI Proface. Giải pháp giao diện người máy độ bền cao, kết nối đa năng với mọi loại PLC.',
                'content' => '<h2>Proface - Thương hiệu số 1 về màn hình HMI</h2>
<p>Proface (thuộc tập đoàn Schneider Electric) được mệnh danh là chuyên gia toàn cầu về giao diện người máy (Human Machine Interface - HMI) và máy tính công nghiệp (IPC). Với khả năng kết nối dễ dàng với hàng trăm loại PLC khác nhau, Proface luôn là lựa chọn hàng đầu của các kỹ sư lập trình.</p>
<h3>Mua HMI Proface chất lượng tại Misutech</h3>
<p>Tại <strong>Misutech</strong>, chúng tôi hiểu rằng giao diện điều khiển đóng vai trò trọng yếu trong vận hành máy móc. Chúng tôi cung cấp:</p>
<ul>
    <li>Màn hình HMI Proface dòng GP4000, SP5000 tiên tiến.</li>
    <li>Màn hình tích hợp bộ điều khiển (HMI + Control).</li>
    <li>Phần mềm bản quyền GP-Pro EX.</li>
</ul>
<p>Đội ngũ của Misutech luôn sẵn sàng hỗ trợ bạn lựa chọn và nâng cấp các mã màn hình Proface cũ sang thế hệ mới một cách dễ dàng và liền mạch nhất.</p>'
            ],
            [
                'name' => 'Cognex',
                'meta_title' => 'Thiết bị xử lý ảnh & Đọc mã vạch Cognex | Misutech',
                'meta_description' => 'Cung cấp hệ thống Machine Vision và máy đọc mã vạch công nghiệp Cognex. Misutech mang công nghệ kiểm tra bằng hình ảnh của Mỹ đến nhà máy của bạn.',
                'content' => '<h2>Cognex - Mắt thần của tự động hóa công nghiệp</h2>
<p>Có trụ sở tại Hoa Kỳ, Cognex là công ty dẫn đầu thế giới về hệ thống thị giác máy tính (Machine Vision) và đọc mã vạch công nghiệp. Thiết bị của Cognex giúp tự động hóa các khâu kiểm tra lỗi, dẫn hướng robot và truy xuất nguồn gốc sản phẩm với tốc độ cực cao.</p>
<h3>Giải pháp Vision Cognex tại Misutech</h3>
<p>Để nâng cao chất lượng sản phẩm và giảm thiểu lỗi do con người, <strong>Misutech</strong> cung cấp các giải pháp Cognex mạnh mẽ:</p>
<ul>
    <li>Camera thông minh In-Sight xử lý ảnh trực tiếp, không cần PC.</li>
    <li>Máy đọc mã vạch công nghiệp DataMan cho mã DPM, 1D, 2D khó đọc.</li>
    <li>Cảm biến hình ảnh Checker đơn giản, hiệu quả.</li>
</ul>
<p>Với khả năng đọc mọi mã vạch và phân tích ảnh chính xác, Misutech tự tin mang thiết bị Cognex vào giải quyết các bài toán kiểm tra ngoại quan khó nhằn nhất trong nhà máy của bạn.</p>'
            ]
        ];

        foreach ($brands as $brand) {
            $brand['slug'] = Str::slug($brand['name']);
            Brand::updateOrCreate(
                ['slug' => $brand['slug']],
                $brand
            );
        }
    }
}
