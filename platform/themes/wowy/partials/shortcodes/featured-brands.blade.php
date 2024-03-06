@php
    $brands = Theme\Wowy\Http\Controllers\WowyController::ajaxGetFeaturedBrands();
@endphp
<section class="section-padding-60">
    <div class="container">
        <h3 class="section-title style-1 mb-30 wow fadeIn animated">{!! BaseHelper::clean($title) !!}</h3>
        {{--        <div id="owl-demo" class="owl-carousel owl-theme"> --}}
        <div class=" owl-carousel owl-theme featured-brands-carousel ">
            {{--        <div class=" owl-carousel owl-theme brands-carousel "> --}}

            @foreach ($brands as $brand)
                <div class="col-6" style="display: flex !important;height: 150px;align-items: center;">
                    <a class="displayManufacturer" href="/products?brands[]={{ $brand->id }}">
                        <img class="displayManufacturerImg" src="{{ RvMedia::getImageUrl($brand->logo) }}">
                    </a>
                </div>
            @endforeach
        </div>
        {{-- <featured-brands-component url="{{ route('public.ajax.featured-brands') }}"></featured-brands-component> --}}
    </div>
</section>
