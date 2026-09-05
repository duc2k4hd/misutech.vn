@php
    $selectedBrands = array_filter(explode(',', (string)request('brands', '')));
    $brandList = isset($brands) ? $brands : collect([]);
    $topBrands = $brandList->take(7);
    $remainingBrandsCount = max(0, $brandList->count() - 7);
@endphp

<div class="misutech_filter_top_bar">
    {{-- 1. Mobile Filter Quick Chips (Chỉ hiển thị trên Mobile / Tablet) --}}
    <div class="misutech_filter_chips_bar misutech_mobile_only">
        <button type="button" class="misutech_chip_btn misutech_chip_master" data-open-modal="modal-all-filters" aria-label="Mở tất cả bộ lọc">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" y1="21" x2="4" y2="14"></line>
                <line x1="4" y1="10" x2="4" y2="3"></line>
                <line x1="12" y1="21" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12" y2="3"></line>
                <line x1="20" y1="21" x2="20" y2="16"></line>
                <line x1="20" y1="12" x2="20" y2="3"></line>
                <line x1="1" y1="14" x2="7" y2="14"></line>
                <line x1="9" y1="8" x2="15" y2="8"></line>
                <line x1="17" y1="16" x2="23" y2="16"></line>
            </svg>
            <span>Bộ lọc</span>
            <span class="misutech_chip_badge" id="filter_badge_total" style="display: none;">0</span>
        </button>

        <button type="button" class="misutech_chip_btn" data-open-modal="modal-price" aria-label="Lọc theo giá">
            <span>Giá</span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            <span class="misutech_chip_badge" id="filter_badge_price" style="display: none;">0</span>
        </button>

        <button type="button" class="misutech_chip_btn" data-open-modal="modal-brands" aria-label="Lọc theo thương hiệu">
            <span>Thương hiệu</span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            <span class="misutech_chip_badge" id="filter_badge_brand" style="display: none;">0</span>
        </button>

        <button type="button" class="misutech_chip_btn" data-open-modal="modal-sort" aria-label="Sắp xếp sản phẩm">
            <span>Sắp xếp</span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </button>

        <button type="button" class="misutech_chip_clear_btn" id="misutech_quick_clear_btn" style="display: none;" aria-label="Xóa tất cả bộ lọc">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            <span>Xóa lọc</span>
        </button>
    </div>

    {{-- 2. Dải Thương Hiệu Nổi Bật (Ảnh 4 - Hiển thị trên cả Desktop và Mobile) --}}
    @if($brandList->isNotEmpty())
        <div class="misutech_brand_grid_wrapper">
            <div class="misutech_brand_grid">
                @foreach($topBrands as $b)
                    @php
                        $isSelected = in_array($b->slug, $selectedBrands);
                    @endphp
                    <button type="button" 
                            class="misutech_brand_card_tile {{ $isSelected ? 'is_active' : '' }}" 
                            data-brand-slug="{{ $b->slug }}"
                            title="{{ $b->name }}">
                        @php
                            $logoUrl = '';
                            if (!empty($b->logo)) {
                                if (Str::startsWith($b->logo, ['http://', 'https://'])) {
                                    $logoUrl = $b->logo;
                                } elseif (Str::startsWith($b->logo, 'storage/')) {
                                    $logoUrl = asset($b->logo);
                                } elseif (Str::startsWith($b->logo, 'clients/imgs/brands/')) {
                                    $logoUrl = asset('storage/' . $b->logo);
                                } else {
                                    $logoUrl = asset('storage/clients/imgs/brands/' . $b->logo);
                                }
                            }
                        @endphp
                        @if(!empty($logoUrl))
                            <img src="{{ $logoUrl }}" 
                                 alt="{{ $b->name }}" 
                                 loading="lazy"
                                 class="misutech_brand_logo_img">
                        @else
                            <span class="misutech_brand_logo_text">{{ $b->name }}</span>
                        @endif
                        <span class="misutech_brand_check_icon">✓</span>
                    </button>
                @endforeach

                @if($remainingBrandsCount > 0 || $brandList->count() > 7)
                    <button type="button" class="misutech_brand_more_tile" data-open-modal="modal-brands">
                        <span>Xem thêm{{-- <br>{{ $remainingBrandsCount > 0 ? $remainingBrandsCount : $brandList->count() }} hãng--}}</span> 
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>
