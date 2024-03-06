@php
    use Botble\Ecommerce\Models\OffersDetail;
    use Botble\Ecommerce\Models\Offers;
    use Botble\Ecommerce\Models\Product;
@endphp

@if (Cart::instance('cart')->count() > 0)
    @php
        $products = get_products([
            'condition' => [
                [
                    'ec_products.id',
                    'IN',
                    Cart::instance('cart')
                        ->content()
                        ->pluck('id')
                        ->all(),
                ],
            ],
            'with' => ['slugable'],
        ]);
    @endphp

    @if (count($products))

        @php

            $cartTotal = Cart::instance('cart')->rawSubTotal();
            $cartIva = Cart::instance('cart')->rawTax();
            if (request()->user('customer')) {
                $userid = request()->user('customer')->id;
                if ($userid == 11 || $userid == 13) {
                    $userid = 2621;
                }
                foreach (Cart::instance('cart')->content() as $key => $cartItem) {
                    $product = $products->find($cartItem->id);
                    $offerDetail = OffersDetail::where('product_id', $cartItem->id)
                        ->where('customer_id', $userid)
                        ->where('status', 'active')
                        ->first();
                    if (auth('customer')->user()) {
                        if ($offerDetail) {
                            $offer = Offers::find($offerDetail->offer_id);
                            if ($offer) {
                                $offerType = $offer->offer_type;
                                if ($offerType == 4 && $cartItem->qty >= 3) {
                                    $cartTotal = $cartTotal - $cartItem->price * floor($cartItem->qty / 3);
                                    $tax = str_replace('€', '', $cartItem->tax());
                                    $tax = str_replace(',', '.', $tax);
                                    $cartIva = $cartIva - floatval($tax) * floor($cartItem->qty / 3);
                                }
                                if ($offerType == 6 && $cartItem->qty >= $offerDetail->quantity) {
                                    $cartIva = $cartIva - floatval($cartItem->tax()) * $cartItem->qty + (($product->tax->percentage * $offerDetail->product_price) / 100) * $cartItem->qty;
                                    $cartTotal = $cartTotal - $cartItem->price * $cartItem->qty + $offerDetail->product_price * $cartItem->qty;
                                }
                            }
                        }
                    }
                }
            }

        @endphp
        <ul>
            @foreach (Cart::instance('cart')->content() as $key => $cartItem)
                @php
                    $product = $products->find($cartItem->id);

                    if (request()->user('customer')) {
                        $userid = request()->user('customer')->id;
                        if ($userid == 11 || $userid == 13) {
                            $userid = 2621;
                        }

                        $offerDetail = OffersDetail::where('product_id', $product->id)
                            ->where('customer_id', $userid)
                            ->where('status', 'active')
                            ->first();
                        if ($offerDetail) {
                            $offer = Offers::find($offerDetail->offer_id);
                            if ($offer) {
                                $offerType = $offer->offer_type;
                            }
                        }
                    }

                @endphp

                @if (!empty($product))
                    @php
                        if (request()->user('customer')) {
                            $pricelist = DB::connection('mysql')->select("select * from ec_pricelist where product_id=$product->id and customer_id=$userid");
                        }
                    @endphp
                    <li>
                        <div class="shopping-cart-img">
                            <a href="{{ $product->original_product->url }}">
                                @php
                                    $defaultImgUrl = RvMedia::getImageUrl(RvMedia::getDefaultImage());
                                    $productImgUrl = RvMedia::getImageUrl($product->original_product->image);
                                    $ch = curl_init($productImgUrl);
                                    curl_setopt($ch, CURLOPT_NOBODY, true);
                                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                                    curl_exec($ch);
                                    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                    curl_close($ch);

                                    if ($responseCode == 200) {
                                        $Image = $productImgUrl;
                                    } else {
                                        $Image = $defaultImgUrl;
                                    }
                                @endphp
                                <img style="width:50px" alt="{{ $product->name }}" src="{{ $Image }}">
                            </a>
                        </div>
                        <div class="shopping-cart-title">
                            <h6>{{ $product->name }} @if ($product->isOutOfStock())
                                    <span class="stock-status-label">({!! $product->stock_status_html !!})</span>
                                @endif
                            </h6>
                            <h3 style="font-size: small;font-weight: 500;">Codice: {{ $product->sku }}</h3>
                            <h3><span class="d-inline-block">{{ $cartItem->qty }}</span>
                                <span class="d-inline-block"> x </span>
                                @if (auth('customer')->user())
                                    @if ($offerDetail)
                                        @if ($offerType == 1 || $offerType == 2 || $offerType == 3)
                                            <span
                                                class="d-inline-block">{{ format_price($offerDetail->product_price) }}</span>
                                            <small><del>{{ format_price($pricelist[0]->final_price) }}</del></small>
                                        @elseif ($offerType == 6)
                                            @if ($cartItem->qty >= $offerDetail->quantity)
                                                <span>{{ format_price($offerDetail->product_price) }}</span>
                                                <small><del>{{ format_price($pricelist[0]->final_price) }}</del></small>
                                            @else
                                                <span>{{ format_price($cartItem->price) }}</span>
                                            @endif
                                        @elseif($offerType == 4)
                                            <span>{{ format_price($cartItem->price) }}</span>
                                            <span class="d-block badge badge-secondary"
                                                style="background: #E52728;font-size:smaller;color:white">3x2</span>
                                        @elseif ($offerType == 5)
                                            <span>{{ format_price($cartItem->price) }}</span>
                                            <span class="d-block badge badge-secondary"
                                                style="background: #E52728;font-size:smaller;color:white"><i
                                                    class="fa fa-link"></i></span>
                                        @endif
                                    @else
                                        <span class="d-inline-block">{{ format_price($cartItem->price) }}</span>
                                    @endif
                                @else
                                    <span class="d-inline-block">{{ format_price($cartItem->price) }}</span>
                                @endif

                            </h3>
                            <p class="mb-0"><small>{{ $cartItem->options['attributes'] ?? '' }}</small></p>

                            @if (!empty($cartItem->options['options']))
                                {!! render_product_options_info($cartItem->options['options'], $product, true) !!}
                            @endif

                            @if (!empty($cartItem->options['extras']) && is_array($cartItem->options['extras']))
                                @foreach ($cartItem->options['extras'] as $option)
                                    @if (!empty($option['key']) && !empty($option['value']))
                                        <p class="mb-0"><small>{{ $option['key'] }}: <strong>
                                                    {{ $option['value'] }}</strong></small></p>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        <div class="shopping-cart-delete">
                            <a href="#" data-url="{{ route('public.cart.remove', $cartItem->rowId) }}"
                                class="remove-cart-item"><i class="fa fa-trash-alt"
                                    style="color:red;font-size: smaller;"></i></a>
                        </div>
                    </li>
                @endif
            @endforeach

            <a href="/empty-cart" class='mt-5 btn btn-danger btn-outline col-12 empty-card'
                style='border-radius:50px'>Svuota il Carrello</a>
        </ul>
    @endif
    <div class="shopping-cart-footer">
        <div class="shopping-cart-total">
            @if (EcommerceHelper::isTaxEnabled())
                <h5><strong class="d-inline-block">{{ __('IVA esclusa') }}:</strong>
                    <span>{{ format_price($cartTotal) }}</span>
                </h5>
                <div class="clearfix"></div>
                <h5><strong class="d-inline-block">{{ __('IVA') }}:</strong>
                    <span>{{ format_price($cartIva) }}</span>
                </h5>
                <div class="clearfix"></div>
                <h4><strong class="d-inline-block">{{ __('IVA inclusa') }}:</strong> <span
                        class="total-on-dropdown">{{ format_price($cartTotal + $cartIva) }}</span></h4>
            @else
                <h4><strong class="d-inline-block">{{ __('IVA esclusa') }}:</strong>
                    <span>{{ format_price($cartTotal) }}</span>
                </h4>
            @endif
        </div>
        <div class="shopping-cart-button">
            <a class="col-12" href="{{ route('public.cart') }}"
                style="color:white !important; text-align:center">{{ __("Controlla e concludi l'ordine") }}</a>
            {{-- @if (session('tracked_start_checkout'))
                <a href="{{ route('public.checkout.information', session('tracked_start_checkout')) }}">{{ __('Checkout') }}</a>
            @endif --}}
        </div>
    </div>
@else
    <span>{{ __('Nessun prodotto nel carrello.') }}</span>
@endif
