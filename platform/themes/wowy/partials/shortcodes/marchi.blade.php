<div class="container mt-5">
    <div class="row">
        @dd(shortcode->attributes)
        @for ($i = 1; $i <= 20; $i++)
            @if (isset($shortcode->attributes['title' . $i]))
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="{{ $shortcode->attributes['foto' . $i] }}" class="card-img-top"
                            alt="{{ $shortcode->attributes['title' . $i] }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $shortcode->attributes['title' . $i] }}</h5>
                            <hr>

                            <a href="{{ $shortcode->attributes['catalog' . $i] }}" target="_blank"
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
