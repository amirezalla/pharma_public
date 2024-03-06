@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')


    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 mt-5">
                @if (session()->has('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <h2 class="d-inline">Elenco delle offerte</h2>
                <div class="d-inline justify-content-end" style="float: right;margin-right: 10px;">
                    <a href="{{ route('admin.ecommerce.offerte.create-view') }}" class="btn btn-primary">Nuova
                        offerta</a>
                </div>
                <div class="d-inline justify-content-end export-offer" style="float: right;margin-right: 10px;">
                    <a href="{{ route('admin.ecommerce.offerte.exportOffer') }}" class="btn btn-primary">Esportare
                        offerte</a>
                </div>
                <table class="mt-3 table table-striped table-hover">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Stato</th>
                            <th>Tipo</th>
                            <th>Data di inizia</th>
                            <th>Data di scadenza</th>
                            <th>Numero di clienti</th>
                            <th>Azione</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($offers as $offer)
                            @php

                                if ($offer->offer_type == 1 || $offer->offer_type == 2) {
                                    $offer_type = 'sconto percentuale';
                                }
                                if ($offer->offer_type == 3) {
                                    $offer_type = 'prezzo fisso';
                                }
                                if ($offer->offer_type == 4) {
                                    $offer_type = '3x2';
                                }
                                if ($offer->offer_type == 5) {
                                    $offer_type = 'collegati';
                                }
                                if ($offer->offer_type == 6) {
                                    $offer_type = 'quantita';
                                }

                            @endphp
                            <tr data-id="{{ $offer->id }}" class="offerte-row">
                                <td><label class="switch">
                                        <input class='toggle-switch' type="checkbox"
                                            @if ($offer->active == 1) checked @else @endif>
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>{{ $offer->id }}</td>
                                <td>{{ $offer->offer_name }}</td>
                                <td class="offer-status">{!! $offer->active
                                    ? "<button class='btn btn-success'>Attivo</button>"
                                    : "<button class='btn btn-danger'>Inattivo</button>" !!} </td>
                                <td><span class="badge badge-danger">{{ $offer_type }}</span></td>
                                <td>{{ $offer->offer_starting_date }}</td>
                                <td>{{ $offer->offer_expiring_date }}</td>

                                <td><span
                                        class="badge badge-info">{{ $offer->offerDetails->pluck('customer_id')->unique()->count() }}</span>
                                </td>
                                <td>
                                    <button class="delete-offerte btn btn-danger"><i class="fa fa-trash"></i></button>
                                    <a class="btn btn-primary mr-2 edit-offer"
                                        href="/admin/ecommerce/offerte/edit/{{ $offer->id }}"><i
                                            class="fa fa-edit"></i></a>
                                    <button class="btn btn-primary mr-2 export-offer"><i class="fa fa-table"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @push('header')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
        <style>
            .switch {
                position: relative;
                display: inline-block;
                width: 45px;
                height: 24px;
            }

            .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                -webkit-transition: .4s;
                transition: .4s;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 18px;
                width: 18px;
                left: 4px;
                bottom: 3px;
                background-color: white;
                -webkit-transition: .4s;
                transition: .4s;
            }

            input:checked+.slider {
                background-color: #2196F3;
            }

            input:focus+.slider {
                box-shadow: 0 0 1px #2196F3;
            }

            input:checked+.slider:before {
                -webkit-transform: translateX(20px);
                -ms-transform: translateX(20px);
                transform: translateX(20px);
            }

            /* Rounded sliders */
            .slider.round {
                border-radius: 34px;
            }

            .slider.round:before {
                border-radius: 50%;
            }
        </style>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"
            integrity="sha512-uMtXmF28A2Ab/JJO2t/vYhlaa/3ahUOgj1Zf27M5rOo8/+fcTUVH0/E0ll68njmjrLqOBjXM3V9NiPFL5ywWPQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
            $(document).on('change', '.toggle-switch', function(evt) {
                var offerId = $(this).closest('tr').data('id');
                console.log(offerId);
                var activeStatus = $(this).prop('checked');
                if (activeStatus) {

                    $(this).closest(".offer-status").html('<button class="btn btn-success">Attivo</button>');

                } else {
                    $(this).closest(".offer-status").html('<button class="btn btn-danger">Inattivo</button>');

                }
                axios
                    .post("https://marigopharma.marigo.collaudo.biz/admin/ecommerce/offerte/update-offer", {
                        offerId: offerId
                    })
                    .then((response) => {
                        location.reload();
                        console.log(response.data)
                    })
                    .catch((err) => console.log(err));
            });
            $(document).on('click', '.delete-offerte', function(evt) {
                var offerId = $(this).closest('tr').data('id');
                var tr = $(this).closest('tr');
                Swal.fire({
                    title: 'Sei sicuro?',
                    text: "Stai per eliminare l'offerta.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Conferma',
                    cancelButtonText: 'Annulla',
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post(
                                `https://marigopharma.marigo.collaudo.biz/admin/ecommerce/offerte/delete-offer`, {
                                    offerId: offerId
                                })
                            .then((response) => {
                                tr.remove();
                            })
                            .catch((error) => {
                                // Handle error if the deletion fails
                                console.error('Error deleting offer:', error);
                            });
                    }
                });
            });


            $(document).on('click', '.export-offer', function(evt) {
                var offerId = $(this).closest('tr').data('id');
                axios.post(`https://marigopharma.marigo.collaudo.biz/admin/ecommerce/offerte/exportOfferDetails`, {
                        offer_id: offerId
                    })
                    .then((response) => {
                        const blob = new Blob([response.data], {
                            type: 'text/csv'
                        });
                        console.log(response.data, blob)
                        // Create a temporary URL for the Blob object
                        const url = URL.createObjectURL(blob);

                        // Create an anchor element and trigger the download
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'offer_details.csv';
                        document.body.appendChild(a);
                        a.click();

                        // Cleanup: Revoke the temporary URL and remove the anchor element
                        URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                    })

            });
        </script>
    @endpush
@stop
