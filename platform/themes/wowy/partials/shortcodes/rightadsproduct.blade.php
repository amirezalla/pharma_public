@php
    $products=Botble\Ecommerce\Models\Product::all();
@endphp

<div class="container">
    <div class="row">
        <div class="col-lg-9">
            @if ($category)
{{--                <section class="bg-grey-9 ">--}}
                    <product-category-products-component :category="{{ json_encode($category) }}"  url="{{ route('public.ajax.product-category-products') }}" all="{{ $category->url }}"></product-category-products-component>
{{--                </section>--}}
            @endif
            {{--            <div class="row">--}}
            {{--                --}}
            {{--                @foreach($products as $product)--}}
            {{--                    <div class="col-4">--}}
            {{--                        @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.product-item', compact('product'))--}}
            {{--                    </div>--}}
            {{--                @endforeach--}}
            {{--                --}}
            {{--            </div>--}}

        </div>
        <div class="col-lg-3">
            <div class="banner-img wow fadeIn animated ">
                <img class="border-radius-10" src="{{ RvMedia::getImageUrl($shortcode->icon) }}" alt="">
                <div class="banner-text">
                    <span>{!! BaseHelper::clean($shortcode->{'title' }) !!}</span>
                    <h4>{!! BaseHelper::clean($shortcode->{'subtitle'}) !!}</h4>
                    {{--            <a href="{{ route('public.ads-click', $ads->key) }}">--}}
                    {{--                {{ $ads->getMetaData('button_text', true) ?: __('Shop Now') }} <i class="fa fa-arrow-right"></i>--}}
                    {{--            </a>--}}
                </div>
            </div>
        </div>

    </div>

</div>

