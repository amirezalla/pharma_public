

{{--@php--}}

{{--@endphp--}}
{{--<!DOCTYPE html>--}}
{{--<html lang="en">--}}
{{--<head>--}}
{{--    <meta charset="UTF-8">--}}
{{--    <meta name="viewport" content="width=device-width, initial-scale=1.0">--}}
{{--    <title>Card Deck</title>--}}
{{--    <!-- Add Bootstrap for styling -->--}}
{{--    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">--}}

{{--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />--}}

{{--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">--}}
{{--</head>--}}
{{--<body>--}}

{{--<div class="container-fluid mt-5">--}}
{{--    <div class="row">--}}
{{--        @foreach ($cards as $card)--}}
{{--            <div class="col-md-3 mb-4">--}}
{{--                <div class="card">--}}
{{--                    <img src="{{ $card['image'] }}" class="card-img-top" alt="{{ $card['title'] }}">--}}
{{--                    <div class="card-body">--}}
{{--                        <h5 class="card-title">{{ $card['title'] }}</h5>--}}
{{--                        <hr>--}}
{{--                        <p class="card-text">{{ $card['description'] }}</p>--}}
{{--                        <a href="https://www.marigopharma.it/wp-content/uploads/2018/02/Cat_ProntoLeggo_2023_WEB.pdf" target="_blank" class="custom-link btn btn-md btn-accent btn-outline btn-icon-left btn-block">SCARICA IL CATALOGO <i class="fa fa-download"></i></a>--}}
{{--                        <a href="https://www.marigopharma.it/richiesta-informazioni-prodotti-per-farmacie-e-parafarmacie/" tabindex="-1" class="pushed custom-link btn btn-md btn-accent btn-outline btn-icon-left btn-block">RICHIEDI INFORMAZIONI <i class="fa fa-envelope"></i></a>--}}

{{--                        <a href="#" class="btn btn-primary">SCARICA IL CATALOGO</a>--}}
{{--                        <a href="#" class="btn btn-secondary">RICHIEDI INFORMAZIONI</a>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        @endforeach--}}
{{--    </div>--}}
{{--</div>--}}
{{--<!-- Add Bootstrap JS -->--}}
{{--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>--}}
{{--</body>--}}
{{--</html>--}}


        <div class="container-fluid mt-5">
            <div class="row">
                @foreach ($cards as $card)
                    <div class="col-md-3 mb-4">
                        <div class="card">
                            <img src="{{ $card['image'] }}" class="card-img-top" alt="{{ $card['title'] }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $card['title'] }}</h5>
                                <hr>
                                {{--                        <p class="card-text">{{ $card['description'] }}</p>--}}
                                <a href="https://www.marigopharma.it/wp-content/uploads/2018/02/Cat_ProntoLeggo_2023_WEB.pdf" target="_blank" class="custom-link btn btn-md btn-accent btn-outline btn-icon-left btn-block">SCARICA IL CATALOGO <i class="fa fa-download"></i></a>
                                <a href="https://www.marigopharma.it/richiesta-informazioni-prodotti-per-farmacie-e-parafarmacie/" tabindex="-1" class="pushed custom-link btn btn-md btn-accent btn-outline btn-icon-left btn-block">RICHIEDI INFORMAZIONI <i class="fa fa-envelope"></i></a>

                                {{--                        <a href="#" class="btn btn-primary">SCARICA IL CATALOGO</a>--}}
                                {{--                        <a href="#" class="btn btn-secondary">RICHIEDI INFORMAZIONI</a>--}}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>


