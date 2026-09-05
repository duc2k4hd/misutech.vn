@php
    $selectedBrands = array_filter(explode(',', (string)request('brands', '')));
    $selectedPriceRanges = array_filter(explode(',', (string)request('price_ranges', '')));
    $currentSort = request('sort', 'featured');
    $brandList = isset($brands) ? $brands : collect([]);
    $topBrands = $brandList->take(7);
    $remainingBrandsCount = max(0, $brandList->count() - 7);
    
    $priceBrackets = [
        ['range' => '0-2000000', 'label' => 'Dưới 2 triệu'],
        ['range' => '2000000-3000000', 'label' => '2 triệu - 3 triệu'],
        ['range' => '3000000-5000000', 'label' => '3 triệu - 5 triệu'],
        ['range' => '5000000-8000000', 'label' => '5 triệu - 8 triệu'],
        ['range' => '8000000-10000000', 'label' => '8 triệu - 10 triệu'],
        ['range' => '10000000-15000000', 'label' => '10 triệu - 15 triệu'],
        ['range' => '15000000-20000000', 'label' => '15 triệu - 20 triệu'],
        ['range' => '20000000-25000000', 'label' => '20 triệu - 25 triệu'],
        ['range' => '25000000-30000000', 'label' => '25 triệu - 30 triệu'],
        ['range' => '30000000-40000000', 'label' => '30 triệu - 40 triệu'],
        ['range' => '40000000-50000000', 'label' => '40 triệu - 50 triệu'],
        ['range' => '50000000-100000000', 'label' => '50 triệu - 100 triệu'],
        ['range' => '100000000-999999999', 'label' => 'Trên 100 triệu'],
    ];

    $sortOptions = [
        'featured' => 'Nổi bật',
        'price-asc' => 'Giá tăng dần',
        'price-desc' => 'Giá giảm dần',
        'bestseller' => 'Bán chạy',
        'discount' => 'Giảm giá',
        'newest' => 'Mới nhất',
        'name' => 'Theo thứ tự A-Z',
    ];
@endphp

{{-- ========================================================================= --}}
{{-- 1. DESKTOP SIDEBAR FILTER (BÊN TRÁI MÀN HÌNH TO) --}}
{{-- ========================================================================= --}}
<aside class="misutech_home_filters misutech_desktop_sidebar_filters" aria-label="Bộ lọc sản phẩm">
    <h2 class="misutech_home_filter_title">
        <span class="misutech_home_filter_title_icon">☷</span> LỌC THEO
    </h2>
    <button class="misutech_home_clear_filters" id="desktop_clear_filters_btn" type="button">
        ♲ XÓA TẤT CẢ
    </button>

    {{-- Nhóm Lọc Giá --}}
    <section class="misutech_home_filter_group">
        <div class="misutech_home_filter_group_header">
            <h3 class="misutech_home_filter_group_title">⌃ &nbsp; KHOẢNG GIÁ</h3>
            <button class="misutech_home_filter_reset" type="button" data-reset-modal="price">ĐẶT LẠI</button>
        </div>
        <div class="misutech_desktop_price_list">
            @foreach($priceBrackets as $bracket)
                @php
                    $isPriceSelected = in_array($bracket['range'], $selectedPriceRanges);
                @endphp
                <label class="misutech_filter_check_item">
                    <input type="checkbox" 
                           class="misutech_price_cb" 
                           name="price_range[]" 
                           value="{{ $bracket['range'] }}" 
                           {{ $isPriceSelected ? 'checked' : '' }}>
                    <span class="misutech_custom_box"></span>
                    <span class="misutech_check_text">{{ $bracket['label'] }}</span>
                </label>
            @endforeach
        </div>
    </section>

    {{-- Nhóm Lọc Thương Hiệu --}}
    <section class="misutech_home_filter_group">
        <div class="misutech_home_filter_group_header">
            <h3 class="misutech_home_filter_group_title">⌃ &nbsp; THƯƠNG HIỆU</h3>
            <button class="misutech_home_filter_reset" type="button" data-reset-modal="brands">ĐẶT LẠI</button>
        </div>
        
        <div class="misutech_sidebar_brand_search">
            <input type="text" 
                   class="misutech_brand_search_input" 
                   placeholder="Tìm thương hiệu..." 
                   aria-label="Tìm thương hiệu">
        </div>

        <div class="misutech_desktop_brand_list" data-searchable-brand-grid>
            @foreach ($brandList as $brand)
                @php
                    $isSelected = in_array($brand->slug, $selectedBrands);
                @endphp
                <label class="misutech_filter_check_item misutech_brand_check_item" data-brand-name="{{ Str::lower($brand->name) }}">
                    <input type="checkbox" 
                           class="misutech_brand_cb" 
                           name="brand[]"
                           value="{{ $brand->slug }}" 
                           {{ $isSelected ? 'checked' : '' }}>
                    <span class="misutech_custom_box"></span>
                    <span class="misutech_check_text">
                        {{ $brand->name }}
                        @if(isset($brand->products_count))
                            <span class="misutech_item_count">({{ $brand->products_count }})</span>
                        @endif
                    </span>
                </label>
            @endforeach
        </div>
    </section>
</aside>

{{-- ========================================================================= --}}
{{-- MODALS BACKDROP OVERLAY & SLIDE-UP POPUPS --}}
{{-- ========================================================================= --}}
<div class="misutech_filter_backdrop" id="misutech_filter_backdrop"></div>

{{-- MODAL 1: BỘ LỌC THEO GIÁ (Ảnh 2) --}}
<div class="misutech_filter_modal" id="modal-price" role="dialog" aria-modal="true" aria-labelledby="modal_price_title">
    <div class="misutech_modal_header">
        <h3 class="misutech_modal_title" id="modal_price_title">Bộ lọc theo giá</h3>
        <button type="button" class="misutech_modal_close" data-close-modal aria-label="Đóng popup">✕</button>
    </div>

    <div class="misutech_modal_body">
        <div class="misutech_modal_section">
            <h4 class="misutech_modal_section_title">Sắp xếp theo</h4>
            <div class="misutech_sort_chips_grid">
                @foreach($sortOptions as $key => $label)
                    <button type="button" 
                            class="misutech_sort_chip {{ $currentSort === $key ? 'is_active' : '' }}" 
                            data-sort-value="{{ $key }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="misutech_modal_section">
            <h4 class="misutech_modal_section_title">Giá</h4>
            <div class="misutech_price_checkbox_grid">
                @foreach($priceBrackets as $bracket)
                    @php
                        $isPriceSelected = in_array($bracket['range'], $selectedPriceRanges);
                    @endphp
                    <label class="misutech_filter_check_item">
                        <input type="checkbox" 
                               class="misutech_price_cb" 
                               name="price_range[]" 
                               value="{{ $bracket['range'] }}" 
                               {{ $isPriceSelected ? 'checked' : '' }}>
                        <span class="misutech_custom_box"></span>
                        <span class="misutech_check_text">{{ $bracket['label'] }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    <div class="misutech_modal_footer">
        <button type="button" class="misutech_btn_modal_reset" data-reset-modal="price">Bỏ chọn</button>
        <button type="button" class="misutech_btn_modal_apply" data-apply-modal>
            Xem <span class="misutech_live_count">{{ $totalProducts ?? 0 }}</span> kết quả
        </button>
    </div>
</div>

{{-- MODAL 2: BỘ LỌC NÂNG CAO (Ảnh 3) --}}
<div class="misutech_filter_modal" id="modal-all-filters" role="dialog" aria-modal="true" aria-labelledby="modal_all_title">
    <div class="misutech_modal_header">
        <h3 class="misutech_modal_title" id="modal_all_title">Bộ lọc nâng cao</h3>
        <button type="button" class="misutech_modal_close" data-close-modal aria-label="Đóng popup">✕</button>
    </div>

    <div class="misutech_modal_body">
        <div class="misutech_modal_section">
            <h4 class="misutech_modal_section_title">Thương hiệu</h4>
            <div class="misutech_modal_search_box">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" 
                       class="misutech_brand_search_input" 
                       placeholder="Tìm thương hiệu" 
                       aria-label="Tìm thương hiệu trong bộ lọc">
            </div>

            <div class="misutech_brand_checkbox_grid" data-searchable-brand-grid>
                @foreach($brandList as $b)
                    @php
                        $isSelected = in_array($b->slug, $selectedBrands);
                    @endphp
                    <label class="misutech_filter_check_item misutech_brand_check_item" data-brand-name="{{ Str::lower($b->name) }}">
                        <input type="checkbox" 
                               class="misutech_brand_cb" 
                               name="brand[]" 
                               value="{{ $b->slug }}" 
                               {{ $isSelected ? 'checked' : '' }}>
                        <span class="misutech_custom_box"></span>
                        <span class="misutech_check_text">
                            {{ $b->name }}
                            @if(isset($b->products_count))
                                <span class="misutech_item_count">({{ $b->products_count }})</span>
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="misutech_modal_section">
            <h4 class="misutech_modal_section_title">Giá</h4>
            <div class="misutech_price_checkbox_grid">
                @foreach($priceBrackets as $bracket)
                    @php
                        $isPriceSelected = in_array($bracket['range'], $selectedPriceRanges);
                    @endphp
                    <label class="misutech_filter_check_item">
                        <input type="checkbox" 
                               class="misutech_price_cb" 
                               name="price_range[]" 
                               value="{{ $bracket['range'] }}" 
                               {{ $isPriceSelected ? 'checked' : '' }}>
                        <span class="misutech_custom_box"></span>
                        <span class="misutech_check_text">{{ $bracket['label'] }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="misutech_modal_section">
            <h4 class="misutech_modal_section_title">Sắp xếp theo</h4>
            <div class="misutech_sort_chips_grid">
                @foreach($sortOptions as $key => $label)
                    <button type="button" 
                            class="misutech_sort_chip {{ $currentSort === $key ? 'is_active' : '' }}" 
                            data-sort-value="{{ $key }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="misutech_modal_footer">
        <button type="button" class="misutech_btn_modal_reset" data-reset-modal="all">Bỏ chọn</button>
        <button type="button" class="misutech_btn_modal_apply" data-apply-modal>
            Xem <span class="misutech_live_count">{{ $totalProducts ?? 0 }}</span> kết quả
        </button>
    </div>
</div>

{{-- MODAL 3: BỘ LỌC THƯƠNG HIỆU (Ảnh 5) --}}
<div class="misutech_filter_modal" id="modal-brands" role="dialog" aria-modal="true" aria-labelledby="modal_brands_title">
    <div class="misutech_modal_header">
        <h3 class="misutech_modal_title" id="modal_brands_title">Bộ lọc Thương hiệu</h3>
        <button type="button" class="misutech_modal_close" data-close-modal aria-label="Đóng popup">✕</button>
    </div>

    <div class="misutech_modal_body">
        <div class="misutech_modal_search_box" style="margin-bottom: 16px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" 
                   class="misutech_brand_search_input" 
                   placeholder="Tìm thương hiệu" 
                   aria-label="Tìm thương hiệu">
        </div>

        <div class="misutech_brand_checkbox_grid" data-searchable-brand-grid>
            @foreach($brandList as $b)
                @php
                    $isSelected = in_array($b->slug, $selectedBrands);
                @endphp
                <label class="misutech_filter_check_item misutech_brand_check_item" data-brand-name="{{ Str::lower($b->name) }}">
                    <input type="checkbox" 
                           class="misutech_brand_cb" 
                           name="brand[]" 
                           value="{{ $b->slug }}" 
                           {{ $isSelected ? 'checked' : '' }}>
                    <span class="misutech_custom_box"></span>
                    <span class="misutech_check_text">
                        {{ $b->name }}
                        @if(isset($b->products_count))
                            <span class="misutech_item_count">({{ $b->products_count }})</span>
                        @endif
                    </span>
                </label>
            @endforeach
        </div>
    </div>

    <div class="misutech_modal_footer">
        <button type="button" class="misutech_btn_modal_reset" data-reset-modal="brands">Bỏ chọn</button>
        <button type="button" class="misutech_btn_modal_apply" data-apply-modal>
            Xem <span class="misutech_live_count">{{ $totalProducts ?? 0 }}</span> kết quả
        </button>
    </div>
</div>

{{-- MODAL 4: SẮP XẾP SẢN PHẨM NHANH --}}
<div class="misutech_filter_modal misutech_modal_small" id="modal-sort" role="dialog" aria-modal="true" aria-labelledby="modal_sort_title">
    <div class="misutech_modal_header">
        <h3 class="misutech_modal_title" id="modal_sort_title">Sắp xếp theo</h3>
        <button type="button" class="misutech_modal_close" data-close-modal aria-label="Đóng popup">✕</button>
    </div>

    <div class="misutech_modal_body">
        <div class="misutech_sort_list">
            @foreach($sortOptions as $key => $label)
                <button type="button" 
                        class="misutech_sort_list_item {{ $currentSort === $key ? 'is_active' : '' }}" 
                        data-sort-value="{{ $key }}">
                    <span>{{ $label }}</span>
                    @if($currentSort === $key)
                        <span class="misutech_sort_check">✓</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
</div>
