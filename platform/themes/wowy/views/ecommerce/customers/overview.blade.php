@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')
    <div class="card">
        <div class="card-header">
        {{--  <h5 class="mb-0">{{ __('Hello :name!', ['name' => request()->user('customer')->name]) }} </h5>  --}}
        <h5 class="mb-0">{{ __('Benvenuto nella tua area riservata!') }} </h5>

    </div>
        <div class="card-body">
            <p>
                {!! BaseHelper::clean(__('Da qui puoi visualizzare il <a href=":profile">tuo profilo </a>',[
                    'profile' => route('customer.edit-account'),
                ])) !!},

                {!! BaseHelper::clean(__('l`elenco dei<a href=":order"> tuoi ordini </a> la lista dei <a href=":wishlist">tuoi prodotti preferiti</a> e <a href=":password">modificare la tua password</a>', [
                    'password' => route('customer.change-password'),
                    'order' => route('customer.orders'),
                    'wishlist'=> route('public.wishlist'),
                ])) !!}
            </p>
        </div>
    </div>
@endsection
