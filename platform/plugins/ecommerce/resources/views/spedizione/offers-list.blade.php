@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mt-5">
            <h2 class="d-inline">Elenco delle Coupon spedizione</h2>
            <div class="d-inline justify-content-end" style="float: right;margin-right: 10px;">
                <a href="{{ route('admin.ecommerce.offerte.create-view') }}" class="btn btn-primary">Nuova
                    Coupon</a>
            </div>
            <table class="mt-3 table table-striped table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Codice</th>
                    <th>Stato</th>
                    <th>Tipo</th>
                    <th>Valore</th>
                    <th>Data di inizia</th>
                    <th>Data di scadenza</th>
                    <th>Numero di clienti</th>
                    <th>Azione</th>
                </tr>
                </thead>
                <tbody>
                @foreach($offers as $offer)

                    @php

                        if($offer->type==1) $offer_type='Percentuale';
                        if($offer->type==2) $offer_type='Fissa';
                        if($offer->type==3) $offer_type='Gratuita';
                    @endphp
                    <tr data-id="{{ $offer->id }}" class="offerte-row">
                        <td>{{ $offer->id }}</td>
                        <td>{{ $offer->code }}</td>
                        <td class="offer-status">{!! ($offer->status )?"<button class='btn btn-success'>Attivo</button>":"<button class='btn btn-danger'>Inattivo</button>" !!} </td>
                        <td><span class="badge badge-danger">{{ $offer_type }}</span></td>
                        <td>{{ $offer->amount }}</td>
                        <td>{{ $offer->starting_date }}</td>
                        <td>{{ $offer->expiring_date }}</td>

                        <td><span class="badge badge-info">{{ $offer->customers()->pluck('customer_id')->count() }}</span></td>
                        <td>
                            <button class="delete-coupon btn btn-danger" onclick="deleteOffer({{ $offer->id }})"><i class="fa fa-trash"></i></button>
                            <button class="btn btn-primary mr-2 export-coupon-customers" onclick="exportCustomers({{ $offer->id }})"><i class="fa fa-user"></i></button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>

    function deleteOffer(offerId) {
        // Display a confirmation dialog using SweetAlert
        Swal.fire({
            title: 'Confirm Deletion',
            text: 'Are you sure you want to delete this offer?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'No, cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                // Send the DELETE request
                axios.post('/admin/ecommerce/spedizione/delete', { id: offerId })
                    .then(function (response) {
                        if (response.data.success) {
                            Swal.fire({
                                title: 'Success',
                                text: response.data.success,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // If the user clicks 'OK', you can perform additional actions, such as reloading the page.
                                location.reload(); // This is just an example; adjust as needed.
                            });
                        } else {
                            console.log(response.data);
                        }
                    })
                    .catch(function (error) {
                        console.error(error);
                    });
            }
        });
    }

    function exportCustomers(id) {

        axios.post(`/admin/ecommerce/spedizione/customers_export`, {id: id})
            .then((response) => {
                const blob = new Blob([response.data], {type: 'text/csv'});
                console.log(response.data, blob)
                // Create a temporary URL for the Blob object
                const url = URL.createObjectURL(blob);

                // Create an anchor element and trigger the download
                const a = document.createElement('a');
                a.href = url;
                a.download = 'SP_offer_id'+id+'_customers.csv';
                document.body.appendChild(a);
                a.click();

                // Cleanup: Revoke the temporary URL and remove the anchor element
                URL.revokeObjectURL(url);
                document.body.removeChild(a);
            }).catch(function(error) {
                // Check if the status code is 404 and the message is as expected
                if (error.response && error.response.status === 404 && error.response.data.message === "Offer not found for the given user ID") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.response.data.message,
                    });
                }
            });
    }


</script>

@stop
