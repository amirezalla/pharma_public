@php
    use App\Http\Controllers\SuggestionController;
    use Botble\Ecommerce\Models\OffersDetail;
    use Botble\Ecommerce\Models\Offers;
    use Botble\Ecommerce\Models\Product;
    use Botble\Ecommerce\Models\CarouselProducts;

    $userid = request()->user('customer')->id;
    if (auth('customer')->user() !== null) {
        $userid = auth('customer')->user()->id;
        $pricelist = DB::connection('mysql')->select("select * from ec_pricelist where product_id=$product->id and customer_id=$userid");
        if (isset($pricelist[0])) {
            $reserved_price = $pricelist[0]->final_price;

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
    }

@endphp

@if ($product)

    <div class="product-cart-wrap  mb-30">
        <div class="row">
            <div class="col-5">
                <div class="product-img-action-wrap">
                    <div class="product-img product-img-zoom">
                        <a href="{{ $product->url }}">
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

                            <img class="default-img" src="{{ $Image }}" alt="{{ $product->name }}">

                        </a>
                        @if (isset($offerDetail))

                            @if ($offerType == 1 || $offerType == 2 || $offerType == 3)
                                <span
                                    class="discount-ev">{{ get_sale_percentage($product->price, $pricelist[0]->final_price) }}</span>
                            @elseif ($offerType == 4)
                                <span class="discount-ev">3x2</span>
                            @elseif ($offerType == 5)
                                <span class="discount-ev"><i class="fa fa-link"></i></span>
                            @elseif ($offerType == 6)
                                <span class="discount-ev">QTY</span>
                            @endif

                        @endif
                    </div>
                    <div class="product-badges product-badges-position product-badges-mrg">
                        @if ($product->isOutOfStock())
                            <span style="background-color: #000; font-size: 11px;">{{ __('Out Of Stock') }}</span>
                        @else
                            @if ($product->productLabels->count())
                                @foreach ($product->productLabels as $label)
                                    <span
                                        @if ($label->color) style="background-color: {{ $label->color }}" @endif>{{ $label->name }}</span>
                                @endforeach
                            @elseif (
                                $product->front_sale_price !== $product->price &&
                                    ($percentSale = get_sale_percentage($product->price, $product->front_sale_price)))
                                <span class="hot">{{ $percentSale }}</span>
                            @endif
                        @endif
                    </div>

                </div>
            </div>
            <div class="col-7">


                <div class="product-content-wrap cart-related-wrap">
                    @php $category = $product->categories->sortByDesc('id')->first(); @endphp
                    <h6><a href="{{ $product->url }}">{{ $product->name }}</a></h6>
                    {{-- @if (EcommerceHelper::isReviewEnabled())
                        <div class="rating_wrap">
                            <div class="rating">
                                <div class="product_rate" style="width: {{ $product->reviews_avg * 20 }}%"></div>
                            </div>
                            <span class="rating_num">({{ $product->reviews_count }})</span>
                        </div>
                    @endif --}}
                    <!-- {!! apply_filters('ecommerce_before_product_price_in_listing', null, $product) !!} -->
                    <div class="col-12">
                        <form class="add-to-cart-form" method="POST" action="{{ route('public.cart.add-to-cart') }}">
                            <div class="product-price">
                                @csrf
                                <div class="row">
                                    <div class="col-8" style="align-self: left">
                                        <input type="hidden" name="id" class="hidden-product-id"
                                            value="{{ $product->is_variation || !$product->defaultVariation->product_id ? $product->id : $product->defaultVariation->product_id }}" />
                                        @if (isset($reserved_price))
                                            @if (!isset($offerDetail) && $reserved_price !== $product->price)
                                                <span>{{ format_price($reserved_price) }}</span>
                                                <input type="hidden" name="product_price" class="hidden-product-id"
                                                    value="{{ $reserved_price }}" />
                                                <span
                                                    class="old-price">{{ format_price($product->price_with_taxes) }}</span>
                                            @elseif (isset($offerDetail) &&
                                                    ($offerType == 1 || $offerType == 2 || $offerType == 3) &&
                                                    $offerDetail->product_price !== $product->price)
                                                <span>{{ format_price($offerDetail->product_price) }}</span>
                                                <input type="hidden" name="product_price" class="hidden-product-id"
                                                    value="{{ $offerDetail->product_price ? $offerDetail->product_price : $pricelist[0]->final_price }}" />
                                                <span
                                                    class="old-price">{{ format_price($product->price_with_taxes) }}</span>
                                            @elseif ($offerDetail && ($offerType != 1 || $offerType != 2 || $offerType != 3))
                                                <span>{{ format_price($reserved_price) }}</span>
                                                <input type="hidden" name="product_price" class="hidden-product-id"
                                                    value="{{ $reserved_price }}" />
                                                <span
                                                    class="old-price">{{ format_price($product->price_with_taxes) }}</span>
                                            @endif
                                        @else
                                            <input type="hidden" name="product_price" class="hidden-product-id"
                                                value="{{ $product->front_sale_price_with_taxes }}" />
                                            <span>{{ format_price($product->front_sale_price_with_taxes) }}</span>
                                        @endif
                                    </div>
                                    <div class="col-4" style="text-align: right">
                                        <button type="submit"
                                            class="button button-add-to-cart @if ($product->isOutOfStock()) btn-disabled @endif"
                                            type="submit" @if ($product->isOutOfStock()) disabled @endif
                                            aria-label='Aggiungi' style='padding:8px 12px !important'>
                                            <i class="far fa-shopping-bag" style="font-size: larger"></i></button>
                                    </div>
                                </div>


                            </div>

                            {!! apply_filters('ecommerce_after_product_price_in_listing', null, $product) !!}

                            @if (EcommerceHelper::isCartEnabled())
                                {{-- <div class="product-action-1 show " @if (!EcommerceHelper::isReviewEnabled()) style="bottom: 10px;" @endif>
                                        <button type="submit"
                                            class="button button-add-to-cart @if ($product->isOutOfStock()) btn-disabled @endif"
                                            type="submit" @if ($product->isOutOfStock()) disabled @endif aria-label='Aggiungi'
                                            style='padding:0px 9px !important'>
                                            <i class="far fa-shopping-bag"></i></button>
                
                                    </div> --}}
                            @endif
                        </form>
                    </div>

                </div>
            </div>

        </div>


    </div>
@endif
