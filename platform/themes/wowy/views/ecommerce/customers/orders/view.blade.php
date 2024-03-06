@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Order detail :order', ['order' => get_order_code($order->id)]) }}</h5>
        </div>
        <div class="card-body">
            <div class="customer-order-detail">
                <div class="row">
                    <div class="col-auto me-auto">
                        <div class="order-slogan">
                            @php
                                $logo = theme_option('logo_in_the_checkout_page') ?: theme_option('logo');
                            @endphp
                            @if ($logo)
                                <img width="100" src="{{ RvMedia::getImageUrl($logo) }}"
                                    alt="{{ theme_option('site_title') }}">
                                <br />
                            @endif
                            {{ setting('contact_address') }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="order-meta">
                            <span class="d-inline-block">Data:</span>
                            <strong class="order-detail-value">{{ $order->created_at->format('d/m/Y h:m') }}</strong>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 border-top pt-2">
                        <h4>Informazione dell'ordine</h4>
                        <div>
                            <div>
                                <span class="d-inline-block">Lo stato dell'ordine:</span>
                                <strong class="order-detail-value">{{ $order->status->label() }}</strong>
                            </div>
                            <div>
                                <span class="d-inline-block">{{ __('Importo') }}:</span>
                                <strong class="order-detail-value"> {{ $order->amount_format }} </strong>
                            </div>
                            <div>
                                <span class="d-inline-block">{{ __('Contributo per spedizione ed imballaggio') }}:</span>
                                <strong class="order-detail-value">{{ format_price($order->shipping_amount) }} </strong>
                            </div>
                            @if (EcommerceHelper::isTaxEnabled())
                                <div>
                                    <span class="d-inline-block">{{ __('IVA') }}:</span>
                                    <strong class="order-detail-value"> {{ format_price($order->tax_amount) }} </strong>
                                </div>
                            @endif

                            <div>
                                @if ($order->discount_amount)
                                    <span class="d-inline-block">{{ __('Discount') }}:</span>
                                    <strong class="order-detail-value"> {{ format_price($order->discount_amount) }}
                                        @if ($order->coupon_code)
                                            ({!! BaseHelper::html(__('Coupon code: ":code"', ['code' => Html::tag('strong', $order->coupon_code)->toHtml()])) !!})
                                        @elseif ($order->discount_description)
                                            ({{ $order->discount_description }})
                                        @endif
                                    </strong>
                                @endif
                            </div>
                            @if ($order->description)
                                <div>
                                    <span class="d-inline-block">{{ __('Note') }}:</span>
                                    <p class="order-detail-value text-warning">{{ $order->description }} </p>
                                </div>
                            @endif
                        </div>


                        <h4 class="mt-3 mb-1">{{ __('Products') }}</h4>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Importo') }}</th>
                                        <th style="width: 100px">{{ __('Quantità') }}</th>
                                        <th class="price text-right">{{ __('Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->products as $orderProduct)
                                        @php
                                            $product = get_products([
                                                'condition' => [
                                                    'ec_products.id' => $orderProduct->product_id,
                                                ],
                                                'take' => 1,
                                                'select' => ['ec_products.id', 'ec_products.images', 'ec_products.name', 'ec_products.price', 'ec_products.sale_price', 'ec_products.sale_type', 'ec_products.start_date', 'ec_products.end_date', 'ec_products.sku', 'ec_products.is_variation', 'ec_products.status', 'ec_products.order', 'ec_products.created_at'],
                                            ]);

                                        @endphp
                                        <tr>
                                            <td class="align-middle">{{ $loop->index + 1 }}</td>
                                            <td class="align-middle">
                                                @php
                                                    $defaultImgUrl = RvMedia::getImageUrl(RvMedia::getDefaultImage());
                                                    $productImgUrl = RvMedia::getImageUrl($product->images[0]);
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
                                                <div class="row">
                                                    <div class="col-2">
                                                        <img src="{{ $Image }}" width="50"
                                                            alt="{{ $orderProduct->product_name }}">
                                                    </div>
                                                    <div class="col-10">
                                                        {{ $orderProduct->product_name }} @if ($product && $product->sku)
                                                            ({{ $product->sku }})
                                                        @endif
                                                        @if ($product && $product->is_variation)
                                                            <p>
                                                                <small>
                                                                    @php $attributes = get_product_attributes($product->id) @endphp
                                                                    @if (!empty($attributes))
                                                                        @foreach ($attributes as $attribute)
                                                                            {{ $attribute->attribute_set_title }}:
                                                                            {{ $attribute->title }}@if (!$loop->last)
                                                                                ,
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </small>
                                                            </p>
                                                        @endif

                                                        @if (!empty($orderProduct->options) && is_array($orderProduct->options))
                                                            @foreach ($orderProduct->options as $option)
                                                                @if (!empty($option['key']) && !empty($option['value']))
                                                                    <p class="mb-0"><small>{{ $option['key'] }}: <strong>
                                                                                {{ $option['value'] }}</strong></small></p>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>


                                            </td>

                                            <td class="align-middle">{{ $orderProduct->amount_format }}</td>
                                            <td class="align-middle">{{ $orderProduct->qty }}</td>
                                            <td class="money text-right align-middle">
                                                <strong>
                                                    {{ $orderProduct->total_format }}
                                                </strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>


                        <div class="mt-2 row">
                            @if ($order->isInvoiceAvailable())
                                <div class="col-auto me-auto">
                                    <a href="{{ route('customer.print-order', $order->id) }}" class="btn btn-info btn-sm"
                                        style='color:white !important'><i class="fa fa-download"></i>
                                        {{ __('Scarica ordine') }}</a>
                                </div>
                            @endif
                            @if ($order->canBeCanceled())
                                <div class="col-auto">
                                    <a href="{{ route('customer.orders.cancel', $order->id) }}"
                                        onclick="return confirm('{{ __('Are you sure?') }}')"
                                        class="btn btn-danger btn-sm ml-2">{{ __('Cancel order') }}</a>
                                </div>
                            @endif
                            @if ($order->canBeReturned())
                                <div class="col-auto">
                                    <a href="{{ route('customer.order_returns.request_view', $order->id) }}"
                                        class="btn btn-danger btn-sm ml-2">
                                        {{ __('Return Product(s)') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
