@extends(BaseHelper::getAdminMasterLayoutTemplate())
@php
    use Botble\Ecommerce\Models\offerType;
    $lastRecord= offerType::latest()->first();
@endphp
@section('content')
<form action="{{ route("admin.ecommerce.offertype.add") }}" method="post">

    @csrf

<div class="container carousel-s">
    <div class="row">
                @if (isset($offertype))
                <div class="alert alert-success" style="margin-bottom: 20px" role="alert">
                    Offer Priority has been changed and {{ $offertype->min_price }}€ < prices <{{ $offertype->max_price }}€ !
                </div>
                @endif
                <div class="col-12">
                    <h1>offre la gestione del carosello</h1>
                    <p>Mostrerà questi in base al loro ordine</p>
                </div>
                <div class="col-6">
                    <ul id="draggableList">
                        <li class="draggableListItem" draggable="true">{{ $lastRecord->First }}</li>
                        <li class="draggableListItem" draggable="true">{{ $lastRecord->Second }}</li>
                        <li class="draggableListItem" draggable="true">{{ $lastRecord->Third }}</li>
                    </ul>
                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="expiry_limit">limite per la data di scadenza</label>
                            <select name="expiry_limit" class="form-select" aria-label="Default select example">
                                <option @if($lastRecord->expiry_limit==1) selected @endif value="1">questo mese</option>
                                <option @if($lastRecord->expiry_limit==3) selected @endif value="3">fino ai prossimi tre mesi</option>
                                <option @if($lastRecord->expiry_limit==6) selected @endif value="6">fino ai prossimi sei mesi</option>
                                <option @if($lastRecord->expiry_limit==12) selected @endif value="12">fino alla fine dell'anno</option>
                                <option @if($lastRecord->expiry_limit==24) selected @endif value="24">fino alla fine del prossimo anno</option>
                              </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="precedenza">Acquistati in precedenza</label>
                            <select name="precedenza" class="form-select" aria-label="Default select example">
                                <option @if($lastRecord->acquistati_in_precedenza==1) selected @endif value="1">questo mese</option>
                                <option @if($lastRecord->acquistati_in_precedenza==3) selected @endif value="3">tre mesi fa</option>
                                <option @if($lastRecord->acquistati_in_precedenza==6) selected @endif value="6">sei mesi fa</option>
                                <option @if($lastRecord->acquistati_in_precedenza==12) selected @endif value="12">un anno fa</option>
                              </select>
                        </div>
                    </div>
                </div>
                <div class="hidden-place d-none">
                    <input type="hidden"  name='orders[]' value="{{ $lastRecord->First }}">
                    <input type="hidden"  name='orders[]' value="{{ $lastRecord->Second }}">
                    <input type="hidden"  name='orders[]' value="{{ $lastRecord->Third }}">
                </div>
                <div class="col-6">
                        <div class="row">
                            <div class="col-6">
                                <label for="minprice">Min Price</label>
                                <input type="text" name="min_price" class="form-control" value="{{ $lastRecord->min_price }}" id="minprice" placeholder="Min price">
                            </div>
                            <div class="col-6">
                                <label for="maxprice">Max Price</label>
                                <input type="text" name="max_price" class="form-control" value="{{ $lastRecord->max_price }}" id="maxprice" placeholder="Max Price">
                            </div>
                        </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="show">numero di elementi da visualizzare</label>
                            <select name="show" class="form-select" aria-label="Default select example">
                                <option @if($lastRecord->show==8) selected @endif value="8">8</option>
                                <option @if($lastRecord->show==16) selected @endif value="16">16</option>
                                <option @if($lastRecord->show==24) selected @endif value="24">24</option>
                                <option @if($lastRecord->show==32) selected @endif value="32">32</option>
                                <option @if($lastRecord->show==40) selected @endif value="40">40</option>
                                <option @if($lastRecord->show==48) selected @endif value="48">48</option>
                              </select>
                        </div>

                    </div>

        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12">
            <input type="submit" class="btn btn-primary" value="Save Data">
        </div>
    </div>
    </div>


        </div>
    </div>
</div>
</form>


<script>
    var draggableList = document.getElementById('draggableList');
        var draggingElement = null;

        // Add event listeners to each list item
        var items = draggableList.getElementsByTagName('li');
        for (var i = 0; i < items.length; i++) {
            items[i].addEventListener('dragstart', handleDragStart, false);
            items[i].addEventListener('dragover', handleDragOver, false);
            items[i].addEventListener('drop', handleDrop, false);
            items[i].addEventListener('dragend', handleDragEnd, false);
        }

        function handleDragStart(e) {
            draggingElement = e.target;
            e.dataTransfer.effectAllowed = 'move';
        }

        function handleDragOver(e) {
            if (e.preventDefault) {
                e.preventDefault();
            }
            e.dataTransfer.dropEffect = 'move';
            return false;
        }

        function handleDrop(e) {
            if (e.stopPropagation) {
                e.stopPropagation();
            }
            if (draggingElement !== this) {
                draggableList.removeChild(draggingElement);
                draggableList.insertBefore(draggingElement, this);
            }
            return false;
        }

        function handleDragEnd(e) {
            // Reset the dragging element
            draggingElement = null;
            var orders = readOrder();

            $('.hidden-place').html('');
            $('.hidden-place').append(`
                <input type='hidden' name='orders[]' value=${orders[0]}>
                <input type='hidden' name='orders[]' value=${orders[1]}>
                <input type='hidden' name='orders[]' value=${orders[2]}>
            `)
        }

        function readOrder(){
            var order=[];
            var items = draggableList.getElementsByTagName('li');
            for (var i = 0; i < items.length; i++) {
                order.push(items[i].textContent);
            }
            return order;
        }
</script>

@stop
