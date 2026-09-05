        <footer class="misutech_home_footer">
            <div class="misutech_home_footer_top">
                <div class="misutech_home_container">
                    <div class="misutech_home_footer_logo_wrap">
                        <a href="{{ route('home.index') }}" aria-label="Trang chủ {{ $settings->company ?? ($settings->name ?? 'MISUTECH') }}">
                            <img src="{{ !empty($settings->site_logo) ? (Str::startsWith($settings->site_logo, ['http://', 'https://']) ? $settings->site_logo : asset('storage/clients/imgs/settings/' . $settings->site_logo)) : asset('storage/clients/imgs/settings/logo-misutech.png') }}" alt="{{ $settings->company ?? ($settings->name ?? 'MISUTECH') }}" class="misutech_home_footer_logo_img" width="180" height="45" loading="lazy" decoding="async">
                        </a>
                    </div>
                    <div class="misutech_home_footer_top_grid">
                    <div class="misutech_home_footer_col">
                        <h3 class="misutech_home_footer_col_title">CHĂM SÓC KHÁCH HÀNG</h3>
                        <ul class="misutech_home_footer_col_list">
                            <li><a href="#">Hệ thống cửa hàng</a></li>
                            <li><a href="#">Chính sách đổi trả</a></li>
                            <li><a href="#">Theo dõi đơn hàng</a></li>
                            <li><a href="#">Bảo hành sản phẩm</a></li>
                            <li><a href="#">Catalogue tự động hóa</a></li>
                            <li><a href="#">Câu hỏi thường gặp</a></li>
                        </ul>
                    </div>
                    <div class="misutech_home_footer_col">
                        <h3 class="misutech_home_footer_col_title">THÔNG TIN CÔNG TY</h3>
                        <ul class="misutech_home_footer_col_list">
                            <li><a href="#">Chúng tôi là ai?</a></li>
                            <li><a href="#">Tầm nhìn & Sứ mệnh</a></li>
                            <li><a href="#">Đối tác chiến lược</a></li>
                            <li><a href="#">Cơ hội nghề nghiệp</a></li>
                            <li><a href="#">Chính sách bảo mật</a></li>
                            <li><a href="#">Điều khoản sử dụng</a></li>
                        </ul>
                    </div>
                    <div class="misutech_home_footer_col">
                        <h3 class="misutech_home_footer_col_title">TÀI KHOẢN CỦA TÔI</h3>
                        <ul class="misutech_home_footer_col_list">
                            <li><a href="#">Sơ đồ trang web</a></li>
                            <li><a href="#">Chính sách bảo mật</a></li>
                            <li><a href="#">Tài khoản của tôi</a></li>
                            <li><a href="{{ route('shop.index') }}">Tìm kiếm nâng cao</a></li>
                            <li><a href="{{ route('contact.index') }}">Liên hệ</a></li>
                            <li><a href="#">Sản phẩm yêu thích</a></li>
                        </ul>
                    </div>
                    <div class="misutech_home_footer_col misutech_home_footer_col_hours">
                        <h3 class="misutech_home_footer_col_title">GIỜ MỞ CỬA</h3>
                        <p class="misutech_home_footer_text">
                            Chào mừng bạn đến với MISUTECH.<br>
                            Chúng tôi luôn sẵn sàng hỗ trợ bạn một cách tốt nhất.
                        </p>
                        <p class="misutech_home_footer_text">
                            <strong>Thứ 2 đến Thứ 6 :</strong> 8:00 Sáng - 8:00 Tối<br>
                            <strong>Thứ 7 :</strong> 7:30 Sáng - 9:30 Tối<br>
                            <strong>Chủ nhật :</strong> 7:00 Sáng - 10:00 Tối
                        </p>
                    </div>
                    <div class="misutech_home_footer_col misutech_home_footer_col_contact">
                        <h3 class="misutech_home_footer_col_title">LIÊN HỆ VỚI CHÚNG TÔI</h3>
                        <div class="misutech_home_footer_contact_item">
                            <span class="misutech_home_footer_contact_icon">🏠</span>
                            <div>
                                <strong>ĐỊA CHỈ:</strong> {{ $settings->address ?? 'Số 252 Đường Đại Thắng, Tổ 4, Phường Dương Kinh, Thành phố Hải Phòng, Việt Nam' }}
                            </div>
                        </div>
                        <div class="misutech_home_footer_contact_item">
                            <span class="misutech_home_footer_contact_icon">📞</span>
                            <div>
                                <strong>HOTLINE:</strong> {{ $settings->hotline ?? '0866.555.212' }}
                            </div>
                        </div>
                        <div class="misutech_home_footer_contact_item">
                            <span class="misutech_home_footer_contact_icon">✉</span>
                            <div>
                                <strong>EMAIL:</strong> {{ $settings->email ?? 'kinhdoanhhpt@haiphongtech.vn' }}
                            </div>
                        </div>
                        <div class="misutech_home_footer_socials">
                            <a href="{{ $settings->facebook ?? ($settings->facebook_url ?? '#') }}" target="_blank" rel="noopener noreferrer" class="misutech_home_footer_social_btn" aria-label="Fanpage Facebook Misutech">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path fill="currentColor" d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/></svg>
                            </a>
                            <a href="{{ $settings->instagram ?? ($settings->instagram_url ?? '#') }}" target="_blank" rel="noopener noreferrer" class="misutech_home_footer_social_btn" aria-label="Instagram Misutech">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path fill="currentColor" d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"/></svg>
                            </a>
                            <a href="{{ $settings->twitter ?? ($settings->twitter_url ?? ($settings->x_url ?? '#')) }}" target="_blank" rel="noopener noreferrer" class="misutech_home_footer_social_btn" aria-label="X Misutech">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path fill="currentColor" d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865l8.875 11.633Z"/></svg>
                            </a>
                            <a href="{{ $settings->tiktok ?? ($settings->tiktok_url ?? '#') }}" target="_blank" rel="noopener noreferrer" class="misutech_home_footer_social_btn" aria-label="TikTok Misutech">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path fill="currentColor" d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3V0Z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <div class="misutech_home_footer_middle_1">
                <div class="misutech_home_container misutech_home_footer_newsletter_wrap">
                    <div class="misutech_home_footer_payments">
                        <span class="misutech_home_payment_icon misutech_home_payment_visa">VISA</span>
                        <span class="misutech_home_payment_icon misutech_home_payment_mastercard">MasterCard</span>
                        <span class="misutech_home_payment_icon misutech_home_payment_paypal">PayPal</span>
                        <span class="misutech_home_payment_icon misutech_home_payment_amex">AMEX</span>
                        <span class="misutech_home_payment_icon misutech_home_payment_cirrus">Cirrus</span>
                        <span class="misutech_home_payment_icon misutech_home_payment_skrill">Skrill</span>
                    </div>
                    <div class="misutech_home_footer_newsletter">
                        <div class="misutech_home_footer_newsletter_text">
                            <h3>ĐĂNG KÝ NHẬN BẢN TIN</h3>
                            <p>Nhận các ưu đãi đặc biệt, khuyến mãi và sản phẩm độc quyền qua email</p>
                        </div>
                        <form class="misutech_home_footer_newsletter_form">
                            <input type="email" placeholder="Nhập email của bạn" required>
                            <button type="submit">ĐĂNG KÝ</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="misutech_home_footer_middle_2">
                <div class="misutech_home_container misutech_home_footer_tags_wrap">
                    <div class="misutech_home_footer_tags">
                        <strong>TỪ KHÓA NỔI BẬT :</strong>
                        @foreach($allCategories as $category)
                            <a href="{{ route('categories.show', $category->slug) }}">{{ $category->name }}</a>
                            @if(!$loop->last) | @endif
                        @endforeach
                    </div>
                    <div class="misutech_home_footer_apps">
                        <a href="#" class="misutech_home_app_badge" aria-label="Tải ứng dụng Misutech trên App Store">
                            <svg viewBox="0 0 512 512" width="120" height="35"><rect width="512" height="512" fill="#000" rx="40"/><path fill="#fff" d="M150 140v230l190-115-190-115zm30 50l100 65-100 65v-130z"/></svg>
                        </a>
                        <a href="#" class="misutech_home_app_badge" aria-label="Tải ứng dụng Misutech trên Google Play">
                            <svg viewBox="0 0 512 512" width="120" height="35"><rect width="512" height="512" fill="#000" rx="40"/><path fill="#fff" d="M256 120c-75 0-136 61-136 136s61 136 136 136 136-61 136-136-61-136-136-136zm0 230c-51 0-94-43-94-94s43-94 94-94 94 43 94 94-43 94-94 94z"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="misutech_home_footer_bottom">
                <div class="misutech_home_container misutech_home_footer_bottom_wrap">
                    <div class="misutech_home_footer_copy">
                        <img width="40" height="40" src="{{ asset('storage/clients/imgs/settings/'. (!empty($settings->site_favicon) ? $settings->site_favicon : 'favicon-misutech.png')) }}" alt="DMCA" loading="lazy" decoding="async">
                        {!! !empty($settings->copyright) ? Blade::render($settings->copyright) : ('© ' . date('Y') . ' ' . (!empty($settings->name) ? $settings->name : 'MISUTECH') . '. Bảo lưu mọi quyền.') !!}
                    </div>
                    <div class="misutech_home_footer_trust">
                        <img width="100" height="35" src="{{ asset('storage/clients/imgs/others/TRUSTe.avif') }}" alt="TRUSTe Verified" loading="lazy" decoding="async">
                    </div>
                </div>
            </div>
        </footer>
