@php
    $brands = Theme\Wowy\Http\Controllers\WowyController::ajaxGetMarchi();
@endphp
{{--<section class="section-padding-60">--}}
{{--    <div class="container">--}}
{{--        <h3 class="section-title style-1 mb-30 wow fadeIn animated">{!! BaseHelper::clean($title) !!}</h3>--}}
{{--        --}}{{--        <div id="owl-demo" class="owl-carousel owl-theme">--}}
{{--        <div class=" owl-carousel owl-theme featured-brands-carousel ">--}}
{{--            --}}{{--        <div class=" owl-carousel owl-theme brands-carousel ">--}}

{{--            @foreach ($brands as $brand)--}}
{{--                <div class="col-6">--}}
{{--                    <a class="displayManufacturer" href="{{ $brand->website }}">--}}
{{--                        <img class="displayManufacturerImg" src="{{ RvMedia::getImageUrl($brand->logo ) }}">--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--            @endforeach--}}
{{--        </div>--}}
{{--        --}}{{-- <featured-brands-component url="{{ route('public.ajax.featured-brands') }}"></featured-brands-component> --}}
{{--    </div>--}}
{{--</section>--}}

<div class="container mt-5">
    <div class="row">
        @foreach ($brands as $card)
            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="{{ $card['image'] }}" class="card-img-top" alt="{{ $card['title'] }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $card['title'] }}</h5>
                        <hr>
{{--                                                <p class="card-text">{{ $card['description'] }}</p>--}}
{{--                        <a href="https://www.marigopharma.it/wp-content/uploads/2018/02/Cat_ProntoLeggo_2023_WEB.pdf" target="_blank" class="custom-link btn btn-md btn-accent btn-outline btn-icon-left btn-block">SCARICA IL CATALOGO <i class="fa fa-download"></i></a>--}}
{{--                        <a href="https://www.marigopharma.it/richiesta-informazioni-prodotti-per-farmacie-e-parafarmacie/" tabindex="-1" class="pushed custom-link btn btn-md btn-accent btn-outline btn-icon-left btn-block">RICHIEDI INFORMAZIONI <i class="fa fa-envelope"></i></a>--}}

                                                <a  href="{{ $card['catalog'] }}" target="_blank" class="btn btn-md btn-outline mt-1">SCARICA IL CATALOGO <i class="fa fa-download m-1"  ></i></a>
                                                <a href="https://marigopharma.marigo.collaudo.biz/contact" class="btn btn-md btn-outline mt-1">RICHIEDI INFORMAZIONI <i class="fa fa-envelope m-1"></i></a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

