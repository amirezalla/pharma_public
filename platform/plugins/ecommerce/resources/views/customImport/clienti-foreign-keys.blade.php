
@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
@if(isset($usersUpdated))
<p>Users and all their foreign-keys updated successfully.</p>
@endif
@if(isset($regUpdated))
<p>Regions updated successfully.</p>
@endif

<a class="btn btn-info" href="https://marigopharma.marigo.collaudo.biz/admin/customers">Go back to Clienti</a>

@stop

