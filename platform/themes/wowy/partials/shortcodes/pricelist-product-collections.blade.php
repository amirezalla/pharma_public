@php
use App\Http\Controllers\PricelistController;
    $products = PricelistController:: pricelist();
@endphp
@if($products !== false)
    <div class="container mb-4">

        <div class="card-deck">

            <div class="col-lg-3 col-md-4">
                @foreach($products as $product)
                    <div class="product-cart-wrap mb-30">
                        <div class="product-img-action-wrap">
                            <div class="product-img product-img-zoom">
                                <a href="{{ $product->url }}">
                                    @if(isset($product->image))
                                    @php
                                        $defaultImgUrl = RvMedia::getImageUrl(RvMedia::getDefaultImage());
                                        $productImgUrl =RvMedia::getImageUrl($product->images[0]);
                                        $ch = curl_init($productImgUrl);
                                        curl_setopt($ch, CURLOPT_NOBODY, true);
                                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                                        curl_exec($ch);
                                        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                        curl_close($ch);

                                        if($responseCode == 200){
                                            $Image=$productImgUrl;
                                        }else{
                                            $Image=$defaultImgUrl;
                                        }
                                                @endphp
                                    <img class="default-img" src="{{ $Image }}" alt="{{ $product->name }}">

                                    @else
                                    <img src="{{ RvMedia::getImageUrl($product->image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $product->name }}" class="default-img">
                                    @endif
                                </a>
                            </div>
                            <div class="product-badges product-badges-position product-badges-mrg">

                            </div>
                        </div>
                        <div class="product-content-wrap">
                            <h2><a href="{{ $product->url }}">{{ $product->name }}</a></h2>
                            <div class="product-price">
                                @php
                                    if(auth('customer')->user()!==NULL){
                                        $userid=auth('customer')->user()->id;
                                        $pricelist=DB::connection('mysql')->select("select * from ec_pricelist where product_id=$product->id and customer_id=$userid");
                                        if(isset($pricelist[0])){
                                            $reserved_price=$pricelist[0]->final_price;
                                        }
                                    }
                                @endphp
                                @if(isset($reserved_price))
                                    @if ($reserved_price !== $product->price)
                                    <span>{{ format_price($reserved_price) }}</span>
                                    <input type="hidden" name="product_price" class="hidden-product-id" value="{{ $reserved_price }}"/>
                                        <span class="old-price">{{ format_price($product->price_with_taxes) }}</span>
                                    @endif
                                @else
                                <span>{{ format_price($product->front_sale_price_with_taxes) }}</span>

                                @endif


                            </div>
                            @if (EcommerceHelper::isCartEnabled())
                                <div class="product-action-1 show " @if (!EcommerceHelper::isReviewEnabled()) style="bottom: 10px;" @endif>
                                    <a aria-label="{{ __('Add To Cart') }}" class="action-btn hover-up add-to-cart-button" data-id="{{ $product->id }}" data-url="{{ route('public.cart.add-to-cart') }}" href="#"><i class="far fa-shopping-bag"></i></a>
                                </div>
                            @endif
                        </div>
                    </div>

                @endforeach

            </div>
        </div>
    </div>

@endif



</div>

