@php
    Theme::asset()->usePath()->add('custom-scrollbar-css', 'plugins/mcustom-scrollbar/jquery.mCustomScrollbar.css');
    Theme::asset()
        ->container('footer')
        ->usePath()
        ->add('custom-scrollbar-js', 'plugins/mcustom-scrollbar/jquery.mCustomScrollbar.js', ['jquery']);

    if (!isset($categories)) {
        $categories = ProductCategoryHelper::getActiveTreeCategories();

        if (Route::currentRouteName() != 'public.products' && request()->input('categories', [])) {
            $categories = $categories->whereIn('id', (array) request()->input('categories', []));
        }
    }

@endphp

<div class="shop-product-filter-header">
    <div class="row">
        <div class="search-style-2 mb-4">
            <div class="row">
                <div class="col-12">
                    <form action="https://marigopharma.marigo.collaudo.biz/product-categories/prodotti-consumabili" method="get"
                        id='queryonsearch'>
                        <strong style="color: #005ba1;font-size: 16px;font-weight: 700;display: block;"
                            class="mb-1">Prodotto</strong>
                        <input autofocus style="width:100% !important;border-radius: 5px;font-size: smaller;"
                            type="text" placeholder="Scrivi codice o nome prodotto..." id='search-consumabili'
                            name="q" autocomplete="off" style="border-radius:26px !important;"
                            @if (request()->input('q') !== null) value={{ request()->input('q') }} @endif>
                    </form>
                </div>
            </div>
        </div>
        @if (request()->user('customer'))
            <strong style="color: #005ba1;font-size: 16px;font-weight: 700;display: block;" class="mb-1">
                Mostra solo
            </strong>

            <input type="hidden" name="userid" id= "user_id" value={{ request()->user('customer')->id }}>
            <div class="col-12 mb-5 widget-filter-item">
                <div class="row"
                    style="border: 1px solid #F5F5F5;border-right: 0;border-left: 0;border-top: 0; margin-top:5px">
                    <div class="col-1" style="align-self: center">
                        <input class="wishlist-check form-check-input" type="checkbox" id='wishlist' name='wishlist'
                            value="1" @if (request()->input('wishlist') == 1) checked @endif>
                    </div>
                    <div class="col-10">
                        <label class="wishlist-check form-check-label"><span class="d-inline-block">I miei
                                preferiti</span></label>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-5 widget-filter-item">
                <div class="row"
                    style="border: 1px solid #F5F5F5;border-right: 0;border-left: 0;border-top: 0; margin-top:5px">
                    <div class="col-1" style="align-self: center">
                        <input class="discounted-check form-check-input" type="checkbox" id='discounted'
                            name='discounted' value=1 @if (request()->input('discounted') == 1) checked @endif>
                    </div>
                    <div class="col-10">
                        <label class="discounted-check form-check-label"><span class="d-inline-block">Prodotti in
                                offerta</span></label>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-5 widget-filter-item">
                <div class="row"
                    style="border: 1px solid #F5F5F5;border-right: 0;border-left: 0;border-top: 0; margin-top:5px">
                    <div class="col-1" style="align-self: center">
                        <input class="recenti-check form-check-input" type="checkbox" id='recenti' name='recenti'
                            value=1 @if (request()->input('recenti') == 1) checked @endif>
                    </div>
                    <div class="col-10">
                        <label class="recenti-check form-check-label"><span class="d-inline-block">Acquistati di
                                recente</span></label>
                    </div>
                </div>
            </div>
        @endif
        @if ($categories)
            <div class="col-lg-12 mb-4 widget-filter-item">
                @php

                    $categories = $categories->sortBy(function ($category) {
                        return strtolower($category['name']); // Use array access if your collection contains arrays
                    });

                @endphp



                <div class="col-12 mb-5 widget-filter-item">
                    <div class="accordion my-2" id="categoryAccordionWrapper ">
                        <strong style="color: #005ba1;font-size: 16px;font-weight: 700;display: block;"
                            class="mb-1">Categorie</strong>
                        <div class="accordion-item">

                            <div class="accordion-header " id="headingOne">
                                <button class="accordion-button py-2 px-4" id="accordion-button" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true"
                                    aria-controls="collapseTwo" style="background-color: white;color:black;">
                                    Categorie
                                </button>
                            </div>
                            <div id="collapseTwo" class="accordion-collapse collapse " aria-labelledby="headingOne">
                                <div class="accordion-body category-place">
                                    @foreach ($categories as $category)
                                        <div class="row"
                                            style="border: 1px solid #F5F5F5;border-right: 0;border-left: 0;border-top: 0; margin-top:5px">
                                            <div class="col-1" style="align-self: center">
                                                <input class="category-check cat-che form-check-input"
                                                    name="categories[]" type="checkbox"
                                                    id="category-filter-{{ $category->id }}" value="{{ $category->id }}"
                                                    @if (in_array($category->id, request()->input('categories', []))) checked @endif>
                                            </div>
                                            <div class="col-10">
                                                <label class=" category-check-labels form-check-label"
                                                    for="category-filter-{{ $category->id }}"><span
                                                        class="d-inline-block">{{ $category->name }}</span> </label>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @endif

        @php
            use Botble\Ecommerce\Models\Product;
            use Botble\Ecommerce\Models\Brand;

            // Retrieve all unique brand IDs from products
            $brandIds = Product::where('status', \Botble\Base\Enums\BaseStatusEnum::PUBLISHED)
                ->pluck('brand_id')
                ->unique();

            // Retrieve brands that are used in products
            if (!isset($brands)) {
                $brands = Brand::whereIn('id', $brandIds)->get();
            }
        @endphp
        <div class="col-12 mb-5 widget-filter-item">
            <div class="accordion my-2" id="brandsAccordionWrapper ">
                <strong style="color: #005ba1;font-size: 16px;font-weight: 700;display: block;"
                    class="mb-1">Produttore</strong>
                <div class="accordion-item">

                    <div class="accordion-header " id="headingOne">
                        <button class="accordion-button py-2 px-4" id="accordion-button" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true"
                            aria-controls="collapseOne" style="background-color: white;color:black;">
                            Produttore
                        </button>
                    </div>
                    <div id="collapseOne" class="accordion-collapse collapse " aria-labelledby="headingOne">
                        <div class="accordion-body brands-place">
                            @foreach ($brands as $brand)
                                <div class="row"
                                    style="border: 1px solid #F5F5F5;border-right: 0;border-left: 0;border-top: 0; margin-top:5px">
                                    <div class="col-1" style="align-self: center">
                                        <input class="brands-check form-check-input" name="brands[]" type="checkbox"
                                            id="brand-filter-{{ $brand->id }}" value="{{ $brand->id }}"
                                            @if (in_array($brand->id, request()->input('brands', []))) checked @endif>
                                    </div>
                                    <div class="col-10">
                                        <label class=" brands-check-label form-check-label"
                                            for="brand-filter-{{ $brand->id }}"><span
                                                class="d-inline-block">{{ $brand->name }}</span> </label>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>

            </div>
        </div>

        @php
            $tags = app(\Botble\Ecommerce\Repositories\Interfaces\ProductTagInterface::class)->advancedGet([
                'condition' => ['status' => \Botble\Base\Enums\BaseStatusEnum::PUBLISHED],
                'withCount' => ['products'],
                'order_by' => ['products_count' => 'desc'],
                'take' => 20,
            ]);
        @endphp
        {{--        @if (count($tags) > 0) --}}
        {{--            <div class="col-lg-3 col-md-4 mb-lg-0 mb-md-5 mb-sm-5 widget-filter-item"> --}}
        {{--                <h5 class="mb-20 widget__title" data-title="{{ __('Tag') }}">{{ __('By :name', ['name' => __('tags')]) }}</h5> --}}
        {{--                <div class="custome-checkbox"> --}}
        {{--                    @foreach ($tags as $tag) --}}
        {{--                        <input class="form-check-input" --}}
        {{--                               name="tags[]" --}}
        {{--                               type="checkbox" --}}
        {{--                               id="tag-filter-{{ $tag->id }}" --}}
        {{--                               value="{{ $tag->id }}" --}}
        {{--                               @if (in_array($tag->id, request()->input('tags', []))) checked @endif> --}}
        {{--                        <label class="form-check-label" for="tag-filter-{{ $tag->id }}"><span class="d-inline-block">{{ $tag->name }}</span> <span class="d-inline-block">({{ $tag->products_count }})</span> </label> --}}
        {{--                        <br> --}}
        {{--                    @endforeach --}}
        {{--                </div> --}}
        {{--            </div> --}}
        {{--        @endif --}}

        {{-- <div class="col-lg-12 widget-filter-item mt-10" data-type="price">
            <strong style="color: #005ba1;font-size: 16px;font-weight: 700;display: block;" class="mb-1">Prezzo
            </strong>
            <div class="container">
                <div class="price-filter range">
                    <div class="price-filter-inner">
                        <div class="slider-range"></div>
                        <input type="hidden" class="min_price min-range" name="min_price"
                            value="{{ request()->input('min_price', 0) }}" data-min="0"
                            data-label="{{ __('Min price') }}" />
                        <input type="hidden" class="min_price max-range" name="max_price"
                            value="{{ request()->input('max_price', (int) theme_option('max_filter_price', 5) * get_current_exchange_rate()) }}"
                            data-max="{{ (int) theme_option('max_filter_price', 5) * get_current_exchange_rate() }}"
                            data-label="{{ __('Max price') }}" />
                        <div class="price_slider_amount">
                            <div class="label-input">
                                <span class="d-inline-block"> Tra </span>
                                <span class="from d-inline-block"></span>
                                <span class="to d-inline-block"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div> --}}
        {{--            <div class="row"> --}}
        {{--                {!! render_product_swatches_filter([ --}}
        {{--                    'view' => Theme::getThemeNamespace() . '::views.ecommerce.attributes.attributes-filter-renderer' --}}
        {{--                ]) !!} --}}
        {{--            </div> --}}
    </div>

    {{--    <a class="show-advanced-filters" href="#"> --}}
    {{--        <span class="title">{{ __('Advanced filters') }}</span> --}}
    {{--        <i class="far fa-angle-up angle-down"></i> --}}
    {{--        <i class="far fa-angle-down angle-up"></i> --}}
    {{--    </a> --}}

    {{--    <div class="advanced-search-widgets" style="display: none"> --}}
    {{--        <div class="row"> --}}
    {{--            {!! render_product_swatches_filter([ --}}
    {{--                'view' => Theme::getThemeNamespace() . '::views.ecommerce.attributes.attributes-filter-renderer' --}}
    {{--            ]) !!} --}}
    {{--        </div> --}}
    {{--    </div> --}}

    <div class="widget mt-3 row">
        <div class="container">
            <a class="btn btn-danger col-12" style="font-size: small; border-radius: 25px; text-transform: none;"
                href="/products">{{ __('Cancella filtri') }}</a>
        </div>
    </div>
</div>
