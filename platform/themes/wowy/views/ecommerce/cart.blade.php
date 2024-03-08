@php
    use App\Http\Controllers\SuggestionController;
    use Botble\Ecommerce\Models\OffersDetail;
    use Botble\Ecommerce\Models\Offers;
    use Botble\Ecommerce\Models\Product;
    use Botble\Ecommerce\Models\ProductVariation;
    use Botble\Ecommerce\Models\SPC;
    use Botble\Ecommerce\Models\CarouselProducts;
    if (request()->user('customer')) {
        $userid = request()->user('customer')->id;
        if (!CarouselProducts::where('customer_id', $userid)->exists()) {
            $discountedProducts = SuggestionController::getProduct($userid);
        } else {
            $productIds = CarouselProducts::where('customer_id', $userid)->pluck('product_id');
            $discountedProducts = Product::whereIn('id', $productIds)->get();
        }
    }
@endphp
<section class="mt-60 mb-20">
    <div class="container">
        <div class="row">
            <div class="col-12 section--shopping-cart">
                <form class="form--shopping-cart" method="post" action="{{ route('public.cart.update') }}">
                    @csrf
                    @if (count($products) > 0)
                        <div class="row">
                            <div class="col-8">
                                <div class="table-responsive">
                                    <table class="table shopping-summery text-center clean table--cart">
                                        <thead>
                                            <tr class="main-heading">
                                                <th scope="col"></th>
                                                <th scope="col">{{ __('Name') }}</th>
                                                <th scope="col">{{ __('Prezzo') }}</th>
                                                <th scope="col">{{ __('Quantità') }}</th>
                                                <th scope="col">{{ __('Totale parziale') }}</th>
                                                <th scope="col">{{ __('Remove') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $cartTotal = Cart::instance('cart')->rawSubTotal();
                                                $cartIva = Cart::instance('cart')->rawTax();
                                                $userid = request()->user('customer')->id;
                                                if ($userid == 11 || $userid == 13) {
                                                    $userid = 2621;
                                                }
                                            @endphp
                                            @foreach (Cart::instance('cart')->content() as $key => $cartItem)
                                                @php
                                                    $flag = false; // Reset flag for each item
                                                    $product = Product::find($cartItem->id);
                                                    if ($product && $product->is_variation) {
                                                        $AllVariations = Product::where('name', $cartItem->name)->get();
                                                        foreach ($AllVariations as $variation) {
                                                            if ($variation->is_variation) {
                                                                $flag = true;
                                                                break; // Found a variation, no need to continue
                                                            }
                                                        }
                                                    }
                                                    if ($flag) {
                                                        $productVariation = ProductVariation::where('product_id', $cartItem->id)->first();
                                                        $product_id = $productVariation ? $productVariation->configurable_product_id : $cartItem->id;
                                                    } else {
                                                        $product_id = $cartItem->id;
                                                    }
                                                    $pricelist = DB::connection('mysql')->select("select * from ec_pricelist where product_id=$product_id and customer_id=$userid");
                                                    if ($pricelist) {
                                                        $offerDetail = OffersDetail::where('product_id', $product_id)->where('customer_id', $userid)->first();
                                                        if ($offerDetail) {
                                                            $offer = Offers::find($offerDetail->offer_id);
                                                            if ($offer) {
                                                                $offerType = $offer->offer_type;
                                                                if ($offerType == 4 && $cartItem->qty >= 3) {
                                                                    $cartTotal = $cartTotal - $cartItem->price * floor($cartItem->qty / 3);
                                                                    $tax = str_replace('€', '', $cartItem->tax());
                                                                    $tax = str_replace(',', '.', $tax);
                                                                    $cartIva = $cartIva - floatval(floatval($tax) * floor($cartItem->qty / 3));
                                                                }
                                                                if ($offerType == 6 && $cartItem->qty >= $offerDetail->quantity) {
                                                                    $tax = str_replace('€', '', $cartItem->tax());
                                                                    $tax = str_replace(',', '.', $tax);
                                                                    $cartIva = $cartIva - floatval($tax) * $cartItem->qty + (($product->tax->percentage * $offerDetail->product_price) / 100) * $cartItem->qty;
                                                                    $cartTotal = $cartTotal - $cartItem->price * $cartItem->qty + $offerDetail->product_price * $cartItem->qty;
                                                                    $cartItem->price = $offerDetail->product_price;
                                                                }
                                                            }
                                                        } else {
                                                            $cartItem->price = $pricelist[0]->final_price;
                                                        }
                                                    }
                                                @endphp

                                                @if (!empty($product))
                                                    <tr>
                                                        <td class="image product-thumbnail">
                                                            <input type="hidden"
                                                                name="items[{{ $key }}][rowId]"
                                                                value="{{ $cartItem->rowId }}">
                                                            <a href="{{ $product->original_product->url }}">
                                                                @php
                                                                    $defaultImgUrl = RvMedia::getImageUrl(RvMedia::getDefaultImage());
                                                                    $productImgUrl = RvMedia::getImageUrl($cartItem->options['image']);
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

                                                                <img class="default-img" src="{{ $Image }}"
                                                                    alt="{{ $product->name }}">

                                                            </a>
                                                        </td>
                                                        <td class="product-des product-name">
                                                            <p class="product-name"><a
                                                                    href="{{ $product->original_product->url }}">{{ $cartItem->name }}
                                                                    @if ($pricelist)
                                                                        @if ($offerDetail)
                                                                            @if ($offerType == 1 || $offerType == 2 || $offerType == 3)
                                                                                <span class="badge badge-secondary"
                                                                                    style="background: #E52728;font-size:smaller">{{ get_sale_percentage($product->price, $offerDetail->product_price) }}</span>
                                                                            @elseif ($offerType == 4)
                                                                                <span class="badge badge-secondary"
                                                                                    style="background: #E52728;font-size:smaller">3x2</span>
                                                                            @elseif ($offerType == 5)
                                                                                <span class="badge badge-secondary"
                                                                                    style="background: #E52728;font-size:smaller"><i
                                                                                        class="fa fa-link"></i></span>
                                                                            @elseif ($offerType == 6 && $cartItem->qty >= $offerDetail->quantity)
                                                                                {{-- It's okay --}}
                                                                                @php
                                                                                    if ($pricelist) {
                                                                                        $priceOfProduct = $pricelist[0]->final_price;
                                                                                    } else {
                                                                                        $priceOfProduct = $product->price;
                                                                                    }
                                                                                @endphp
                                                                                <span class="badge badge-secondary"
                                                                                    style="background: #E52728;font-size:smaller">{{ get_sale_percentage($offerDetail->product_price, $priceOfProduct) }}</span>
                                                                            @endif
                                                                        @endif
                                                                    @endif

                                                                    @if ($product->isOutOfStock())
                                                                        <span
                                                                            class="stock-status-label">({!! $product->stock_status_html !!})</span>
                                                                    @endif
                                                                </a></p>
                                                            <p class="mb-0">
                                                                <small>{{ $cartItem->options['attributes'] ?? '' }}</small>
                                                            </p>

                                                            @if (!empty($cartItem->options['options']))
                                                                {!! render_product_options_info($cartItem->options['options'], $product, true) !!}
                                                            @endif

                                                            @if (!empty($cartItem->options['extras']) && is_array($cartItem->options['extras']))
                                                                @foreach ($cartItem->options['extras'] as $option)
                                                                    @if (!empty($option['key']) && !empty($option['value']))
                                                                        <p class="mb-0">
                                                                            <small>{{ $option['key'] }}:
                                                                                <strong>
                                                                                    {{ $option['value'] }}
                                                                                </strong>
                                                                            </small>
                                                                        </p>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </td>
                                                        <td class="price" data-title="{{ __('Price') }}">
                                                            @if ($pricelist)
                                                                @if ($offerDetail)
                                                                    @if ($offerType == 6 && $cartItem->qty >= $offerDetail->quantity)
                                                                        @php
                                                                            $cartItem->price = $offerDetail->product_price;
                                                                        @endphp
                                                                        <span>{{ format_price($cartItem->price) }}</span>
                                                                        <span>
                                                                            <del
                                                                                style="display:block;font-size: xx-small">
                                                                                {{ format_price($product->price) }}
                                                                            </del>
                                                                        </span>
                                                                    @elseif ($offerType == 6 && $cartItem->qty < $offerDetail->quantity)
                                                                        @php
                                                                            $cartItem->price = $pricelist[0]->final_price;
                                                                        @endphp
                                                                        <span>{{ format_price($cartItem->price) }}</span>
                                                                    @elseif ($offerType == 4)
                                                                        <span>{{ format_price($pricelist[0]->final_price) }}</span>
                                                                    @elseif ($offerType == 3 || $offerType == 2 || $offerType == 1)
                                                                        <span>{{ format_price($cartItem->price) }}</span>
                                                                        <span>
                                                                            <del
                                                                                style="display:block;font-size: xx-small">
                                                                                {{ format_price($product->price) }}
                                                                            </del>
                                                                        </span>
                                                                    @else
                                                                        <span>{{ format_price($cartItem->price) }}</span>
                                                                    @endif
                                                                @else
                                                                    <span>{{ format_price($pricelist[0]->final_price) }}</span>
                                                                @endif
                                                            @else
                                                                <span>{{ format_price($cartItem->price) }}</span>
                                                            @endif
                                                        </td>

                                                        <td class="text-center" data-title="{{ __('Quantity') }}">
                                                            <div class="detail-qty border radius  m-auto">
                                                                <a href="#" class="qty-down"><i
                                                                        class="fa fa-caret-down"
                                                                        aria-hidden="true"></i></a>
                                                                <input type="number" min="1"
                                                                    value="{{ $cartItem->qty }}"
                                                                    name="items[{{ $key }}][values][qty]"
                                                                    class="qty-val qty-input" />
                                                                <a href="#" class="qty-up"><i
                                                                        class="fa fa-caret-up"
                                                                        aria-hidden="true"></i></a>
                                                            </div>
                                                        </td>
                                                        <td class="text-right" data-title="{{ __('Subtotal') }}">
                                                            @if ($pricelist)
                                                                @if ($offerDetail)
                                                                    @if ($offerType == 4)
                                                                        @php
                                                                            $originalPrice = $pricelist[0]->final_price;

                                                                            // Calculate the total number of items that need to be paid for
                                                                            $paidItemsCount = $cartItem->qty - floor($cartItem->qty / 3);

                                                                            // Calculate the total price for the paid items
                                                                            $totalPriceForPaidItems = $paidItemsCount * $originalPrice;

                                                                            // Calculate the adjusted price per item, taking into account the quantity
                                                                            // This ensures that the cart item's price is adjusted to reflect the effective price after the offer
$adjustedPricePerItem = $cartItem->qty > 0 ? $totalPriceForPaidItems / $cartItem->qty : 0;

// Update the cart item's price to the adjusted price per item
                                                                            $cartItem->price = $adjustedPricePerItem;
                                                                        @endphp

                                                                        <span>{{ format_price($totalPriceForPaidItems) }}</span>

                                                                        @if ($cartItem->qty >= 3)
                                                                            <span>
                                                                                <del
                                                                                    style="display:block; font-size: xx-small">
                                                                                    {{ format_price($pricelist[0]->final_price * $cartItem->qty) }}
                                                                                </del>
                                                                            </span>
                                                                        @endif
                                                                    @elseif ($offerType == 6 && $cartItem->qty >= $offerDetail->quantity)
                                                                        <span>{{ format_price($cartItem->price * $cartItem->qty) }}</span>
                                                                        <span>
                                                                            <del
                                                                                style="display:block;font-size: xx-small">{{ format_price($pricelist[0]->final_price * $cartItem->qty) }}</del>
                                                                        </span>
                                                                    @else
                                                                        <span>{{ format_price($cartItem->price * $cartItem->qty) }}</span>
                                                                    @endif
                                                                @else
                                                                    <span>{{ format_price($cartItem->price * $cartItem->qty) }}</span>
                                                                @endif
                                                            @else
                                                                <span>{{ format_price($cartItem->price * $cartItem->qty) }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="action" data-title="{{ __('Remove') }}">
                                                            <a href="#" class="text-muted remove-cart-button "
                                                                data-url="{{ route('public.cart.remove', $cartItem->rowId) }}"><i
                                                                    class="fa fa-trash-alt  "
                                                                    style="color: red; font-size: 12pt;"></i></a>
                                                        </td>
                                                    </tr>
                                                    @if ($pricelist)
                                                        @if ($offerDetail)
                                                            @if ($offerType == 5)
                                                                @php
                                                                    $collegati_id = $offerDetail->gift_product_id;
                                                                    $collegati = Product::find($collegati_id);
                                                                @endphp
                                                                <tr
                                                                    style="position: relative; background-color:#d7f7d8">


                                                                    <td class="image product-thumbnail">
                                                                        {{--  <input type="hidden" name="items[{{ $key }}][rowId]" value="{{ $cartItem->rowId }}">  --}}
                                                                        <span>
                                                                            {{-- <img src="{{ $cartItem->options['image'] }}" alt="{{ $product->name }}" /> --}}
                                                                            <h6>{{ $collegati->sku }}</h6>
                                                                        </span>
                                                                    </td>
                                                                    <td class="product-des product-name">
                                                                        <p class="product-name">
                                                                            <span>{{ $collegati->name }} &nbsp;
                                                                                @if ($product->isOutOfStock())
                                                                                    <span
                                                                                        class="stock-status-label">({!! $product->stock_status_html !!})</span>
                                                                                @endif
                                                                            </span>
                                                                        </p>
                                                                        <p class="mb-0">
                                                                            <small>{{ $cartItem->options['attributes'] ?? '' }}</small>
                                                                        </p>

                                                                        @if (!empty($cartItem->options['options']))
                                                                            {!! render_product_options_info($cartItem->options['options'], $product, true) !!}
                                                                        @endif

                                                                        @if (!empty($cartItem->options['extras']) && is_array($cartItem->options['extras']))
                                                                            @foreach ($cartItem->options['extras'] as $option)
                                                                                @if (!empty($option['key']) && !empty($option['value']))
                                                                                    <p class="mb-0">
                                                                                        <small>{{ $option['key'] }}:
                                                                                            <strong>
                                                                                                {{ $option['value'] }}</strong></small>
                                                                                    </p>
                                                                                @endif
                                                                            @endforeach
                                                                        @endif
                                                                    </td>
                                                                    <td class="price">

                                                                        <span>{{ format_price(0) }}</span>
                                                                        <small
                                                                            style="display:block"><del>{{ format_price($collegati->price) }}</del></small>
                                                                    </td>

                                                                    <td class="text-center">
                                                                        <div class="detail-qty border radius  m-auto">
                                                                            <input type="number" disabled
                                                                                min="1"
                                                                                value="{{ 1 }}"
                                                                                name="collegati[]"
                                                                                class="qty-val qty-input" />
                                                                        </div>
                                                                    </td>
                                                                    <td colspan="2" class="text-right"
                                                                        data-title="{{ __('Subtotal') }}">
                                                                    </td>

                                                                </tr>
                                                            @endif
                                                            @if ($pricelist)
                                                                @if ($offerDetail)
                                                                    @if ($offerType == 6)
                                                                        <tr class="alert alert-danger">
                                                                            <td colspan="6">se la quantità di questo
                                                                                prodotto
                                                                                sarà superiore a
                                                                                {{ $offerDetail->quantity }}
                                                                                avrai uno sconto del
                                                                                @php
                                                                                    if ($pricelist) {
                                                                                        $priceOfProduct = $pricelist[0]->final_price;
                                                                                    } else {
                                                                                        $priceOfProduct = $product->price;
                                                                                    }
                                                                                @endphp
                                                                                {{ get_sale_percentage($offerDetail->product_price, $priceOfProduct) }}
                                                                                solo su questo prodotto</td>
                                                                        </tr>
                                                                    @endif
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="cart-action row">
                                    <div class="col-12">
                                        {{--                                    <div class="heading_s1 mb-3"> --}}
                                        {{--                                        <h4>{{ __('Apply Coupon') }}</h4> --}}
                                        {{--                                    </div> --}}
                                        <div class="total-amount">
                                            <div class="left">
                                                <div class="coupon form-coupon-wrapper">
                                                    <div class="form-row row justify-content-center">
                                                        <div class="form-group col-lg-9">
                                                            <input class="font-medium coupon-code" type="text"
                                                                name="coupon_code" value="{{ old('coupon_code') }}"
                                                                @if (($couponDiscountAmount > 0 && session('applied_coupon_code')) || session('applied_spc')) disabled readonly @endif
                                                                placeholder="{{ __('Hai un codice coupon? Inseriscilo qui.') }}">
                                                        </div>
                                                        <div class="form-group col-lg-3">
                                                            <button
                                                                class="col-12 btn btn-rounded btn-sm btn-apply-coupon-code"
                                                                type="button"
                                                                data-url="{{ route('public.coupon.apply') }}"
                                                                style="line-height: 3 !important"><i
                                                                    class="far fa-bookmark mr-5"></i>Applica
                                                                Coupon</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            @if (Cart::instance('cart')->count() > 0)
                                                <div class="col-12 mt-30">
                                                    <div class="row">
                                                        <div class="col-6" style="align-self: flex-end">
                                                            <a href="/empty-cart"
                                                                class='mt-5 btn btn-danger btn-outline col-12 empty-card'
                                                                style='border-radius:50px'><i
                                                                    class='fa fa-trash-alt mr-2'></i> Svuota il
                                                                Carrello</a>
                                                            {{--                                        <a class="btn btn-rounded" href="{{ route('public.products') }}"><i class="far fa-cart-plus mr-5"></i>{{ __('Continue Shopping') }}</a> --}}
                                                        </div>
                                                        <div class="col-6">
                                                            <button type="submit" name="checkout"
                                                                class="col-12 btn btn-rounded"> <i
                                                                    class="fa fa-share-square mr-10"></i>
                                                                {{ __('Proceed To Checkout') }}</button>
                                                        </div>
                                                    </div>



                                                </div>
                                                {{--                        <div class="divider center_icon mt-50 mb-50"><i class="fa fa-gem"></i></div> --}}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="row mb-50">

                                    <div class="col-lg-12 col-md-12">
                                        <div class="border p-md-4 p-30 border-radius-10 cart-totals">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <tbody>



                                                        @if (auth('customer'))
                                                            @php
                                                                session()->forget('shippingAmount');
                                                                $address = Botble\Ecommerce\Models\Address::where('customer_id', auth('customer')->user()->id)->first();
                                                                $customerType = auth('customer')->user()->type;
                                                                $region = $address->state;

                                                                $weight = 0.0;
                                                                $IVAPERCENTAGE = 1.22;
                                                                $orderAmount = Cart::instance('cart')->rawTotal();

                                                                if ($region == ('campania' || 'lazio') && $customerType == ('Farmacia' || 'Parafarmacia' || 'AltroPharma') && $orderAmount < 300) {
                                                                    $shippingAmount = 10;
                                                                }
                                                                if ($region == ('campania' || 'lazio') && $customerType == ('Farmacia' || 'Parafarmacia' || 'AltroPharma') && $orderAmount >= 300) {
                                                                    $shippingAmount = 5;
                                                                }
                                                                if ($customerType == ('Farmacia' || 'Parafarmacia' || 'AltroPharma')) {
                                                                    $shippingAmount = 10;
                                                                }
                                                                session(['shippingAmount' => $shippingAmount]);
                                                                $subtotal = Cart::instance('cart')->rawSubTotal();

                                                                if (session('applied_spc')) {
                                                                    $coupon = SPC::where('code', session('applied_spc'))->where('status', 1)->first();

                                                                    if ($coupon->min_order != null && Cart::instance('cart')->rawSubTotal() + Cart::instance('cart')->rawTax() + $shippingAmount < $coupon->min_order) {
                                                                        session()->forget('applied_spc');
                                                                        session()->forget('discount_amount');
                                                                    } else {
                                                                        $first = $shippingAmount;
                                                                        if ($coupon->type == 1) {
                                                                            $shippingAmount = $shippingAmount * ((100 - $coupon->amount) / 100);
                                                                        } elseif ($coupon->type == 2) {
                                                                            $shippingAmount = $coupon->amount >= $shippingAmount ? 0 : $shippingAmount - $coupon->amount;
                                                                        } else {
                                                                            $shippingAmount = 0;
                                                                        }
                                                                        $couponDiscountAmount = $first - $shippingAmount;
                                                                    }
                                                                }
                                                            @endphp
                                                        @endif

                                                        <input type="hidden" name="shippingAmount"
                                                            value="{{ $shippingAmount }}">

                                                        <tr>

                                                            <td class="cart_total_label">
                                                                {{ __('Subtotale IVA esclusa') }}
                                                            </td>
                                                            <td class="cart_total_amount"><strong><span
                                                                        class="font-xl fw-900 text-brand">{{ format_price(Cart::instance('cart')->rawSubTotal()) }}</span></strong>
                                                            </td>
                                                        </tr>

                                                        <tr>

                                                            <td class="cart_total_label">
                                                                {{ __('Contributo spese di spedizione e imballagio') }}
                                                            </td>
                                                            <td class="cart_total_amount">
                                                                <strong>
                                                                    <span class="font-xl fw-900 text-brand">
                                                                        @if (session('applied_spc'))
                                                                            {{ format_price($first) }}
                                                                        @else
                                                                            {{ format_price($shippingAmount) }}
                                                                        @endif
                                                                    </span>
                                                                </strong>
                                                            </td>
                                                        </tr>

                                                        @if (($couponDiscountAmount > 0 && session('applied_coupon_code')) || session('applied_spc'))
                                                            <tr>
                                                                @php
                                                                    if (session('applied_coupon_code')) {
                                                                        $couponcodefinal = session('applied_coupon_code');
                                                                    } else {
                                                                        $couponcodefinal = session('applied_spc');
                                                                    }
                                                                @endphp
                                                                <td class="cart_total_label">
                                                                    {{ __('Coupon code: :code', ['code' => $couponcodefinal]) }}
                                                                    (<small><a
                                                                            class="btn-remove-coupon-code btn-remove-spc text-danger"
                                                                            data-url="{{ route('public.coupon.remove') }}"
                                                                            href="javascript:void(0)"
                                                                            data-processing-text="{{ __('Removing...') }}">{{ __('Remove') }}</a></small>)<span>
                                                                </td>
                                                                @if (session('applied_spc') || session('applied_coupon_code'))
                                                                    <td><span class="font-lg fw-900 text-brand"
                                                                            style="color: #E52728 !important">
                                                                            -{{ format_price($couponDiscountAmount) }}
                                                                        </span>
                                                                    </td>
                                                                @endif
                                                                <input type="hidden" name="couponCode"
                                                                    value="{{ $couponcodefinal }} ">
                                                            </tr>
                                                        @endif
                                                        @if (EcommerceHelper::isTaxEnabled())
                                                            <tr>
                                                                <td class="cart_total_label">{{ __('Tax') }}</td>
                                                                <td class="cart_total_amount"><span
                                                                        class="font-lg fw-900 text-brand">{{ format_price(Cart::instance('cart')->rawTax()) }}</span>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        <tr>

                                                            <td class="cart_total_label">
                                                                {{ __('Totale IVA inclusa') }}
                                                            </td>
                                                            <td class="cart_total_amount"><strong>
                                                                    <span id="total"
                                                                        class="font-xl fw-900 text-brand">
                                                                        @if (session('applied_spc'))
                                                                            {{ format_price(Cart::instance('cart')->rawSubTotal() + Cart::instance('cart')->rawTax() + $shippingAmount) }}
                                                                        @else
                                                                            {{ format_price(Cart::instance('cart')->rawSubTotal() + Cart::instance('cart')->rawTax() + $shippingAmount - $couponDiscountAmount) }}
                                                                        @endif
                                                                    </span>
                                                                </strong>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <p style="font-size: 9.5pt !important;">Il contributo per le spese di
                                                spedizione ed imballaggio è calcolato sull'importo dell'ordine al netto
                                                di
                                                eventuali omaggi a cui si ha diritto. <br>
                                                Questi ultimi saranno stornati dal totale, in fase di gestione
                                                dell'ordine.
                                            </p>
                                            <p style='font-size: 11pt; color: black'><label for="comunicarci"><b>Hai
                                                        qualcosa da comunicarci?</b></label></p>
                                            <textarea id="comunicarci" name="note" rows="5" cols="37" style='min-height: unset!important'
                                                placeholder="Scrivi qui eventuali note per l'ordine">
                                                @if (Session::get('note') != '')
{{ Session::has('note') ? Session::get('note') : '' }}
@endif
                                            </textarea>

                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="error-container"
                    style="background-color: #fff;
                            padding: 20px;text-align: center;
                            border-radius: 5px;">
                    <div class="error-icon"
                        style="font-size: 90px;
                                color: #777;
                                margin-bottom: 20px;">
                        <i class="far fa-shopping-cart"></i>
                    </div>
                    <p style="font-size: 12pt;
                                font-weight: 600;">Il suo
                        carello è vuoto!</p>
                </div>
            </div>
        </div>
        @endif

    </div>
    <div class="col-12 related-listing">
        @if (request()->user('customer'))

            <div class="row">
                <center>
                    <h4 class="title-discounted mb-30" style="color:#005BA1; ">
                        <i class="fas fa-circle" style="animation:pulse-blue 2s infinite;border-radius:10px"></i>
                        &nbsp;
                        &nbsp;
                        Pensiamo che questi prodotti potrebbero interessarti
                    </h4>
                </center>
                {{-- <div class="owl-carousel owl-theme discounted-carousel "> --}}
                <div class="owl-carousel owl-theme discounted-carousel ">
                    @foreach ($discountedProducts as $discountedProduct)
                        @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.cart-related-product-items',
                            ['product' => $discountedProduct]
                        )
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
