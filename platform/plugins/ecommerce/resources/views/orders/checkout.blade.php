@extends('plugins/ecommerce::orders.master')
@section('title')
    {{ __('Checkout') }}
@stop
@section('content')
    <div id="pdfModal" class="modal"
        style="  position: fixed;
z-index: 1;
left: 0;
top: 0;
width: 100%;
height: 100%;
overflow: auto;
background-color: rgba(0,0,0,0.4);">
        <div class="modal-content"
            style=" position: relative;
    margin: 15% auto;
    padding: 20px;
    width: 80%;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
    background-color: #fefefe;">
            <span class="close"
                style="  color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;">&times;
            </span>
            <div id="pdf-container" style="display: flex;
      flex-direction: column;
      align-items: center;"></div>

        </div>
    </div>

    @if (Cart::instance('cart')->count() > 0)
        @include('plugins/payment::partials.header')

        {!! Form::open([
            'route' => ['public.checkout.process', $token],
            'class' => 'checkout-form payment-checkout-form',
            'id' => 'checkout-form',
        ]) !!}

        <input type="hidden" name="checkout-token" id="checkout-token" value="{{ $token }}">

        <div class="container" id="main-checkout-product-info">
            <div class="row">


                <div class="col-lg-12 col-md-6 ">
                    <div class="d-none d-sm-block">
                        @include('plugins/ecommerce::orders.partials.logo')
                    </div>
                    <div class="d-block d-sm-none">
                        @include('plugins/ecommerce::orders.partials.logo')
                    </div>
                    <div id="cart-item" class="position-relative">

                        <div class="payment-info-loading" style="display: none;">
                            <div class="payment-info-loading-content">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>

                        <h5 class="checkout-payment-title">{{ __('Riepilogo Ordine') }}</h5>
                        <h6>Verifica il tuo ordine in basso e poi clicca su “Invia l’ordine” per inviarlo.</h6>
                        <div id="cart-item" class="position-relative">

                            <div class="payment-info-loading" style="display: none;">
                                <div class="payment-info-loading-content">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </div>

                            {{-- {!! apply_filters(RENDER_PRODUCTS_IN_CHECKOUT_PAGE, $products) !!} --}}
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Codice</th>
                                        <th>Nome</th>
                                        <th>Prezzo</th>
                                        <th>Quantità</th>
                                        <th>Totale parziale</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        @php
                                            $userid = request()->user('customer')->id;
                                            $pricelist = DB::connection('mysql')->select(
                                                "select * from ec_pricelist where product_id=$product->id and customer_id=$userid",
                                            );
                                        @endphp
                                        <tr>
                                            <td>{{ $product->sku }}</td>
                                            <td>{{ $product->cartItem->name }}</td>
                                            <td>
                                                @if (!isset($pricelist))
                                                    {{ format_price($pricelist->final_price) }}
                                                @else
                                                    {{ format_price($product->cartItem->price) }}
                                                @endif
                                            </td>
                                            <td>{{ $product->cartItem->qty }}</td>
                                            <td>
                                                {{ format_price($product->cartItem->price * $product->cartItem->qty) }}

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="mt-2 p-2">
                                <div class="row">
                                    <div class="col-6">
                                        <p>Subtotale IVA Esclusa:</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="price-text sub-total-text text-end">
                                            {{ format_price(Cart::instance('cart')->rawSubTotal()) }} </p>
                                    </div>
                                </div>
                                @if (session('applied_coupon_code'))
                                    <div class="row coupon-information">
                                        <div class="col-6">
                                            <p>{{ __('Coupon code') }}:</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="price-text coupon-code-text"> {{ session('applied_coupon_code') }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                                @if ($couponDiscountAmount > 0)
                                    <div class="row price discount-amount">
                                        <div class="col-6">
                                            <p>{{ __('Coupon code discount amount') }}:</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="price-text total-discount-amount-text">
                                                {{ format_price($couponDiscountAmount) }} </p>
                                        </div>
                                    </div>
                                @endif
                                @if ($promotionDiscountAmount > 0)
                                    <div class="row">
                                        <div class="col-6">
                                            <p>{{ __('Promotion discount amount') }}:</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="price-text"> {{ format_price($promotionDiscountAmount) }} </p>
                                        </div>
                                    </div>
                                @endif
                                @if (!empty($shipping) && Arr::get($sessionCheckoutData, 'is_available_shipping', true))
                                    <div class="row">
                                        <div class="col-6">
                                            <p>{{ __('Contributo spese di spedizione e imballaggio') }}:</p>
                                        </div>
                                        <div class="col-6 float-end">
                                            <p class="price-text shipping-price-text">
                                                {{ format_price(floatval(Session::get('shippingAmount'))) }}</p>
                                        </div>
                                    </div>
                                @endif


                                @if (EcommerceHelper::isTaxEnabled())
                                    <div class="row">
                                        <div class="col-6">
                                            <p>{{ __('Tax') }}:</p>
                                        </div>
                                        <div class="col-6 float-end">
                                            <p class="price-text tax-price-text">
                                                {{ format_price(Cart::instance('cart')->rawTax()) }}</p>
                                        </div>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-6">
                                        <p><strong>Totale IVA Inclusa </strong>:</p>
                                    </div>
                                    <div class="col-6 float-end">
                                        <p class="total-text raw-total-text"
                                            data-price="{{ format_price(Cart::instance('cart')->rawTotal(), null, true) }}">
                                            {{ $promotionDiscountAmount + $couponDiscountAmount - floatval(Session::get('shippingAmount')) > Cart::instance('cart')->rawSubTotal() + Cart::instance('cart')->rawTax() ? format_price(0) : format_price(Cart::instance('cart')->rawSubTotal() + Cart::instance('cart')->rawTax() - $promotionDiscountAmount - $couponDiscountAmount + floatval(Session::get('shippingAmount'))) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-checkout">
                            @if ($isShowAddressForm)
                                <div>
                                    <input type="hidden" value="{{ route('public.checkout.save-information', $token) }}"
                                        id="save-shipping-information-url">
                                    @include(
                                        'plugins/ecommerce::orders.partials.address-form',
                                        compact('sessionCheckoutData'))
                                </div>
                                <br>
                            @endif

                            @if (EcommerceHelper::isBillingAddressEnabled())
                                <div>
                                    <h5 class="checkout-payment-title">{{ __('Billing information') }}</h5>
                                    @include(
                                        'plugins/ecommerce::orders.partials.billing-address-form',
                                        compact('sessionCheckoutData'))
                                </div>
                                {{-- <br> --}}
                            @endif

                            @if (!is_plugin_active('marketplace'))
                                @if (Arr::get($sessionCheckoutData, 'is_available_shipping', true))
                                    <div id="shipping-method-wrapper" style="display: none !important">
                                        <h5 class="checkout-payment-title">{{ __('Shipping method') }}</h5>
                                        <div class="shipping-info-loading" style="display: none;">
                                            <div class="shipping-info-loading-content">
                                                <i class="fas fa-spinner fa-spin"></i>
                                            </div>
                                        </div>
                                        @if (!empty($shipping))
                                            <div class="payment-checkout-form">
                                                <input type="hidden" name="shipping_option"
                                                    value="{{ old('shipping_option', $defaultShippingOption) }}">
                                                <ul class="list-group list_payment_method">
                                                    <input id="shipping-method-default-3" name="shipping_method"
                                                        class="magic-radio" checked="checked" data-option="3" type="radio"
                                                        value="default">
                                                </ul>

                                            </div>
                                        @else
                                            <p>{{ __('No shipping methods available!') }}</p>
                                        @endif
                                    </div>
                                    <br>
                                @endif
                            @endif

                            <div class="position-relative">
                                <div class="payment-info-loading" style="display: none;">
                                    <div class="payment-info-loading-content">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </div>
                                <h5 class="checkout-payment-title">{{ __('Payment method') }}</h5>
                                <input type="hidden" name="amount"
                                    value="{{ $promotionDiscountAmount + $couponDiscountAmount - floatval(Session::get('shippingAmount')) > Cart::instance('cart')->rawTotal() ? 0 : format_price(Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount + floatval(Session::get('shippingAmount')), null, true) }}">
                                <input type="hidden" name="currency"
                                    value="{{ strtoupper(get_application_currency()->title) }}">
                                {!! apply_filters(PAYMENT_FILTER_PAYMENT_PARAMETERS, null) !!}
                                <ul class="list-group list_payment_method">
                                    @php
                                        $selected = session('selected_payment_method');
                                        $default = \Botble\Payment\Supports\PaymentHelper::defaultPaymentMethod();
                                        $selecting = $selected ?: $default;
                                    @endphp

                                    {!! apply_filters(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, null, [
                                        'amount' =>
                                            $promotionDiscountAmount + $couponDiscountAmount - $shippingAmount > Cart::instance('cart')->rawTotal()
                                                ? 0
                                                : format_price(
                                                    Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount + $shippingAmount,
                                                    null,
                                                    true,
                                                ),
                                        'currency' => strtoupper(get_application_currency()->title),
                                        'name' => null,
                                        'selected' => $selected,
                                        'default' => $default,
                                        'selecting' => $selecting,
                                    ]) !!}

                                    @if (get_payment_setting('status', 'cod') == 1)
                                        <li class="list-group-item">
                                            <input class="magic-radio js_payment_method" type="radio"
                                                name="payment_method" id="payment_cod"
                                                @if ($selecting == \Botble\Payment\Enums\PaymentMethodEnum::COD) checked @endif value="cod"
                                                data-bs-toggle="collapse" data-bs-target=".payment_cod_wrap"
                                                data-parent=".list_payment_method">
                                            <label for="payment_cod"
                                                class="text-start">{{ setting('payment_cod_name', trans('plugins/payment::payment.payment_via_cod')) }}</label>
                                            <div class="payment_cod_wrap payment_collapse_wrap collapse @if ($selecting == \Botble\Payment\Enums\PaymentMethodEnum::COD) show @endif"
                                                style="padding: 15px 0;">
                                                {!! BaseHelper::clean(setting('payment_cod_description')) !!}

                                                @php $minimumOrderAmount = setting('payment_cod_minimum_amount', 0); @endphp
                                                @if ($minimumOrderAmount > Cart::instance('cart')->rawSubTotal())
                                                    <div class="alert alert-warning" style="margin-top: 15px;">
                                                        {{ __('Minimum order amount to use COD (Cash On Delivery) payment method is :amount, you need to buy more :more to place an order!', ['amount' => format_price($minimumOrderAmount), 'more' => format_price($minimumOrderAmount - Cart::instance('cart')->rawSubTotal())]) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </li>
                                    @endif

                                    @if (get_payment_setting('status', 'bank_transfer') == 1)
                                        <li class="list-group-item">
                                            <input class="magic-radio js_payment_method" type="radio"
                                                name="payment_method" id="payment_bank_transfer"
                                                @if ($selecting == \Botble\Payment\Enums\PaymentMethodEnum::BANK_TRANSFER) checked @endif value="bank_transfer"
                                                data-bs-toggle="collapse" data-bs-target=".payment_bank_transfer_wrap"
                                                data-parent=".list_payment_method">
                                            <label for="payment_bank_transfer"
                                                class="text-start">{{ setting('payment_bank_transfer_name', trans('plugins/payment::payment.payment_via_bank_transfer')) }}</label>
                                            <div class="payment_bank_transfer_wrap payment_collapse_wrap collapse @if ($selecting == \Botble\Payment\Enums\PaymentMethodEnum::BANK_TRANSFER) show @endif"
                                                style="padding: 15px 0;">
                                                {!! BaseHelper::clean(setting('payment_bank_transfer_description')) !!}
                                            </div>
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            <br>

                            @if (Session::get('note') != '')
                                <div class="form-group mb-3 @if ($errors->has('description')) has-error @endif">
                                    <label for="description" class="control-label">{{ __('Order notes') }}</label>

                                    <textarea disabled name="description" id="description" rows="3" class="form-control"
                                        placeholder="{{ __('Notes about your order, e.g. special notes for delivery.') }}">{{ Session::has('note') ? Session::get('note') : old('description') }}</textarea>
                                    {!! Form::error('description', $errors) !!}
                                </div>
                            @endif

                            @if (EcommerceHelper::getMinimumOrderAmount() > Cart::instance('cart')->rawSubTotal())
                                <div class="alert alert-warning">
                                    {{ __('Minimum order amount is :amount, you need to buy more :more to place an order!', ['amount' => format_price(EcommerceHelper::getMinimumOrderAmount()), 'more' => format_price(EcommerceHelper::getMinimumOrderAmount() - Cart::instance('cart')->rawSubTotal())]) }}
                                </div>
                            @endif

                            <div class="col-12">
                                <p>
                                    inviando l'ordine si dichiara di aver letto, compreso e accettato integralmente le <span
                                        class="text-primary condizioni" style="cursor: pointer"
                                        onclick="openModal()">condizioni generali di vendita</span><br>
                                </p>
                            </div>

                            <div class="row">
                                <div class="col-md-6 d-none d-md-block" style="line-height: 53px">
                                    <a href="{{ route('public.cart') }}"><i style="color:#005BA1"
                                            class="fas fa-long-arrow-alt-left"></i> <span
                                            class="d-inline-block back-to-cart"
                                            style="color:#005BA1">{{ __('Back to cart') }}</span></a>
                                </div>
                                <div class="col-md-6 checkout-button-group">
                                    @if (EcommerceHelper::isValidToProcessCheckout())
                                        <button type="submit"
                                            style='background-color:#51b448;
                                        border: none;
                                        border-radius: 50px;
                                        color: #fff;
                                        font-size: 15px;
                                        font-weight: 500;
                                        padding: 12px 40px;'class="btn payment-checkout-btn payment-checkout-btn-step float-end"
                                            data-processing-text="{{ __('In lavorazione. attendere prego...') }}"
                                            data-error-header="{{ __('Error') }}">
                                            {{ __("Invia l'ordine") }}
                                        </button>
                                    @else
                                        <span class="btn payment-checkout-btn-step float-end disabled"
                                            style="background:#51b448">
                                            {{ __("Invia l'ordine") }}
                                        </span>
                                    @endif
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}

            @include('plugins/payment::partials.footer')
        @else
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-warning my-5">
                            <span>{!! __('No products in cart. :link!', ['link' => Html::link(route('public.index'), __('Back to shopping'))]) !!}</span>
                        </div>
                    </div>
                </div>
            </div>
    @endif
@stop

@push('header')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.9.359/pdf.min.js"></script>

    <link rel="stylesheet" href="{{ asset('vendor/core/core/base/libraries/intl-tel-input/css/intlTelInput.min.css') }}">
    <style>
        #pdf-container canvas {
            display: block;
            margin-top: -30px;
            padding: 0;
        }
    </style>
@endpush

@push('footer')
    <script>
        // Get the modal
        var modal = document.getElementById("pdfModal");

        // Get the button that opens the modal
        var btn = document.querySelector(".condizioni");

        // Get the <span> element that closes the modal
        var span = document.querySelector(".close");

        // When the user clicks the button, open the modal
        function openModal() {
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        var url = 'https://marigopharma.marigo.collaudo.biz/storage/2124-condizioni-generali-di-vendita-webview.pdf';

        var pdfjsLib = window['pdfjs-dist/build/pdf'];

        // The workerSrc property shall be specified.
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.9.359/pdf.worker.min.js';

        var pdfDoc = null,
            scale = 1.5, // Adjust this value to zoom the PDF
            container = document.getElementById('pdf-container');

        function renderPage(num) {
            if (num > pdfDoc.numPages) {
                return; // All pages rendered
            }

            // Get page
            pdfDoc.getPage(num).then(function(page) {
                var viewport = page.getViewport({
                    scale: scale
                });
                var canvas = document.createElement('canvas');
                container.appendChild(canvas);
                var ctx = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                // Render the page into the canvas
                var renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                page.render(renderContext).promise.then(function() {
                    // Move to next page
                    renderPage(num + 1);
                });
            });
        }

        pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
            pdfDoc = pdfDoc_;
            renderPage(1);
        });
    </script>
    {{-- <script>$(".payment-checkout-btn-step").trigger( "click" );</script> --}}
    <script src="{{ asset('vendor/core/core/base/libraries/intl-tel-input/js/intlTelInput.min.js') }}"></script>
    <script src="{{ asset('vendor/core/core/base/js/phone-number-field.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
        integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush
