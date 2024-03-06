@php
    use App\Http\Controllers\SuggestionController;
    use Botble\Ecommerce\Models\OffersDetail;
    use Botble\Ecommerce\Models\Offers;
    use Botble\Ecommerce\Models\Product;
    use Botble\Ecommerce\Models\ProductVariation;
    use Botble\Ecommerce\Models\SPC;
    use Botble\Ecommerce\Models\CarouselProducts;
    $layout = theme_option('product_list_layout');

    $requestLayout = request()->input('layout');
    if ($requestLayout && in_array($requestLayout, array_keys(get_product_single_layouts()))) {
        $layout = $requestLayout;
    }

    $layout = $layout && in_array($layout, array_keys(get_product_single_layouts())) ? $layout : 'product-full-width';
    Theme::layout($layout);

    Theme::asset()->usePath()->add('jquery-ui-css', 'css/plugins/jquery-ui.css');
    Theme::asset()->container('footer')->usePath()->add('jquery-ui-js', 'js/plugins/jquery-ui.js');
    Theme::asset()->container('footer')->usePath()->add('jquery-ui-touch-punch-js', 'js/plugins/jquery.ui.touch-punch.min.js');

    $products->loadMissing(['categories', 'categories.slugable']);
    $request = request();
    if (request()->user('customer')) {
        $userid = request()->user('customer')->id;
        if (!CarouselProducts::where('customer_id', $userid)->exists()) {
            $discountedProducts = SuggestionController::getProduct($userid);
        } else {
            $productIds = CarouselProducts::where('customer_id', $userid)->pluck('product_id');
            $discountedProducts = Product::whereIn('id', $productIds)->get();
        }
    }
@endphp
<div class="row">
    @if (auth('customer')->user() !== null &&
            $request->is('products') &&
            $request->query('preferiti_page') == '1' &&
            $request->query('wishlist') == '1')


        <div class="col-lg-12 products-listing position-relative">
            <div class="products-listing position-relative">
                @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.product-items',
                    compact('products'))
            </div>
        </div>
    @else
        <div class="col-12 related-listing">
            @if (request()->user('customer'))

                <div class="row">
                    <center>
                        <h4 class="title-discounted mb-30" style="color:#005BA1; ">
                            <i class="fas fa-circle" style="animation:pulse-blue 2s infinite;border-radius:10px"></i>
                            &nbsp;
                            &nbsp;
                            Pensiamo che questi prodotti potrebbero interessarti
                        </h4>
                    </center>
                    {{-- <div class="owl-carousel owl-theme discounted-carousel "> --}}
                    <div class="owl-carousel owl-theme discounted-carousel ">
                        @foreach ($discountedProducts as $discountedProduct)
                            @include(Theme::getThemeNamespace() .
                                    '::views.ecommerce.includes.cart-related-product-items',
                                ['product' => $discountedProduct]
                            )
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <div class="col-lg-3">
            <!-- <a class="shop-filter-toogle" href="#">
            <span class="fal fa-2x  fa-filter mr-5  ml-0"></span>
            <span class="title fa-2x">{{ __('Filters') }}</span>
            <i class="far fa-2x fa-angle-up  angle-down"></i>
            <i class="far fa-2x fa-angle-down  fa-2x angle-up"></i>
        </a> -->
            <form action="{{ URL::current() }}" method="GET" id="products-filter-ajax">
                @if ($layout != 'product-full-width')
                    <input type="hidden" name="layout" value="{{ $layout }}">
                @endif
                @include(Theme::getThemeNamespace() . '::views/ecommerce/includes/filters')
            </form>
        </div>

        <div class="col-lg-9 products-listing position-relative">
            <div class="products-listing position-relative">
                @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.product-items',
                    compact('products'))
            </div>
        </div>

    @endif

</div>
