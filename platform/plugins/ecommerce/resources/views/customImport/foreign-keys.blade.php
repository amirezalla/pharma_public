
@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
@if(isset($taxes))
<p>Taxes updated successfully.</p>
@endif
@if(isset($brands))
<p>Brands updated successfully.</p>
@endif
@if(isset($linee))
<p>Lines updated successfully.</p>
@endif

@if(isset($expiring))
<p>Expiring datas updated successfully.</p>
@endif

@if(isset($pricelist))
<p>Price list updated successfully.</p>
@endif
@if(isset($strumentiUpdated))
<p>Strumenti list updated successfully.</p>
@endif
@if(isset($UpdatedProducts))
<p>Consumabili list updated successfully.</p>
@endif


<a class="btn btn-info" href="https://marigopharma.marigo.collaudo.biz/admin/ecommerce/products">Go back to products</a>

@stop

