{{--<section class="product-tabs pt-40 pb-30 wow fadeIn animated">--}}
{{--    --}}
{{--    <product-collections-component title="{!! BaseHelper::clean($title) !!}" :product_collections="{{ json_encode($productCollections) }}" url="{{ route('public.ajax.products') }}"></product-collections-component>--}}

{{--</section>--}}
@php
    $products=Botble\Ecommerce\Models\Product::latest()->take(6)->get();
@endphp


<div class="container mt-5">
    <h3 class="ps-section__title" style="
    font-size: 30px;
    text-align: center;
    margin-bottom: 26px;
    margin-top: 92px;
    font-weight: 600;">Gli ultimi prodotti</h3>
</div>
<div class="container">
    <div class="row">

        @foreach($products as $product)
            <div class="col-2">
                @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.product-item', compact('product'))
            </div>
        @endforeach

    </div>
</div>

{{--<section class="section-padding-60">--}}
{{--    <div class="container">--}}
{{--        <h3 class="section-title style-1 mb-30 wow fadeIn animated">{!! BaseHelper::clean($title) !!}</h3>--}}
{{--        <div class=" owl-carousel owl-theme featured-brands-carousel ">--}}

{{--            <product-collections-component title="{!! BaseHelper::clean($title) !!}" :product_collections="{{ json_encode($productCollections) }}" url="{{ route('public.ajax.products') }}"></product-collections-component>--}}

{{--        </div>--}}
{{--        --}}{{-- <featured-brands-component url="{{ route('public.ajax.featured-brands') }}"></featured-brands-component> --}}
{{--    </div>--}}
{{--</section>--}}
