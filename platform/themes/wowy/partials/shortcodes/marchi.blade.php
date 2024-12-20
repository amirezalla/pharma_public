{{-- @php
    $brands = Theme\Wowy\Http\Controllers\WowyController::ajaxGetMarchi();
@endphp

<div class="container mt-5">
    <div class="row">

        @foreach ($brands as $card)
            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="{{ $card['image'] }}" class="card-img-top" alt="{{ $card['title'] }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $card['title'] }}</h5>
                        <hr>
                        <a href="{{ $card['catalog'] }}" target="_blank" class="btn btn-md btn-outline mt-1">SCARICA IL
                            CATALOGO <i class="fa fa-download m-1"></i></a>
                        <a href="https://marigopharma.it/contact" class="btn btn-md btn-outline mt-1">RICHIEDI
                            INFORMAZIONI <i class="fa fa-envelope m-1"></i></a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div> --}}





<div class="container mt-5">
    <div class="row">
        @for ($i = 1; $i <= 20; $i++)
            @if ($shortcode->{'title' . $i})
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="{{ RvMedia::getImageUrl($shortcode->{'foto' . $i}, null, false, RvMedia::getDefaultImage()) }}"
                            class="card-img-top" alt="{{ $shortcode->{'title' . $i} }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $shortcode->{'title' . $i} }}</h5>
                            <hr>

                            <a href=" {{ RvMedia::getImageUrl($shortcode->{'catalog' . $i}) }}" target="_blank"
                                class="btn btn-md btn-outline mt-1">SCARICA IL CATALOGO <i
                                    class="fa fa-download m-1"></i></a>
                            <a href="https://marigopharma.it/contact" class="btn btn-md btn-outline mt-1">RICHIEDI
                                INFORMAZIONI <i class="fa fa-envelope m-1"></i></a>
                        </div>
                    </div>
                </div>
            @endif
        @endfor
    </div>
</div>
