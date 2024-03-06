
@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
@php
                            use Botble\Ecommerce\Models\Product;
                            use Botble\Ecommerce\Models\Agent;
                            use Botble\Ecommerce\Models\Regione;
                            use Botble\Ecommerce\Models\Customer;


@endphp
<input type="hidden" class="offer-id-hidden" value={{ $offer->id }}>
    {{-- {!! Form::open() !!} --}}
        <div class="container" id="main-discount">
            <div class="max-width-1200 row" style="padding: 20px;background:white;border-radius: 10px;box-shadow: 1px 0px 6px 0px #888;">
                <div class="mt-3 col-md-12 offer-section">
                    <h4>Dettagli dell'offerta</h4>
                    <input disabled type="text" class="form-control col-12" value={{$offer->offer_name}}>
                    <table class="mt-3 table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Stato</th>
                                <th>Tipo</th>
                                <th>Data di scadenza</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                if($offer->offer_type==1 || $offer->offer_type==2) $offer_type='sconto percentuale';
                                if($offer->offer_type==3) $offer_type='prezzo fisso';
                                if($offer->offer_type==4) $offer_type='3x2';
                                if($offer->offer_type==5) $offer_type='collegati';
                                if($offer->offer_type==6) $offer_type='quantita';
                            @endphp
                            <tr data-id="{{ $offer->id }}" class="offerte-row">
                                <td>{{ $offer->id }}</td>
                                <td>{{ $offer->offer_name }}</td>
                                <td class="offer-status">{!! ($offer->active)?"<button class='btn btn-success'>Attivo</button>":"<button class='btn btn-danger'>Inattivo</button>" !!}</td>
                                <td><span class="badge badge-danger">{{ $offer_type }}</span></td>
                                @php

                                if($products->count()<2){
                                    $plannedOffersCount = $offerDetails->where('product_id',$products[0]->id)->where('status', 'planned')->count();
                                }
                                @endphp
                                <td><input type="date" class="form-control editExpiringdate" placeholder="From Date" value={{ $offer->offer_expiring_date }} @if( isset($plannedOffersCount)) @if($plannedOffersCount < 1) @endif  @else disabled  @endif></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 col-md-12 poduct-section">
                    <h4 class='mt-3'>Prodotti dell'offerta</h4>
                        @if($products->count()<2)
                            <div class="alert alert-info" role="alert">
                                La attivazioe/disattivazione su singolo prodotto non è possibile perché questo attiverà/disattiverà l'intera offerta, per questo devi fare direttamente dalla sezione elenco dell'offerte
                            </div>
                        @endif

                            @foreach ($products as $product)
                                @php

                                    $quantita=$offerDetails->where('product_id',$product->id)->first()->quantity;
                                    $product_price=$offerDetails->where('product_id',$product->id)->first()->product_price;

                                    $gift_product=Product::find($offerDetails->where('product_id',$product->id)->first()->gift_product_id);
                                    if($gift_product){
                                        $gift_product_name=$gift_product->name;
                                    }
                                    $flag_three=$offerDetails->where('product_id',$product->id)->first()->flag_three;
                                    $quantita=$offerDetails->where('product_id',$product->id)->first()->quantity;
                                    $allActive=$offerDetails->where('product_id',$product->id)->where("status","active");
                                    if($allActive->count(0)){
                                        $isActive=1;
                                    }else{$isActive=0;}
                                    $filteredRecords=$offerDetails->where('product_id', $product->id);
                                    $customerIds=$filteredRecords->pluck('customer_id')->unique();
                                    $customers=Customer::whereIn('id', $customerIds)->get();
                                @endphp
                                <table data-id="{{ $product->id }}" class="product-table mb-3 table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            @if($products->count()>1)
                                            <th>  </th>
                                            @endif
                                            <th>Codice</th>
                                            <th>Nome</th>
                                            <th>Prezzo</th>
                                            @if($offer->offer_type==6 ) <th>Quantita</th> @endif
                                            @if($offer->offer_type==1 || $offer->offer_type==3 || $offer->offer_type==2 || $offer->offer_type==6 ) <th>Prezzo scontato</th> @endif
                                            @if($offer->offer_type==5 ) <th>Prodotto collegato</th> @endif

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="offerte-row">

                                            <td>
                                                <label class="switch">
                                                    <input class='toggle-switch' type="checkbox" @if($isActive) checked @endif @if($products->count()<2) disabled @endif>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>

                                        <td>{{ $product->sku }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td class="">{{ $product->price }}</td>
                                        @if($offer->offer_type==6 ) <th>{{$quantita}}</th> @endif
                                        @if($offer->offer_type==1 || $offer->offer_type==3 || $offer->offer_type==2 || $offer->offer_type==6 ) <th>{{$product_price}}</th> @endif
                                        @if($offer->offer_type==5 )  @if(isset($gift_product_name))<th>{{$gift_product_name}}</th> @endif @endif
                                        </tr>
                                    </tbody>
                                </table>
                                <h4 class="mt-3">Clienti per {{ $product->name }}</h4>
                                <table class="customer-table mt-3 mb-5 table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>  </th>
                                            <th>Codice</th>
                                            <th>Nome</th>
                                            <th>Agente</th>
                                            <th>Regione</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                @foreach ($customers as $customer)



                                                @php
                                                    $status=$offerDetails->where('customer_id',$customer->id)->where('product_id',$product->id)->first()->status;
                                                @endphp
                                            <tr data-id="{{ $customer->id }}" class="offerte-row">
                                                <td><label class="switch">
                                                    <input class='toggle-switch' type="checkbox" @if($status=='active') checked @endif>
                                                    <span class="slider round"></span>
                                                </label>
                                                </td>
                                                <td>{{ $customer->codice }}</td>
                                                <td>{{ $customer->name }}</td>
                                                @php

                                                $agent=Agent::find($customer->agent_id);
                                                $region=Regione::find($customer->region_id)
                                                @endphp
                                                <td class="customer_agent">@if($agent){{ $agent->nome }} &nbsp {{$agent->cognome}} @endif</td>
                                                <td><span class="badge badge-danger">{{ Regione::find($customer->region_id)->name }}</span></td>
                                            </tr>


                                    @endforeach
                                </tbody>
                            </table>
                            @endforeach

                </div>

            </div>
        </div>


    {{-- {!! Form::close() !!} --}}
@stop

@push('header')
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

      input:checked + .slider {
        background-color: #2196F3;
      }

      input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
      }

      input:checked + .slider:before {
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


.table td{
    padding:8pt;
}
.regione-row tr, .agente-row tr, .strumenti-row tr{

    font-size: 9px

}


.regione-row input:checked + .slider:before , .agente-row input:checked + .slider:before , .strumenti-row input:checked + .slider:before  {
    -webkit-transform: translateX(10px);
    -ms-transform: translateX(10px);
    transform: translateX(10px);
  }


.regione-row .switch , .agente-row .switch , .strumenti-row .switch {
    width: 23px;
    height: 14px;
}
.regione-row .switch .slider:before , .agente-row .switch .slider:before , .strumenti-row .switch .slider:before{
    height: 9px !important;
    width: 9px !important;
    left: 2px;
    bottom: 2px;
    width: 23px;
    height: 14px;
}

    .panel{
        position: absolute;
        background-color: white;
        padding: 15px;
        align-content: space-between;
        display: inline-block;
        z-index: 99999;
        min-width: 300px;
        transform: translateX(10px);
        top: 35px;
        border: none;
        box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px !important
    }
    .prev-page{
        color: mediumblue;
        text-transform: uppercase;
        cursor: pointer;
    }
    .next-page{
    margin-left: 125px;
    color: mediumblue;
    text-transform: uppercase;
    cursor: pointer;
    }
    .div-select-product,
    #div-select-customer{

        position: relative;

    }
    .list-search-data .clearfix .row{
        padding: 15px;
    cursor: pointer;
    }
    .list-search-data .clearfix .row:hover{
        color: mediumblue;
    background-color: #f5f5f5;
    }

    .panel-default .panel-body{
        padding-bottom: none !important;
    }

    .highlighted{
        background-color: #ebeb69;
    }
    .xsmall{
        font-size: x-small;
    }
    .product-checked-row{
        background-color: antiquewhite;
    }
    input[type="date"] {
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #fff;
      }


</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js" integrity="sha512-uMtXmF28A2Ab/JJO2t/vYhlaa/3ahUOgj1Zf27M5rOo8/+fcTUVH0/E0ll68njmjrLqOBjXM3V9NiPFL5ywWPQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>

    'use strict';

    window.trans = window.trans || {};

    window.trans.discount = JSON.parse('{!! addslashes(json_encode(trans('plugins/ecommerce::discount'))) !!}');

    $(document).on('change', '.product-table .toggle-switch', function(evt) {
        evt.preventDefault();
        // Get the data-id of the closest product-table
        var totalCheckedProductTables = $('.product-table .toggle-switch:checked').length;
        var isChecked = $(this).prop('checked');
        if (totalCheckedProductTables ==0) {
            $(this).prop('checked', true);
            Swal.fire({
                icon: 'warning',
                title: 'Non può disattivare il prodotto',
                text: "L'offerta ha bisogno di almeno un prodotto associato, se intendi cancellare o disattivare questa offerta puoi farlo dalla pagina di riepilogo/elenco offerte.",
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33'
            });
            return;
        }else{
            var productTableId = $(this).closest('.product-table').data('id');
            var offerId=$('.offer-id-hidden').val();
            if(isChecked) var status='active';
            else var status='deactive';
            axios
            .post("https://marigolab.it/admin/ecommerce/offerte/deactiveProductInoffer", {product_id:productTableId,offer_id:offerId,status_to:status})
            .then((response) => {
                location.reload();
            })


        }
    });



    $(document).on('change', '.customer-table .toggle-switch', function(evt) {

        var totalCheckedCustomerTables = $('.customer-table .toggle-switch:checked').length;
        var isChecked = $(this).prop('checked');
        if (totalCheckedCustomerTables ==0) {
            $(this).prop('checked', true);
            Swal.fire({
                icon: 'warning',
                title: 'Non può disattivare il cliente',
                text: "L'offerta ha bisogno di almeno un cliente associato, se intendi cancellare o disattivare questa offerta puoi farlo dalla pagina di riepilogo/elenco offerte.",
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33'
            });
            return;
        }else{
            var prevProductTable = $(this).closest('.customer-table').prevAll('.product-table').first();
            var prevToggleSwitchChecked = prevProductTable.find('.toggle-switch:checked').length == 1;
            if (!prevToggleSwitchChecked) {
                // Set the current toggle-switch back to checked
                $(this).prop('checked', false);
                Swal.fire({
                    icon: 'warning',
                    title: 'Impossibile attivare il cliente',
                    text: 'Prima devi attivare il prodotto associato a questo cliente.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                });
                return;
            }else{
                var prevProductTable = $(this).closest('.customer-table').prevAll('.product-table').first();
                var productTableId = prevProductTable.data('id');
                var customerId = $(this).closest('.offerte-row').data('id');


                var offerId = $('.offer-id-hidden').val();
                var status = isChecked ? 'active' : 'deactive';

                var customerTable = $(this).closest('.customer-table');
                var totalCheckedToggleSwitches = customerTable.find('.toggle-switch:checked').length;
                if(totalCheckedToggleSwitches!=0){
                    axios
                    .post("https://marigolab.it/admin/ecommerce/offerte/deactiveCustomerInoffer", {
                        product_id: productTableId,
                        customer_id:customerId,
                        offer_id: offerId,
                        status_to: status
                    })
                    .then((response) => {
                        //location.reload();
                    })
                }else{
                    $(this).prop('checked', true);

                    Swal.fire({
                        icon: 'warning',
                        title: 'Impossibile disattivare il cliente',
                        text: `E' necessario almeno un cliente attivo associato a questo prodotto. Se intendevi disattivare l'offerta per questo prodotto, disabilita il prodotto con il suo apposito pulsante di configurazione.`,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                    return;



                }

            }


        }
    });

    $(document).on('change', '.editExpiringdate', function(evt) {
        var offerId=$('.offer-id-hidden').val();
        const newDate = this.value;
        axios.post('https://marigopharma.marigo.collaudo.biz/admin/ecommerce/offerte/updateExpirationDate', {
            offer_id: offerId,
            expiration_date: newDate
        })
        .then(function (response) {
            console.log(response.data);
            // You can add any success feedback here, like a notification
        })
        .catch(function (error) {
            console.error(error);
            // Handle any errors, perhaps notify the user
        });
    })




</script>

    @php
        Assets::addScripts(['form-validation']);
    @endphp
    {!! JsValidator::formRequest(\Botble\Ecommerce\Http\Requests\DiscountRequest::class) !!}
@endpush
