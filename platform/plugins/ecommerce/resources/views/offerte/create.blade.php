@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')

    {{-- {!! Form::open() !!} --}}
        <div class="container mt-5" id="main-discount">
            <div class="max-width-1200 row" style="padding: 20px;background:white;border-radius: 10px;box-shadow: 1px 0px 6px 0px #888;">
                <div class="col-md-12">
                    <h4>Crea una promozione di sconto</h4>
                    <input type="text" id='discount-name' class="form-control" placeholder="Inserisci il nome della promozione" name="title">
                    <hr>
                </div>
                <div class="col-md-12">
                    <h5>Selezionare il tipo di offerta </h5>
                    <select id="offerType" class="form-select">
                        <option selected>Open this select menu</option>
                        <option value="1">offerta con sconto percentuale</option>
                        <option value="2">offerta con sconto percentuale con price range</option>
                        <option value="3">offerta con prezzo fisso</option>
                        <option value="4">offerta 3x2</option>
                        <option value="5">offerta su articoli collegati</option>
                        <option value="6">offerta di sconto per quantita</option>
                      </select>
                      <input type="hidden" id="offer_type_hidden">
                </div>


                <div class="col-md-12 d-none" id='sconto'>
                    <div class="row mt-3">
                        <div class="col-8">
                        <h5>Prodotto</h5>
                        <div class="div-select-product">
                            <div class="box-search-advance product" style="min-width:310px;">
                                <input type="text" class="form-control next-input textbox-advancesearch product product-search-realtime" placeholder="Cerca prodotto" aria-invalid="false">
                                <input type="hidden" name='current-page' class="product-search-current-page">
                            </div>
                            <div class="panel panel-default d-none">
                                <div class="panel-body">
                                    <div class="list-search-data">
                                        <ul class="clearfix">

                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <hr>
                        </div>
                        <div class="col-4">
                            <label for="scontoPercentuale">Percentuale di sconto</label>
                            <input type="text" name="percentuale" class="form-control" id="percentual" placeholder="Percentuale di sconto">
                        </div>
                    </div>
                </div>


                <div class="col-md-12 d-none" id='scontorange'>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-warning" role="alert">
                                in questa tipologia è possibile aggiungere solo un prodotto all offerta
                            </div>
                        </div>
                        <div class="col-8">
                        <h5>Prodotto</h5>
                        <div class="div-select-product">
                            <div class="box-search-advance product" style="min-width:310px;">
                                <input type="text" class="form-control next-input textbox-advancesearch product product-search-realtime" placeholder="Cerca prodotto" aria-invalid="false">
                                <input type="hidden" name='current-page' class="product-search-current-page">
                            </div>
                            <div class="panel panel-default d-none">
                                <div class="panel-body">
                                    <div class="list-search-data">
                                        <ul class="clearfix">

                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <hr>
                        </div>
                        <div class="col-4">
                            <label for="scontoPercentuale">Percentuale di sconto</label>
                            <input type="text" name="percentuale" class="form-control" id="percentual" placeholder="Percentuale di sconto">
                        </div>
                    </div>
                </div>


                <div class="col-md-12 d-none" id='fisso'>
                    <div class="row mt-3">
                        <div class="col-12">
                        <h5>Prodotto</h5>
                        <div class="div-select-product">
                            <div class="box-search-advance product" style="min-width:310px;">
                                <input type="text" class="form-control next-input textbox-advancesearch product product-search-realtime" placeholder="Cerca prodotto" aria-invalid="false">
                                <input type="hidden" name='current-page' class="product-search-current-page">
                            </div>
                            <div class="panel panel-default d-none">
                                <div class="panel-body">
                                    <div class="list-search-data">
                                        <ul class="clearfix">

                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <hr>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 d-none" id='three'>
                    <div class="row mt-3">
                        <div class="col-12">
                        <h5>Prodotto</h5>
                        <div class="div-select-product">
                            <div class="box-search-advance product" style="min-width:310px;">
                                <input type="text" class="form-control next-input textbox-advancesearch product product-search-realtime" placeholder="Cerca prodotto" aria-invalid="false">
                                <input type="hidden" name='current-page' class="product-search-current-page">
                            </div>
                            <div class="panel panel-default d-none">
                                <div class="panel-body">
                                    <div class="list-search-data">
                                        <ul class="clearfix">

                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <hr>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 d-none" id='collegati'>

                    <div class="row mt-3">
                        <div class="col-12">
                        <h5>Prodotto</h5>
                        <div class="div-select-product">
                            <div class="box-search-advance product" style="min-width:310px;">
                                <input type="text" class="form-control next-input textbox-advancesearch product product-search-realtime"  placeholder="Cerca prodotto" aria-invalid="false">
                                <input type="hidden" name='current-page' class="product-search-current-page">

                            </div>
                            <div class="panel panel-default d-none">
                                <div class="panel-body">
                                    <div class="list-search-data">
                                        <ul class="clearfix">

                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <hr>
                        </div>
                    </div>


                </div>
                <div class="col-md-12 d-none" id='quantita'>
                    <div class="row mt-3">
                        <div class="col-12">
                        <h5>Prodotto</h5>
                        <div class="div-select-product">
                            <div class="box-search-advance product" style="min-width:310px;">
                                <input type="text" class="form-control next-input textbox-advancesearch product product-search-realtime" placeholder="Cerca prodotto" aria-invalid="false">
                                <input type="hidden" name='current-page' class="product-search-current-page">
                            </div>
                            <div class="panel panel-default d-none">
                                <div class="panel-body">
                                    <div class="list-search-data">
                                        <ul class="clearfix">
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        </div>
                    </div>
                </div>

                <table class="table table-striped table-hover table-bordered">
                    <tbody class='product-row'>

                    </tbody>
                </table>




                <div class="row mt-3 users-sections d-none">
                    <div class="col-12">
                        <div class="alert alert-success" role="alert">
                            ci sono <span class="user-count">0</span> cliente per i consumabili selezionati e la selezione di più opzioni ne escluderà di più.
                            <br>
                            se vuoi includere tutto lascialo così.
                        </div>
                    </div>
                    <div class="d-none" id='caratter'>
                        <div class="date-row row mt-3 d-none">
                            <h5>Non più acquistato dopo il:</h5>
                            <div class="col">
                                <input type="date" class="form-control fromDate" placeholder="Dopo il"><br>
                                <input type='checkbox' class='include-new d-none'> &nbsp
                                <span for="include-new" class="d-none">Includi nuovi clienti che non sono presenti nella cronologia</span>
                            </div>
                            {{--  <div class="col">  --}}
                                {{--  <input type="date" class="form-control toDate" placeholder="Dopo il" max="{{ date('Y-m-d') }}">  --}}
                            {{--  </div>  --}}
                            <div class="col">
                                <button type="button" class="btn btn-primary fromToApply">Apply</button>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h5>Regione</h5>
                                    <div class="div-select-regione" style="position: relative">
                                        <div class="box-search-advance regione" style="min-width:310px;">
                                            <input type="text" class="form-control next-input textbox-advancesearch regione regione-search-realtime" placeholder="Cerca regione" aria-invalid="false">
                                            <input type="hidden" name='current-page' class="regione-search-current-page">
                                        </div>
                                    </div>
                                    <table class="table table-striped table-hover table-bordered regione-table">
                                        <tbody class='regione-row'>
                                        </tbody>
                                    </table>
                                    <hr>
                                </div>
                            <div class="col-md-12">
                                <h5>Agente</h5>
                                <div class="div-select-agente" style='position:relative'>
                                    <div class="box-search-advance agente" style="min-width:310px;">
                                        <input type="text" class="form-control next-input textbox-advancesearch agente agente-search-realtime" placeholder="Cerca agente" aria-invalid="false">
                                        <input type="hidden" name='current-page' class="agente-search-current-page">
                                    </div>
                                </div>
                                <table class="table table-striped table-hover table-bordered agente-table ">
                                    <tbody class='agente-row'>
                                    </tbody>
                                </table>
                                <hr>
                            </div>
                        </div>
                        </div>
                        <div class="col-md-12">
                            <h5>Clienti <span class="badge badge-danger active-users-count d-none">0</span> </h5>
                            <div class="div-select-users" style="position: relative">
                                <div class="box-search-advance" style="min-width:310px;">
                                    <input type="text" class="form-control next-input textbox-advancesearch users users-search-realtime" placeholder="Cerca per codice o nome" aria-invalid="false">
                                </div>
                            </div>

                            <table class="table table-striped table-hover table-bordered">
                                <tbody class='users-row'>

                                </tbody>
                            </table>
                            <hr>
                        </div>
                        <div class="col-md-6 mt-3">
                            <h5> Data d'inizio</h5>
                                <input type="date" id="start_date" value="<?= date('Y-m-d'); ?>" >
                        </div>
                        <div class="col-md-6 mt-3">
                            <h5>Data di scadenza</h5>
                                <input type="date" id="expiring_date" min="<?= date('Y-m-d'); ?>">
                        </div>

                        <form action="https://marigopharma.marigo.collaudo.biz/admin/ecommerce/customImport/sconto" method="post" class="sconto-form">
                            @csrf
                            <input class="mt-5 btn btn-primary mb-5 col-6 discount-check-submit" value="Creare Sconto">
                        </form>
                    </div>





                <div class="alert alert-success update-alert hidden" role="alert">

                </div>
                <br>

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



.regione-row input:checked + .slider:before , .agente-row input:checked + .slider:before {
    -webkit-transform: translateX(10px);
    -ms-transform: translateX(10px);
    transform: translateX(10px);
  }


.regione-row .switch , .agente-row .switch{
    width: 23px;
    height: 14px;
}
.regione-row .switch .slider:before , .agente-row .switch .slider:before {
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



    $(document).ready(function () {

        $('body').click(function(evt){
            let container = $('.div-select-product');
            if(evt.target.className == "product-search-realtime")
                container.find('.panel').removeClass('hidden');
            if($(evt.target).closest('.product-search-realtime').length)
                return;
            container.find('.panel').addClass('hidden');

        });

        $(document).on('click','body',function(evt){
            let container = $('.div-select-product-in-collegati');
            if(evt.target.className == "product-search-realtime-in-collegati")
                container.find('.panel').removeClass('hidden');
            //For descendants of menu_content being clicked, remove this check if you do not want to put constraint on descendants.
            if($(evt.target).closest('.product-search-realtime-in-collegati').length)
                return;

            container.find('.panel').addClass('hidden');
            //Do processing of click event here for every element except with id menu_content

        });



        function sendFilterRequest(fromDate, inncludeNew) {
            var includeNew = $('#includeNew').is(':checked') ? $('#includeNew').val() : 'no';

            var uncheckedAgentIds = [];
            $('.agente-row tr').each(function() {
                var id = $(this).attr('id');
                var checkbox = $(this).find('.toggle-switch');
                if (checkbox.prop('checked')) {
                    uncheckedAgentIds.push(id);
                }
            });

            var uncheckedRegioneIds = [];
            $('.regione-row tr').each(function() {
                var id = $(this).attr('id');
                var checkbox = $(this).find('.toggle-switch');
                if (checkbox.prop('checked')) {
                    uncheckedRegioneIds.push(id);
                }
            });



            var idArray = [];
            $('.product-row tr').each(function() {
            var rowId = $(this).attr('id');
            idArray.push(rowId);
            });

            var customers = [];
            $('.users-row tr').each(function() {
                var id = $(this).attr('id');
                var checkbox = $(this).find('.toggle-switch-user');
                if (checkbox.prop('checked')) {
                    customers.push(id);
                }
            });
            axios
                .post("https://marigopharma.marigo.collaudo.biz/filter-customers", {
                    customers:customers,
                    consumabili:idArray,
                    agents: uncheckedAgentIds,
                    regions: uncheckedRegioneIds,
                    fromDate: fromDate,
                    includeNew: includeNew
                })
                .then((response) => {
                    var idsToUncheck = response.data.customersToUncheck;
                    $.each(idsToUncheck, function(index, id) {
                        $('.users-row #' + id + ' .toggle-switch-user').prop('checked', false);
                    });

                    var idsToCheck = response.data.customersToCheck;
                    $.each(idsToCheck, function(index, id) {
                        $('.users-row #' + id + ' .toggle-switch-user').prop('checked', true);
                    });

                    var checkedUserIds = [];
                    $('.users-row tr').each(function() {
                        var id = $(this).attr('id');
                        var checkbox = $(this).find('.toggle-switch-user');
                        if (checkbox.prop('checked')) {
                            checkedUserIds.push(id);
                        }
                    });

                    var count = response.data.count;
                    $('.active-users-count').html(count);
                    $('.active-users-count').removeClass('d-none');

                })
                .catch((err) => console.log(err));
        }





        $(document).on('click','.fromToApply',function(evt){
            var fromDate = $('.fromDate').val();
            var includeNew = $('.include-new').val();
            sendFilterRequest(fromDate, includeNew);
        });


        $(document).on('change','.toggle-switch',function(evt){
            var fromDate = $('.fromDate').val();
            var includeNew = $('.include-new').val();
            sendFilterRequest(fromDate, includeNew);
        });




        $('.users-search-realtime').keyup(function(){
            const searchTerm=$(this).val().toLowerCase();

            $('.users-row tr').each(function() {
                var rowText = $(this).find('.name').text().toLowerCase();
                var rowText1=$(this).find('.codice').text().toLowerCase();

                if (searchTerm.length < 1) {
                    $('.users-row tr').removeClass('highlighted');
                    return;
                }

                if (rowText.indexOf(searchTerm) !== -1 || rowText1.indexOf(searchTerm) !== -1 ) {
                    $(this).addClass('highlighted');
                } else {
                    $(this).removeClass('highlighted');
                }

            });

        });



        $(document).on('keyup','.agente-search-realtime',function(){
            const searchTerm=$(this).val().toLowerCase();

            $('.agente-row tr').each(function() {
                var rowText = $(this).find('.name').text().toLowerCase();

                if (searchTerm.length < 1) {
                    $('.agente-row tr').removeClass('highlighted');
                    return;
                }

                if (rowText.indexOf(searchTerm) !== -1) {
                    $(this).addClass('highlighted');
                } else {
                    $(this).removeClass('highlighted');
                }

            });
        });

        $(document).on('keyup','.regione-search-realtime',function(){
            const searchTerm=$(this).val().toLowerCase();

            $('.regione-row tr').each(function() {
                var rowText = $(this).find('.name').text().toLowerCase();

                if (searchTerm.length < 1) {
                    $('.regione-row tr').removeClass('highlighted');
                    return;
                }

                if (rowText.indexOf(searchTerm) !== -1) {
                    $(this).addClass('highlighted');
                } else {
                    $(this).removeClass('highlighted');
                }

            });
        });


        $('.product-search-realtime').keyup(function(){
                const keyword=$(this).val()
                if($('.product-search-current-page').val()!=''){
                    var page=$('.product-search-current-page').val()
                }
                else{var page=1}

                axios
                .get("https://marigopharma.marigo.collaudo.biz/admin/ecommerce/products/get-list-products-for-select", { params:{
                keyword: $(this).val(),
                include_variation: true,
                page: page
                }})
                .then((response) => {
                    $(".div-select-product .panel").removeClass('d-none');
                    $(".div-select-product .panel").removeClass('hidden');
                    const products=response.data.data.data;
                    $('.div-select-product .panel .panel-body .list-search-data .clearfix').html('');
                    $('.div-select-product .panel .panel-body .list-search-data .navigation').html('');

                    products.forEach(element => {
                        $('.div-select-product .panel .panel-body .list-search-data .clearfix').append("<li class='row product-select-btn' data-id='"+element.id+"' data-sku='"+element.sku+"' data-value='"+element.price+"'>"+element.name+"<br><small style='font-size:xxsmall;color:#888'>"+element.sku+"</small></li>");

                    });
                    // $('#div-select-product .panel .panel-body .list-search-data .navigation').append("<span class='prev-page' data-href='"+response.data.data.prev_page_url+"'>prev page</span>")
                    // $('#div-select-product .panel .panel-body .list-search-data .navigation').append("<span class='next-page' data-href='"+response.data.data.next_page_url+"'>next page</span>")
                    console.log(response.data);
                })

                .catch((err) => console.log(err));
        });


        $(document).on('keyup','.product-search-realtime-in-collegati',function(){
                const keyword=$(this).val()
                console.log(keyword);

                if($('.product-search-current-page').val()!=''){
                    var page=$('.product-search-current-page').val()
                }
                else{var page=1}

                axios
                .get("https://marigopharma.marigo.collaudo.biz/admin/ecommerce/products/get-list-products-for-select", { params:{
                keyword: $(this).val(),
                include_variation: 0,
                page: page
                }})
                .then((response) => {
                    $(".div-select-product-in-collegati .panel").removeClass('d-none');
                    $(".div-select-product-in-collegati .panel").removeClass('hidden');
                    const products=response.data.data.data;
                    $('.div-select-product-in-collegati .panel .panel-body .list-search-data .clearfix').html('');

                    products.forEach(element => {
                        $('.div-select-product-in-collegati .panel .panel-body .list-search-data .clearfix').append("<li class='row product-select-btn-in-collegati' data-id='"+element.id+"' data-sku='"+element.sku+"' data-value='"+element.price+"'>"+element.name+"<br><small style='font-size:xxsmall;color:#888'>"+element.sku+"</small></li>");

                    });
                    // $('#div-select-product .panel .panel-body .list-search-data .navigation').append("<span class='prev-page' data-href='"+response.data.data.prev_page_url+"'>prev page</span>")
                    // $('#div-select-product .panel .panel-body .list-search-data .navigation').append("<span class='next-page' data-href='"+response.data.data.next_page_url+"'>next page</span>")
                    console.log(response.data);
                })
                .catch((err) => console.log(err));

        });


        $(document).on("click", ".reselect-collegati", function(){
            $(this).closest('.collegati').html(`
                    <div class="div-select-product-in-collegati" style='position:relative'>
                        <div class="box-search-advance product" style="min-width:310px;">
                            <input type="text" class="form-control next-input textbox-advancesearch product product-search-realtime-in-collegati" placeholder="Cerca prodotto" aria-invalid="false">
                            <input type="hidden" name='current-page' class="product-search-current-page">
                        </div>
                        <div class="panel panel-default d-none">
                            <div class="panel-body">
                                <div class="list-search-data">
                                    <ul class="clearfix">
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
            `);
        });


        $(document).on("click", ".product-select-btn-in-collegati", function(){
            var sku=$(this).attr('data-sku');
            $('.sconto-form').append("<input type='hidden' name='products[]' value="+sku+">");
            var price=$(this).attr('data-value');
            var id=$(this).attr('data-id');
            var name=$(this).text();
            var sconto=$('#percentual').val();
            $(this).closest('.collegati').html(`
                <p data-gift-product-id=${id} style='margin:unset'>${name} &nbsp <small>${sku}</small><button class='btn btn-sm reselect-collegati'><i class='fa fa-search'></i></button></p>
            `);



            var idArray = [];
            $('.product-row tr').each(function() {
                var rowId = $(this).attr('id');
                idArray.push(rowId);

                // Get the value of data-gift-product-id of the p tag in this row
                var giftProductId = $(this).find('p').data('gift-product-id');
                if (giftProductId !== undefined) {
                    idArray.push(String(giftProductId));
                }

            });
            console.log(idArray);


            axios
            .post("https://marigopharma.marigo.collaudo.biz/get-customers-by-product", {products: idArray,collegati:1})
            .then((response) => {
                console.log(response.data);
                var customers = response.data.incustomers;
                var agents= response.data.agents;
                var regione= response.data.regione;
                var count= response.data.count;
                $('.user-count').html(count);
                $('.users-sections').removeClass('d-none')
                $('.users-row').html(`
                <tr>
                    <th>
                        <label class="switch">
                            <input class='toggle-switch-all-customers' type="checkbox" checked>
                            <span class="slider round"></span>
                        </label>
                    </th>
                </tr>`);
                $('.agente-row').html(`
                <tr>
                    <th>
                        <label class="switch">
                            <input class='toggle-switch-all' type="checkbox" checked>
                            <span class="slider round"></span>
                        </label>
                    </th>
                </tr>`);
                $('.date-row').removeClass('d-none');
                $('.regione-row').html(`
                <tr>
                    <th>
                        <label class="switch">
                            <input class='toggle-switch-all' type="checkbox" checked>
                            <span class="slider round"></span>
                        </label>
                    </th>
                </tr>`);

                $('#caratter').removeClass('d-none');
                customers.forEach(function(element) {
                    var id=element.id;
                    var codice=element.codice;
                    var name=element.name;
                    $('.users-row').append(`
                        <tr id=${id}>
                            <td><label class="switch">
                                <input class='toggle-switch-user' type="checkbox" checked>
                                <span class="slider round"></span>
                            </label>
                            </td>
                            <td class='codice'>${codice}</td>
                            <td class='name'>${name}</td>
                        </tr>
                    `);
                });

                agents.forEach(function(element) {
                    var id=element.id;
                    var codice=element.codice;
                    var name=element.nome + ' ' + element.cognome;
                    $('.agente-row').append(`
                        <tr id=${id}>
                            <td><label class="switch">
                                <input class='toggle-switch' type="checkbox" checked>
                                <span class="slider round"></span>
                            </label>
                            </td>
                            <td>${codice}</td>
                            <td class='name'>${name}</td>
                        </tr>
                    `);
                });
                regione.forEach(function(element) {
                    var id=element.id;
                    var name=element.name ;
                    $('.regione-row').append(`
                        <tr id=${id}>
                            <td><label class="switch">
                                <input class='toggle-switch' type="checkbox" checked>
                                <span class="slider round"></span>
                            </label>
                            </td>
                            <td class='name'>${name}</td>
                        </tr>
                    `);
                });


            })

            .catch((err) => console.log(err));




        });




        $(document).on("click", " #fisso .product-select-btn", function(){
            var sku=$(this).attr('data-sku');
            var id=$(this).attr('data-id');

            var price=$(this).attr('data-value');
            var name=$(this).text();
            var sconto=$('#percentual').val();
            $('.product-row').append(`
            <tr id=${id}>
                <td>${sku}</td>
                <td>${name}</td>
                <td class='real-price'>${price} € </td>
                <td class='fixed-price'><input type='text' placeholder='fixed price' class='fixed-price form-control'></td>
                <td><button class='btn btn-danger deleteProduct_table' data-value='${id}'><i class='fa fa-trash'></i></button></td>
            </tr>
            `);

            var idArray = [];
            $('.product-row tr').each(function() {
            var rowId = $(this).attr('id');
            idArray.push(rowId);
            });
            axios
            .post("https://marigopharma.marigo.collaudo.biz/get-customers-by-product", {products: idArray})
            .then((response) => {
                console.log(response.data);
                var customers = response.data.incustomers;
                var agents= response.data.agents;
                var regione= response.data.regione;
                var count= response.data.count;
                $('.user-count').html(count);
                $('.users-sections').removeClass('d-none');
                $('.users-row').html(`
                <tr>
                    <th>
                        <label class="switch">
                            <input class='toggle-switch-all-customers' type="checkbox" checked>
                            <span class="slider round"></span>
                        </label>
                    </th>
                </tr>`);
                $('.agente-row').html(`
                <tr>
                    <th>
                        <label class="switch">
                            <input class='toggle-switch-all' type="checkbox" checked>
                            <span class="slider round"></span>
                        </label>
                    </th>
                </tr>`);
                $('.date-row').removeClass('d-none');
                $('.regione-row').html(`
                <tr>
                    <th>
                        <label class="switch">
                            <input class='toggle-switch-all' type="checkbox" checked>
                            <span class="slider round"></span>
                        </label>
                    </th>
                </tr>`);

                $('#caratter').removeClass('d-none');
                customers.forEach(function(element) {
                    var id=element.id;
                    var codice=element.codice;
                    var name=element.name;
                    $('.users-row').append(`
                        <tr id=${id}>
                            <td><label class="switch">
                                <input class='toggle-switch-user' type="checkbox" checked>
                                <span class="slider round"></span>
                            </label>
                            </td>
                            <td class='codice'>${codice}</td>
                            <td class='name'>${name}</td>
                        </tr>
                    `);
                });

                agents.forEach(function(element) {
                    var id=element.id;
                    var codice=element.codice;
                    var name=element.nome + ' ' + element.cognome;
                    $('.agente-row').append(`
                        <tr id=${id}>
                            <td><label class="switch">
                                <input class='toggle-switch' type="checkbox" checked>
                                <span class="slider round"></span>
                            </label>
                            </td>
                            <td>${codice}</td>
                            <td class='name'>${name}</td>
                        </tr>
                    `);
                });
                regione.forEach(function(element) {
                    var id=element.id;
                    var name=element.name ;
                    $('.regione-row').append(`
                        <tr id=${id}>
                            <td><label class="switch">
                                <input class='toggle-switch' type="checkbox" checked>
                                <span class="slider round"></span>
                            </label>
                            </td>
                            <td class='name'>${name}</td>
                        </tr>
                    `);
                });

            })

            .catch((err) => console.log(err));


        });


        $(document).on("click", "#sconto .product-select-btn ", function(){
            var sku=$(this).attr('data-sku');
            var id=$(this).attr('data-id');


                    $('.sconto-form').append("<input type='hidden' name='products[]' value="+sku+">");
                    var price=$(this).attr('data-value');
                    var name=$(this).text();
                    var sconto=$('#percentual').val();
                    $('.product-row').append(`
                    <tr id=${id}>
                        <td>${sku}</td>
                        <td>${name}</td>
                        <td class='real-price'>${price} € </td>
                        <td class='offer-price'>${(price*(100-sconto))/100 + '€'}</td>
                        <td><button class='btn btn-danger deleteProduct_table' data-value='${id}'><i class='fa fa-trash'></i></button></td>
                    </tr>
                    `);

                    var idArray = [];
                    $('.product-row tr').each(function() {
                    var rowId = $(this).attr('id');
                    idArray.push(rowId);
                    });
                    axios
                    .post("https://marigopharma.marigo.collaudo.biz/get-customers-by-product", {products: idArray})
                    .then((response) => {
                        console.log(response.data);
                        var customers = response.data.incustomers;
                        var agents= response.data.agents;
                        var regione= response.data.regione;
                        var count= response.data.count;
                        $('.user-count').html(count);
                        $('.users-sections').removeClass('d-none')
                        $('.users-row').html(`
                        <tr>
                            <th>
                                <label class="switch">
                                    <input class='toggle-switch-all-customers' type="checkbox" checked>
                                    <span class="slider round"></span>
                                </label>
                            </th>
                            <th>Seleziona/Deseleziona tutti</th>
                        </tr>`);
                        $('.agente-row').html(`
                        <tr>
                            <th>
                                <label class="switch">
                                    <input class='toggle-switch-all' type="checkbox" checked>
                                    <span class="slider round"></span>
                                </label>
                            </th>
                            <th>Seleziona/Deseleziona tutti</th>
                        </tr>`);
                        $('.date-row').removeClass('d-none');
                        $('.regione-row').html(`
                        <tr>
                            <th>
                                <label class="switch">
                                    <input class='toggle-switch-all' type="checkbox" checked>
                                    <span class="slider round"></span>
                                </label>
                            </th>
                            <th>Seleziona/Deseleziona tutti</th>
                        </tr>`);

                        $('#caratter').removeClass('d-none');
                        customers.forEach(function(element) {
                            var id=element.id;
                            var codice=element.codice;
                            var name=element.name;
                            $('.users-row').append(`
                                <tr id=${id}>
                                    <td><label class="switch">
                                        <input class='toggle-switch-user' type="checkbox" checked>
                                        <span class="slider round"></span>
                                    </label>
                                    </td>
                                    <td class='codice'>${codice}</td>
                                    <td class='name'>${name}</td>
                                </tr>
                            `);
                        });

                        agents.forEach(function(element) {
                            var id=element.id;
                            var codice=element.codice;
                            var name=element.nome + ' ' + element.cognome;
                            $('.agente-row').append(`
                                <tr id=${id}>
                                    <td><label class="switch">
                                        <input class='toggle-switch' type="checkbox" checked>
                                        <span class="slider round"></span>
                                    </label>
                                    </td>
                                    <td>${codice}</td>
                                    <td class='name'>${name}</td>
                                </tr>
                            `);
                        });
                        regione.forEach(function(element) {
                            var id=element.id;
                            var name=element.name ;
                            $('.regione-row').append(`
                                <tr id=${id}>
                                    <td><label class="switch">
                                        <input class='toggle-switch' type="checkbox" checked>
                                        <span class="slider round"></span>
                                    </label>
                                    </td>
                                    <td class='name'>${name}</td>
                                </tr>
                            `);
                        });

                    })

                    .catch((err) => console.log(err));


        });


        $(document).on("click", "#scontorange .product-select-btn", function(){
            $('.product-search-realtime').prop('disabled',true);
            var sku=$(this).attr('data-sku');
            var id=$(this).attr('data-id');


                    $('.sconto-form').append("<input type='hidden' name='products[]' value="+sku+">");
                    var price=$(this).attr('data-value');
                    var name=$(this).text();
                    var sconto=$('#percentual').val();
                    $('.product-row').append(`
                    <tr id=${id}>
                        <td>${sku}</td>
                        <td>${name}</td>
                        <td class='real-price'>${price} € </td>
                        <td class='offer-price'>${(price*(100-sconto))/100 + '€'}</td>
                        <td class='scontorange-offer-range'><input type='text' placeholder='min price' class='min form-control'> &nbsp <input type='text' placeholder='max price' class='max form-control'><br><button class='apply-sconto-range btn btn-primary'>apply</button></td>
                        <td><button class='btn btn-danger deleteProduct_table' data-value='${id}'><i class='fa fa-trash'></i></button></td>
                    </tr>
                    `);

        });


        $(document).on("click", ".apply-sconto-range", function(){
            var idArray = [];
            $('.product-row tr').each(function() {
            var rowId = $(this).attr('id');
            idArray.push(rowId);
            });
            var max = $('.max').val();
            var min = $('.min').val();
            axios
                    .post("https://marigopharma.marigo.collaudo.biz/get-customers-by-product", {products: {id:idArray[0],max:max,min:min},scontorange:true})
                    .then((response) => {
                        console.log(response.data);
                        var customers = response.data.incustomers;
                        var agents= response.data.agents;
                        var regione= response.data.regione;
                        var count= response.data.count;
                        $('.user-count').html(count);
                        $('.users-sections').removeClass('d-none')
                        $('.users-row').html(`
                        <tr>
                            <th>
                                <label class="switch">
                                    <input class='toggle-switch-all-customers' type="checkbox" checked>
                                    <span class="slider round"></span>
                                </label>
                            </th>
                            <th>Seleziona/Deseleziona tutti</th>
                        </tr>`);
                        $('.agente-row').html(`
                        <tr>
                            <th>
                                <label class="switch">
                                    <input class='toggle-switch-all' type="checkbox" checked>
                                    <span class="slider round"></span>
                                </label>
                            </th>
                            <th>Seleziona/Deseleziona tutti</th>
                        </tr>`);
                        $('.date-row').removeClass('d-none');
                        $('.regione-row').html(`
                        <tr>
                            <th>
                                <label class="switch">
                                    <input class='toggle-switch-all' type="checkbox" checked>
                                    <span class="slider round"></span>
                                </label>
                            </th>
                            <th>Seleziona/Deseleziona tutti</th>
                        </tr>`);

                        $('#caratter').removeClass('d-none');
                        customers.forEach(function(element) {
                            var id=element.id;
                            var codice=element.codice;
                            var name=element.name;
                            $('.users-row').append(`
                                <tr id=${id}>
                                    <td><label class="switch">
                                        <input class='toggle-switch-user' type="checkbox" checked>
                                        <span class="slider round"></span>
                                    </label>
                                    </td>
                                    <td>${codice}</td>
                                    <td>${name}</td>
                                </tr>
                            `);
                        });


                        agents.forEach(function(element) {
                            var id=element.id;
                            var codice=element.codice;
                            var name=element.nome + ' ' + element.cognome;
                            $('.agente-row').append(`
                                <tr id=${id}>
                                    <td><label class="switch">
                                        <input class='toggle-switch' type="checkbox" checked>
                                        <span class="slider round"></span>
                                    </label>
                                    </td>
                                    <td>${codice}</td>
                                    <td class='name'>${name}</td>
                                </tr>
                            `);
                        });
                        regione.forEach(function(element) {
                            var id=element.id;
                            var name=element.name ;
                            $('.regione-row').append(`
                                <tr id=${id}>
                                    <td><label class="switch">
                                        <input class='toggle-switch' type="checkbox" checked>
                                        <span class="slider round"></span>
                                    </label>
                                    </td>
                                    <td class='name'>${name}</td>
                                </tr>
                            `);
                        });

                    })

                    .catch((err) => console.log(err));
        });


        $(document).on("click", "#three .product-select-btn", function(){

            var sku=$(this).attr('data-sku');
            var id=$(this).attr('data-id');

                    $('.sconto-form').append("<input type='hidden' name='products[]' value="+sku+">");
                    var price=$(this).attr('data-value');
                    var name=$(this).text();
                    var sconto=$('#percentual').val();
                    $('.product-row').append(`
                    <tr id=${id}>
                        <td>${sku}</td>
                        <td>${name}</td>
                        <td class='real-price'>${price} € </td>
                        <td class='range'><span class="badge text-bg-danger">3x2</span></td>
                        <td><button class='btn btn-danger deleteProduct_table' data-value='${sku}'><i class='fa fa-trash'></i></button></td>
                    </tr>
                    `);

                    var idArray = [];
                    $('.product-row tr').each(function() {
                    var rowId = $(this).attr('id');
                    idArray.push(rowId);
                    });
                    axios
                    .post("https://marigopharma.marigo.collaudo.biz/get-customers-by-product", {products: idArray})
                    .then((response) => {
                        console.log(response.data);
                        var customers = response.data.incustomers;
                        var agents= response.data.agents;
                        var regione= response.data.regione;
                        var count= response.data.count;
                        $('.user-count').html(count);
                        $('.users-sections').removeClass('d-none')
                        $('.users-row').html(`
                        <tr>
                            <th>
                                <label class="switch">
                                    <input class='toggle-switch-all-customers' type="checkbox" checked>
                                    <span class="slider round"></span>
                                </label>
                            </th>
                            <th>Seleziona/Deseleziona tutti</th>
                        </tr>`);
                        $('.agente-row').html(`
                        <tr>
                            <th>
                                <label class="switch">
                                    <input class='toggle-switch-all' type="checkbox" checked>
                                    <span class="slider round"></span>
                                </label>
                            </th>
                            <th>Seleziona/Deseleziona tutti</th>
                        </tr>`);
                        $('.date-row').removeClass('d-none');
                        $('.regione-row').html(`
                        <tr>
                            <th>
                                <label class="switch">
                                    <input class='toggle-switch-all' type="checkbox" checked>
                                    <span class="slider round"></span>
                                </label>
                            </th>
                            <th>Seleziona/Deseleziona tutti</th>
                        </tr>`);

                        $('#caratter').removeClass('d-none');
                        customers.forEach(function(element) {
                            var id=element.id;
                            var codice=element.codice;
                            var name=element.name;
                            $('.users-row').append(`
                                <tr id=${id}>
                                    <td><label class="switch">
                                        <input class='toggle-switch-user' type="checkbox" checked>
                                        <span class="slider round"></span>
                                    </label>
                                    </td>
                                    <td class='codice'>${codice}</td>
                                    <td class='name'>${name}</td>
                                </tr>
                            `);
                        });

                        agents.forEach(function(element) {
                            var id=element.id;
                            var codice=element.codice;
                            var name=element.nome + ' ' + element.cognome;
                            $('.agente-row').append(`
                                <tr id=${id}>
                                    <td><label class="switch">
                                        <input class='toggle-switch' type="checkbox" checked>
                                        <span class="slider round"></span>
                                    </label>
                                    </td>
                                    <td>${codice}</td>
                                    <td class='name'>${name}</td>
                                </tr>
                            `);
                        });
                        regione.forEach(function(element) {
                            var id=element.id;
                            var name=element.name ;
                            $('.regione-row').append(`
                                <tr id=${id}>
                                    <td><label class="switch">
                                        <input class='toggle-switch' type="checkbox" checked>
                                        <span class="slider round"></span>
                                    </label>
                                    </td>
                                    <td class='name'>${name}</td>
                                </tr>
                            `);
                        });

                    })

                    .catch((err) => console.log(err));


        });

        $(document).on("click", "#collegati .product-select-btn", function(){
            var sku=$(this).attr('data-sku');
            var id=$(this).attr('data-id');

            $('.sconto-form').append("<input type='hidden' name='products[]' value="+sku+">");
            var price=$(this).attr('data-value');
            var name=$(this).text();
            var sconto=$('#percentual').val();
            $('.product-row').append(`
            <tr id=${id}>
                <td>${sku}</td>
                <td>${name}</td>
                <td class='real-price'>${price} € </td>
                <td class='collegati'>
                    <div class="div-select-product-in-collegati" style='position:relative'>
                        <div class="box-search-advance product" style="min-width:310px;">
                            <input type="text" class="form-control next-input textbox-advancesearch product product-search-realtime-in-collegati" placeholder="Cerca prodotto" aria-invalid="false">
                            <input type="hidden" name='current-page' class="product-search-current-page">
                        </div>
                        <div class="panel panel-default d-none">
                            <div class="panel-body">
                                <div class="list-search-data">
                                    <ul class="clearfix">
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td><button class='btn btn-danger deleteProduct_table' data-value='${id}'><i class='fa fa-trash'></i></button></td>
            </tr>
            `);


        });



        $(document).on("click", "#quantita .product-select-btn", function(){
            var sku=$(this).attr('data-sku');
            var id=$(this).attr('data-id');

            $('.sconto-form').append("<input type='hidden' name='products[]' value="+sku+">");
            var price=$(this).attr('data-value');
            var name=$(this).text();
            var sconto=$('#percentual').val();
            $('.product-row').append(`
            <tr id=${id}>
                <td>${sku}</td>
                <td>${name}</td>
                <td class='real-price'>${price} € </td>
                <td class='quantita'>
                    <input type="text" class="form-control" placeholder="quantita" aria-invalid="false">
                </td>
                <td class='quantita-percentage'>
                    <input type="text" class="form-control" placeholder="percentuale" aria-invalid="false">
                </td>
                <td><button class='btn btn-danger deleteProduct_table' data-value='${sku}'><i class='fa fa-trash'></i></button></td>
            </tr>
            `);

            var idArray = [];
            $('.product-row tr').each(function() {
            var rowId = $(this).attr('id');
            idArray.push(rowId);
            });
            axios
            .post("https://marigopharma.marigo.collaudo.biz/get-customers-by-product", {products: idArray})
            .then((response) => {
                console.log(response.data);
                var customers = response.data.incustomers;
                var agents= response.data.agents;
                var regione= response.data.regione;
                var count= response.data.count;
                $('.user-count').html(count);
                $('.users-sections').removeClass('d-none')
                $('.users-row').html(`
                <tr>
                    <th>
                        <label class="switch">
                            <input class='toggle-switch-all-customers' type="checkbox" checked>
                            <span class="slider round"></span>
                        </label>
                    </th>
                </tr>`);
                $('.agente-row').html(`
                <tr>
                    <th>
                        <label class="switch">
                            <input class='toggle-switch-all' type="checkbox" checked>
                            <span class="slider round"></span>
                        </label>
                    </th>
                </tr>`);
                $('.date-row').removeClass('d-none');
                $('.regione-row').html(`
                <tr>
                    <th>
                        <label class="switch">
                            <input class='toggle-switch-all' type="checkbox" checked>
                            <span class="slider round"></span>
                        </label>
                    </th>
                </tr>`);

                $('#caratter').removeClass('d-none');
                customers.forEach(function(element) {
                    var id=element.id;
                    var codice=element.codice;
                    var name=element.name;
                    $('.users-row').append(`
                        <tr id=${id}>
                            <td><label class="switch">
                                <input class='toggle-switch-user' type="checkbox" checked>
                                <span class="slider round"></span>
                            </label>
                            </td>
                            <td class='codice'>${codice}</td>
                            <td class='name'>${name}</td>
                        </tr>
                    `);
                });

                agents.forEach(function(element) {
                    var id=element.id;
                    var codice=element.codice;
                    var name=element.nome + ' ' + element.cognome;
                    $('.agente-row').append(`
                        <tr id=${id}>
                            <td><label class="switch">
                                <input class='toggle-switch' type="checkbox" checked>
                                <span class="slider round"></span>
                            </label>
                            </td>
                            <td>${codice}</td>
                            <td class='name'>${name}</td>
                        </tr>
                    `);
                });
                regione.forEach(function(element) {
                    var id=element.id;
                    var name=element.name ;
                    $('.regione-row').append(`
                        <tr id=${id}>
                            <td><label class="switch">
                                <input class='toggle-switch' type="checkbox" checked>
                                <span class="slider round"></span>
                            </label>
                            </td>
                            <td class='name'>${name}</td>
                        </tr>
                    `);
                });


            })

            .catch((err) => console.log(err));


        });


        $(document).on('keyup','#percentual',function(){
            var percentual=parseFloat($(this).val());

            $("td.real-price").each(function(){
            var str=$(this).text();
            console.log(str)
            str = str.replace('/€/g','');
            str = parseFloat(str);
            console.log(str,percentual);
            $(this).closest('tr').find('td.offer-price').html( (str * (100-percentual))/100 + '€');
            })


        });

        $(document).on('click','.deleteProduct_table',function(){
            $('.product-search-realtime').removeAttr('disabled');
            var sku=$(this).attr('data-value');

            console.log(sku);
            $("input[value="+sku+"]").remove();
            $("tr#"+sku).remove();


            var rowCount = $('.product-row tr').length; // Initial number of rows

            if(rowCount>0){

                var idArray = [];
                $('.product-row tr').each(function() {
                var rowId = $(this).attr('id');
                idArray.push(rowId);
                });
                axios
                    .post("https://marigopharma.marigo.collaudo.biz/get-customers-by-consumabili", {consumabili: idArray})
                    .then((response) => {
                        console.log(response.data);
                        var customers = response.data.incustomers;
                        var agents= response.data.agents;
                        var regione= response.data.regione;
                        var count= response.data.count;
                        $('.user-count').html(count);
                        $('.users-sections').removeClass('d-none')
                        $('.users-row').html(`
                        <tr>
                            <th>
                                <label class="switch">
                                    <input class='toggle-switch-all-customers' type="checkbox" checked>
                                    <span class="slider round"></span>
                                </label>
                            </th>
                            <th>Seleziona/Deseleziona tutti</th>
                        </tr>`);
                        $('.agente-row').html(`
                        <tr>
                            <th>
                                <label class="switch">
                                    <input class='toggle-switch-all' type="checkbox" checked>
                                    <span class="slider round"></span>
                                </label>
                            </th>
                            <th>Seleziona/Deseleziona tutti</th>
                        </tr>`);
                        $('.date-row').removeClass('d-none');
                        $('.regione-row').html(`
                        <tr>
                            <th>
                                <label class="switch">
                                    <input class='toggle-switch-all' type="checkbox" checked>
                                    <span class="slider round"></span>
                                </label>
                            </th>
                            <th>Seleziona/Deseleziona tutti</th>
                        </tr>`);

                        $('#caratter').removeClass('d-none');
                        customers.forEach(function(element) {
                            var id=element.id;
                            var codice=element.codice;
                            var name=element.name;
                            $('.users-row').append(`
                                <tr id=${id}>
                                    <td><label class="switch">
                                        <input class='toggle-switch-user' type="checkbox" checked>
                                        <span class="slider round"></span>
                                    </label>
                                    </td>
                                    <td>${codice}</td>
                                    <td>${name}</td>
                                </tr>
                            `);
                        });


                        agents.forEach(function(element) {
                            var id=element.id;
                            var codice=element.codice;
                            var name=element.nome + ' ' + element.cognome;
                            $('.agente-row').append(`
                                <tr id=${id}>
                                    <td><label class="switch">
                                        <input class='toggle-switch' type="checkbox" checked>
                                        <span class="slider round"></span>
                                    </label>
                                    </td>
                                    <td>${codice}</td>
                                    <td class='name'>${name}</td>
                                </tr>
                            `);
                        });
                        regione.forEach(function(element) {
                            var id=element.id;
                            var name=element.name ;
                            $('.regione-row').append(`
                                <tr id=${id}>
                                    <td><label class="switch">
                                        <input class='toggle-switch' type="checkbox" checked>
                                        <span class="slider round"></span>
                                    </label>
                                    </td>
                                    <td class='name'>${name}</td>
                                </tr>
                            `);
                        });

                    })

                    .catch((err) => console.log(err));





            }else{

                $('.users-sections').addClass('d-none')
                $('.users-row').html('');
                $('.agente-row').html('');
                $('.regione-row').html('');

            }

        });









        $(document).on("change","#offerType",function(){
            $(this).prop('disabled',true);
            $('#offer_type_hidden').val($(this).val())
            switch ($(this).val()) {
                case '1':
                $("#sconto").removeClass('d-none');
                $("#scontorange").addClass('d-none');
                $("#fisso").addClass('d-none');
                $("#three").addClass('d-none');
                $("#collegati").addClass('d-none');
                $("#quantita").addClass('d-none');
                break;

                case '2':
                $("#scontorange").removeClass('d-none');
                $("#three").addClass('d-none');
                $("#fisso").addClass('d-none');
                $("#sconto").addClass('d-none');
                $("#collegati").addClass('d-none');
                $("#quantita").addClass('d-none');
                break;

                case '3':
                $("#fisso").removeClass('d-none');
                $("#scontorange").addClass('d-none');
                $("#collegati").addClass('d-none');
                $("#sconto").addClass('d-none');
                $("#three").addClass('d-none');
                $("#quantita").addClass('d-none');
                break;

                case '4':
                $("#three").removeClass('d-none');
                $("#scontorange").addClass('d-none');
                $("#fisso").addClass('d-none');
                $("#sconto").addClass('d-none');
                $("#quantita").addClass('d-none');
                $("#collegati").addClass('d-none');
                break;

                case '5':
                $("#three").addClass('d-none');

                $("#scontorange").addClass('d-none');
                $("#fisso").addClass('d-none');
                $("#sconto").addClass('d-none');
                $("#quantita").addClass('d-none');
                $("#collegati").removeClass('d-none');
                break;

                case '6':
                $("#three").addClass('d-none');
                $("#quantita").removeClass('d-none');
                $("#scontorange").addClass('d-none');
                $("#fisso").addClass('d-none');
                $("#sconto").addClass('d-none');
                $("#collegati").addClass('d-none');
                break;
            }
        });



        $(document).on("click",".discount-check-submit",function(){

            var offerType= $('#offer_type_hidden').val();
            var dataArray = [];
            var product_ids=[];
            $(".product-row tr").each(function() {
                var id = $(this).attr("id");
                var collegati = $(this).find('.collegati p').data('gift-product-id');
                var quantita= $(this).find('.quantita input').val();
                console.log("collegati:", collegati);
                console.log("quantita:", quantita);
                if(offerType==6){
                    var price=$(this).find(".real-price").text().trim();
                    price = parseFloat(price.replace(/[^\d.]/g, ""));
                    var percentage = $(this).find(".quantita-percentage input").val();
                    var offerPrice= (price*(100-percentage))/100;
                }
                else if(offerType==3)
                {
                    var offerPrice=$(this).find('.fixed-price input').val();
                    console.log(offerPrice);

                }else{
                    var offerPrice = $(this).find(".offer-price").text().trim();
                    offerPrice = parseFloat(offerPrice.replace(/[^\d.]/g, ""));
                }
                product_ids.push(id);
                dataArray.push({ id: id, price: offerPrice, collegati:collegati, quantita:quantita });
            });

            var checkedUserIds=[];
            $('.users-row tr').each(function() {
                var id = $(this).attr('id');
                var checkbox = $(this).find('.toggle-switch-user');
                if (checkbox.prop('checked')) {
                    checkedUserIds.push(id);
                }
            });
            var collegati= $()


            console.log(dataArray,product_ids,checkedUserIds);

            const startDate = document.getElementById('start_date').value;


            axios.post("https://marigopharma.marigo.collaudo.biz/admin/ecommerce/offerte/checkProductHasActiveOffer", {product_ids: product_ids,date:startDate})
            .then((response) => {

                if(response.data){
                    console.log(response.data);
                    Swal.fire({
                        title: 'Attenzione!',
                        width: '75%',
                        text: response.data.message,
                        icon: 'info',
                        confirmButtonText: 'Okay'
                    });
                }else{
                    axios.post("https://marigopharma.marigo.collaudo.biz/checkIfBetter", {consumabili: dataArray, customers:checkedUserIds,offer_type:offerType})
                    .then((response) => {
                        var res=response.data;
                        var html=`
                        <div class="container">
                            <div class="row">

                        `;
                        res.forEach(function(data){
                            var productId=data.product.id;
                            var productName=data.product.name;
                            var productCodice=data.product.sku;
                            var productPrice=parseFloat(data.product.price);
                            var offerPrice=parseFloat(data.offer_price);
                            var quantita=data.quantita;
                            var gift_product=data.gift_product;
                            var flag_three=data.flag_three;
                            var percentage = parseInt(((offerPrice - productPrice) / Math.abs(productPrice)) * 100);
                            if(flag_three !=null){
                                html+=`
                                <div class="col-12">
                                    <table class="table mt-3 mb-3 xsmall" id="${productId}">
                                        <thead>
                                            <tr class='product-checked-row'>
                                                <td>${productCodice}</td>
                                                <td>${productName} &nbsp <span class='badge badge-danger'>3x2</span> </td>
                                                <td>${productPrice}€</td>
                                            </tr>
                                        </thead>
                                    <tbody>
                                `;
                            }else if(quantita !=null){
                                html+=`
                                <div class="col-12">
                                    <table class="table mt-3 mb-3 xsmall" id="${productId}">
                                        <thead>
                                            <tr class='product-checked-row'>
                                                <td>${productCodice}</td>
                                                <td>${productName}</td>
                                                <td>${productPrice}€</td>
                                                <td>se prendono ${quantita}</td>
                                                <td>${offerPrice}€ &nbsp <span class="badge badge-danger">${percentage}%</span></td>
                                            </tr>
                                        </thead>
                                    <tbody>
                                `;
                            }else if(gift_product !=null){

                                html+=`
                                <div class="col-12">
                                    <table class="table mt-3 mb-3 xsmall" id="${productId}">
                                        <thead>
                                            <tr class='product-checked-row'>
                                                <td>${productCodice}</td>
                                                <td>se prendono ${productName}</td>
                                                <td>riceveranno ${gift_product.name} come gratis</td>
                                            </tr>
                                        </thead>
                                    <tbody>
                                `;

                            }else{
                                html+=`
                                <div class="col-12">
                                    <table class="table mt-3 mb-3 xsmall" id="${productId}">
                                        <thead>
                                            <tr class='product-checked-row'>
                                                <td>${productCodice}</td>
                                                <td>${productName}</td>
                                                <td>${productPrice}€</td>
                                                <td>${offerPrice}€ &nbsp <span class="badge badge-danger">${percentage}%</span></td>
                                            </tr>
                                        </thead>
                                    <tbody>
                                `;
                            }

                            var customers=data.customers;
                            if(customers.length>0){
                                customers.forEach(function(customer){
                                    var customerCodice=customer.codice;
                                    var customerName=customer.name;
                                    html+=`
                                        <tr>
                                            <td>${customerCodice}</td>
                                            <td colspan='4' >${customerName}</td>
                                        </tr>
                                    `;
                                })
                            }else{
                                html+=`
                                    <div class="alert alert-warning" role="alert">
                                        tutti i clienti di questo prodotto stanno pagando meno di questo si prega di modificare l'offerta o lasciarla così.
                                    </div>
                                `;
                            }

                            html+=`
                                    </tbody>
                                </table>
                            </div>
                            `;

                        });
                        html+=`
                            </div>
                        </div>
                        `;

                        Swal.fire({
                            title: 'Riepilogo Offerta',
                            html:html,
                            width: '75%',
                            focusConfirm: false,
                            showCloseButton: true,
                            showCancelButton:true,
                            confirmButtonText: 'Salvare dati',
                            cancelButtonText: 'Cancellare',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const offerName = document.getElementById('discount-name').value;
                                const startDate = document.getElementById('start_date').value;
                                const ExpiringDate = document.getElementById('expiring_date').value;
                                const offerType = document.getElementById('offer_type_hidden').value;
                                const formData = {
                                offer_name: offerName,
                                start_date: startDate,
                                expiring_date:ExpiringDate,
                                offer_type: offerType,
                                offer_details:res
                              };
                              axios.post('https://marigopharma.marigo.collaudo.biz/saveOffer', formData)
                                .then((response) => {
                                  // Handle the response from the server
                                  window.location.href = 'https://marigopharma.marigo.collaudo.biz/admin/ecommerce/offerte';
                                })
                                .catch((error) => {
                                  // Handle any errors that occurred during the request
                                  Swal.fire('Error', 'An error occurred while submitting the form.', 'error');
                                });
                            }
                          });

                    })
                    .catch((error)=>{console.log(error)})
                }
            });


        })

        $(document).on('change','.toggle-switch-all',function(evt){
            var isChecked = $(this).is(':checked');
            var currentTable = $(this).closest('table');
            currentTable.find('.toggle-switch').prop('checked', isChecked);

            var fromDate = $('.fromDate').val();
            var toDate = $('.toDate').val();
            sendFilterRequest(fromDate, toDate);
        });
        $(document).on('change','.toggle-switch-all-customers',function(evt){
            var isChecked = $(this).is(':checked');
            var currentTable = $(this).closest('table');
            currentTable.find('.toggle-switch-user').prop('checked', isChecked);
        });


    });

</script>

    @php
        Assets::addScripts(['form-validation']);
    @endphp
    {!! JsValidator::formRequest(\Botble\Ecommerce\Http\Requests\DiscountRequest::class) !!}
@endpush
