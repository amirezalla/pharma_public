@extends('plugins/ecommerce::orders.master')
@section('title')
    {{ __('Order successfully. Order number :id', ['id' => $order->code]) }}
@stop
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-6  ">
                @include('plugins/ecommerce::orders.partials.logo')

                <div class="thank-you">
                    <div class="d-block">
                        <h3 class="thank-you-sentence" style="position:relative">
                            <i class="fa fa-check-circle" aria-hidden="true" style="font-size:5em;color:#56b448"></i>
                            <div style="position: absolute;top:0px;font-size:larger;left:122px">
                                <h3>{{ __('Your order is successfully placed') }}</h3>
{{--                                <p>{{ __('Nel caso in cui tu ne avessi necessità, hai la possibilità di apportare modifiche al tuo ordine per i prossimi 30 minuti.') }}</p>--}}
                            </div>

                        </h3>
                    </div>
                </div>

                @include('plugins/ecommerce::orders.thank-you.customer-info', compact('order'))

{{--                <a href="{{ route('public.index') }}" class="btn payment-checkout-btn"> {{ __('Continue shopping') }} </a>--}}

                <a href="https://marigopharma.marigo.collaudo.biz/customer/orders" class="btn payment-checkout-btn"style="background:#56b448"> {{ __('I tuoi ordini') }} </a>

            </div>

        </div>
    </div>
@stop
