<?php

namespace Botble\Ecommerce\Http\Controllers\Fronts;

use BaseHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ShippingCodStatusEnum;
use Botble\Ecommerce\Enums\ShippingMethodEnum;
use Botble\Ecommerce\Enums\ShippingStatusEnum;
use Botble\Ecommerce\Events\OrderPlacedEvent;
use Botble\Ecommerce\Facades\DiscountFacade;
use Botble\Ecommerce\Http\Requests\ApplyCouponRequest;
use Botble\Ecommerce\Http\Requests\CheckoutRequest;
use Botble\Ecommerce\Http\Requests\SaveCheckoutInformationRequest;
// use Botble\Ecommerce\Jobs\ChangeOrderConfirmation;
// use Botble\Ecommerce\Jobs\EditOrderJob;
// use Botble\Ecommerce\Jobs\OrderSubmittedJob;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderShippingAmount;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Ecommerce\Repositories\Interfaces\AddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Ecommerce\Repositories\Interfaces\DiscountInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderAddressInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentInterface;
use Botble\Ecommerce\Repositories\Interfaces\ShippingInterface;
use Botble\Ecommerce\Repositories\Interfaces\TaxInterface;
use Botble\Ecommerce\Services\Footprints\FootprinterInterface;
use Botble\Ecommerce\Services\HandleApplyCouponService;
use Botble\Ecommerce\Services\HandleApplyPromotionsService;
use Botble\Ecommerce\Services\HandleRemoveCouponService;
use Botble\Ecommerce\Services\HandleShippingFeeService;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Supports\PaymentHelper;
use Cart;
use EcommerceHelper;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Mollie\Api\Types\OrderStatus;
use OptimizerHelper;
use OrderHelper;
use Theme;
use Validator;
use Botble\Payment\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Botble\Ecommerce\Mail\OrderSubmitted;
use Botble\Ecommerce\Mail\OrderConfirmed;
use Botble\Ecommerce\Mail\OrderPaymentFailed;
use Botble\Ecommerce\Http\Controllers\SaveCartController;
use Botble\Ecommerce\Mail\OrderEdited;
use Botble\Ecommerce\Models\Invoice;
use Botble\Ecommerce\Models\OffersDetail;
use Botble\Ecommerce\Models\Offers;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductVariants;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\SPC;
use Illuminate\Support\Facades\DB;



class PublicCheckoutController
{
    protected TaxInterface $taxRepository;

    protected OrderInterface $orderRepository;

    protected OrderProductInterface $orderProductRepository;

    protected OrderAddressInterface $orderAddressRepository;

    protected AddressInterface $addressRepository;

    protected CustomerInterface $customerRepository;

    protected ShippingInterface $shippingRepository;

    protected OrderHistoryInterface $orderHistoryRepository;

    protected ProductInterface $productRepository;

    protected DiscountInterface $discountRepository;

    public function __construct(
        TaxInterface          $taxRepository,
        OrderInterface        $orderRepository,
        OrderProductInterface $orderProductRepository,
        OrderAddressInterface $orderAddressRepository,
        AddressInterface      $addressRepository,
        CustomerInterface     $customerRepository,
        ShippingInterface     $shippingRepository,
        OrderHistoryInterface $orderHistoryRepository,
        ProductInterface      $productRepository,
        DiscountInterface     $discountRepository
    )
    {
        $this->taxRepository = $taxRepository;
        $this->orderRepository = $orderRepository;
        $this->orderProductRepository = $orderProductRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->addressRepository = $addressRepository;
        $this->customerRepository = $customerRepository;
        $this->shippingRepository = $shippingRepository;
        $this->orderHistoryRepository = $orderHistoryRepository;
        $this->productRepository = $productRepository;
        $this->discountRepository = $discountRepository;

        OptimizerHelper::disable();
    }

    public function getCheckout(
        string                       $token,
        Request                      $request,
        BaseHttpResponse             $response,
        HandleShippingFeeService     $shippingFeeService,
        HandleApplyCouponService     $applyCouponService,
        HandleRemoveCouponService    $removeCouponService,
        HandleApplyPromotionsService $applyPromotionsService
    )
    {

        // if (!EcommerceHelper::isCartEnabled()) {
        //     abort(404);
        // }

        // if (!EcommerceHelper::isEnabledGuestCheckout() && !auth('customer')->check()) {
        //     return $response->setNextUrl(route('customer.login'));
        // }

        // if ($token !== session('tracked_start_checkout')) {
        //     $order = $this->orderRepository->getFirstBy(['token' => $token, 'is_confirmed' => false]);
        //     if (!$order) {
        //         return $response->setNextUrl(route('public.index'));
        //     }
        // }

        if (!$request->session()->has('error_msg') && $request->input('error') == 1 && $request->input('error_type') == 'payment') {
            $request->session()->flash('error_msg', __('Payment failed!'));
        }

        $sessionCheckoutData = OrderHelper::getOrderSessionData($token);

        if(session()->has('retry_checkout')){
            $products=Order::where('token',session('retry-checkout'))->get()->products();
        }else{
            $products = Cart::instance('cart')->products();
            if (!$products->count()) {
                return $response->setNextUrl(route('public.cart'));
            }
        }


        foreach ($products as $product) {
            if ($product->isOutOfStock()) {
                return $response
                    ->setError()
                    ->setNextUrl(route('public.cart'))
                    ->setMessage(__('Product :product is out of stock!', ['product' => $product->original_product->name]));
            }
        }

        if (!EcommerceHelper::canCheckoutForDigitalProducts($products)) {
            return $response
                ->setError()
                ->setNextUrl(route('customer.login'))
                ->setMessage(__('Your shopping cart has digital product(s), so you need to sign in to continue!'));
        }

        $sessionCheckoutData = $this->processOrderData($token, $sessionCheckoutData, $request);

        $paymentMethod = $request->input('payment_method', session('selected_payment_method') ?: PaymentHelper::defaultPaymentMethod());
        if ($paymentMethod) {
            session()->put('selected_payment_method', $paymentMethod);
        }

        if (is_plugin_active('marketplace')) {
            [
                $sessionCheckoutData,
                $shipping,
                $defaultShippingMethod,
                $defaultShippingOption,
                $shippingAmount,
                $promotionDiscountAmount,
                $couponDiscountAmount,
            ] = apply_filters(PROCESS_CHECKOUT_ORDER_DATA_ECOMMERCE, $products, $token, $sessionCheckoutData, $request);
        } else {
            $promotionDiscountAmount = $applyPromotionsService->execute($token);

            $sessionCheckoutData['promotion_discount_amount'] = $promotionDiscountAmount;

            $couponDiscountAmount = 0;
            if (session()->has('applied_coupon_code')) {
                $couponDiscountAmount = Arr::get($sessionCheckoutData, 'coupon_discount_amount', 0);
            }

            $orderTotal = Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount;
            $orderTotal = max($orderTotal, 0);

            $isAvailableShipping = EcommerceHelper::isAvailableShipping($products);

            $shipping = [];
            $defaultShippingMethod = $request->input('shipping_method', Arr::get($sessionCheckoutData, 'shipping_method', ShippingMethodEnum::DEFAULT));
            $defaultShippingOption = $request->input('shipping_option', Arr::get($sessionCheckoutData, 'shipping_option'));
            $shippingAmount = 0;

            if ($isAvailableShipping) {
                $origin = EcommerceHelper::getOriginAddress();
                $shippingData = EcommerceHelper::getShippingData($products, $sessionCheckoutData, $origin, $orderTotal, $paymentMethod);

                $shipping = $shippingFeeService->execute($shippingData);

                foreach ($shipping as $key => &$shipItem) {
                    if (get_shipping_setting('free_ship', $key)) {
                        foreach ($shipItem as &$subShippingItem) {
                            Arr::set($subShippingItem, 'price', 0);
                        }
                    }
                }

                if ($shipping) {
                    if (!$defaultShippingMethod) {
                        $defaultShippingMethod = old(
                            'shipping_method',
                            Arr::get($sessionCheckoutData, 'shipping_method', Arr::first(array_keys($shipping)))
                        );
                    }
                    if (!$defaultShippingOption) {
                        $defaultShippingOption = old('shipping_option', Arr::get($sessionCheckoutData, 'shipping_option', $defaultShippingOption));
                    }
                }

                $shippingAmount = Arr::get($shipping, $defaultShippingMethod . '.' . $defaultShippingOption . '.price', 0);

                Arr::set($sessionCheckoutData, 'shipping_method', $defaultShippingMethod);
                Arr::set($sessionCheckoutData, 'shipping_option', $defaultShippingOption);
                Arr::set($sessionCheckoutData, 'shipping_amount', $shippingAmount);

                OrderHelper::setOrderSessionData($token, $sessionCheckoutData);
            }

            if (session()->has('applied_coupon_code')) {
                if (!$request->input('applied_coupon')) {
                    $discount = $applyCouponService->getCouponData(
                        session('applied_coupon_code'),
                        $sessionCheckoutData
                    );
                    if (empty($discount)) {
                        $removeCouponService->execute();
                    } else {
                        $shippingAmount = Arr::get($sessionCheckoutData, 'is_free_shipping') ? 0 : $shippingAmount;
                    }
                } else {
                    $shippingAmount = Arr::get($sessionCheckoutData, 'is_free_shipping') ? 0 : $shippingAmount;
                }
            }

            $sessionCheckoutData['is_available_shipping'] = $isAvailableShipping;

            if (!$sessionCheckoutData['is_available_shipping']) {
                $shippingAmount = 0;
            }
        }

        $isShowAddressForm = EcommerceHelper::isSaveOrderShippingAddress($products);
        $data = compact(
            'token',
            'shipping',
            'defaultShippingMethod',
            'defaultShippingOption',
            'shippingAmount',
            'promotionDiscountAmount',
            'couponDiscountAmount',
            'sessionCheckoutData',
            'products',
            'isShowAddressForm',
        );

        if (auth('customer')->check()) {
            $addresses = auth('customer')->user()->addresses;
            $isAvailableAddress = !$addresses->isEmpty();

            if (Arr::get($sessionCheckoutData, 'is_new_address')) {
                $sessionAddressId = 'new';
            } else {
                $sessionAddressId = Arr::get($sessionCheckoutData, 'address_id', $isAvailableAddress ? $addresses->first()->id : null);
                if (!$sessionAddressId && $isAvailableAddress) {
                    $address = $addresses->firstWhere('is_default') ?: $addresses->first();
                    $sessionAddressId = $address->id;
                }
            }

            $data = array_merge($data, compact('addresses', 'isAvailableAddress', 'sessionAddressId'));
        }

        $checkoutView = Theme::getThemeNamespace('views.ecommerce.orders.checkout');

        if (view()->exists($checkoutView)) {
            return view($checkoutView, $data);
        }

        return view('plugins/ecommerce::orders.checkout', $data);
    }

    protected function processOrderData(string $token, array $sessionData, Request $request, bool $finished = false): array
    {
        if ($request->has('billing_address_same_as_shipping_address')) {
            $sessionData['billing_address_same_as_shipping_address'] = $request->input('billing_address_same_as_shipping_address');
        }

        if ($request->has('billing_address')) {
            $sessionData['billing_address'] = $request->input('billing_address');
        }

        if ($request->has('address.address_id')) {
            $sessionData['is_new_address'] = $request->input('address.address_id') == 'new';
        }

        if ($request->input('address', [])) {
            if (!isset($sessionData['created_account']) && $request->input('create_account') == 1) {
                $validator = Validator::make($request->input(), [
                    'password' => 'required|min:6',
                    'password_confirmation' => 'required|same:password',
                    'address.email' => 'required|max:60|min:6|email|unique:ec_customers,email',
                    'address.name' => 'required|min:3|max:120',
                ]);

                if (!$validator->fails()) {
                    $customer = $this->customerRepository->createOrUpdate([
                        'name' => BaseHelper::clean($request->input('address.name')),
                        'email' => BaseHelper::clean($request->input('address.email')),
                        'phone' => BaseHelper::clean($request->input('address.phone')),
                        'password' => Hash::make($request->input('password')),
                    ]);

                    auth('customer')->attempt([
                        'email' => $request->input('address.email'),
                        'password' => $request->input('password'),
                    ], true);

                    event(new Registered($customer));

                    $sessionData['created_account'] = true;

                    $address = $this->addressRepository
                        ->createOrUpdate(array_merge($request->input('address'), [
                            'customer_id' => $customer->id,
                            'is_default' => true,
                        ]));

                    $request->merge(['address.address_id' => $address->id]);
                    $sessionData['address_id'] = $address->id;
                }
            }

            if ($finished && auth('customer')->check()) {
                $customer = auth('customer')->user();
                if ($customer->addresses->count() == 0 || $request->input('address.address_id') == 'new') {
                    $address = $this->addressRepository
                        ->createOrUpdate(array_merge($request->input('address', []), [
                            'customer_id' => auth('customer')->id(),
                            'is_default' => $customer->addresses->count() == 0,
                        ]));

                    $request->merge(['address.address_id' => $address->id]);
                    $sessionData['address_id'] = $address->id;
                }
            }
        }

        $address = null;

        if (($addressId = $request->input('address.address_id')) && $addressId !== 'new') {
            $address = $this->addressRepository->findById($addressId);
            if ($address) {
                $sessionData['address_id'] = $address->id;
            }
        } elseif (auth('customer')->check() && !Arr::get($sessionData, 'address_id')) {
            $address = $this->addressRepository->getFirstBy([
                'customer_id' => auth('customer')->id(),
                'is_default' => true,
            ]);

            if ($address) {
                $sessionData['address_id'] = $address->id;
            }
        }

        $addressData = [
            'billing_address_same_as_shipping_address' => Arr::get($sessionData, 'billing_address_same_as_shipping_address', true),
            'billing_address' => Arr::get($sessionData, 'billing_address', []),
        ];

        if (!empty($address)) {
            $addressData = [
                'name' => $address->name,
                'phone' => $address->phone,
                'email' => $address->email,
                'country' => $address->country,
                'state' => $address->state,
                'city' => $address->city,
                'address' => $address->address,
                'zip_code' => $address->zip_code,
                'address_id' => $address->id,
            ];
        } elseif ((array)$request->input('address', [])) {
            $addressData = (array)$request->input('address', []);
        }

        $addressData = OrderHelper::cleanData($addressData);

        $sessionData = array_merge($sessionData, $addressData);

        $products = Cart::instance('cart')->products();
        if (is_plugin_active('marketplace')) {
            $sessionData = apply_filters(
                HANDLE_PROCESS_ORDER_DATA_ECOMMERCE,
                $products,
                $token,
                $sessionData,
                $request
            );

            OrderHelper::setOrderSessionData($token, $sessionData);

            return $sessionData;
        }

        if (!isset($sessionData['created_order'])) {
            $currentUserId = 0;
            $cartTotal=Cart::instance('cart')->rawSubTotal();
            $cartIva=Cart::instance('cart')->rawTax();
            if (auth('customer')->check()) {
                $currentUserId = auth('customer')->id();
                if($currentUserId==11 || $currentUserId==13){
                    $currentUserId=2621;
                }
            }
            foreach(Cart::instance('cart')->content() as $key => $cartItem){
                $flag = false; // Reset flag for each item
                $product = Product::find($cartItem->id); // Assuming $item->id is correct

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
                $offerDetail=OffersDetail::where('product_id',$product_id)->where('customer_id',$currentUserId)->where('status','active')->first();
                if($offerDetail){
                    $offer=Offers::find($offerDetail->offer_id);
                    if($offer){
                        $offerType=$offer->offer_type;
                        if($offerType==4 && $cartItem->qty >=3){
                            $cartTotal=$cartTotal- ($cartItem->price * floor($cartItem->qty/3));
                            $tax=str_replace('€', '', $cartItem->tax());
                                                        $cartIva = $cartIva - (floatval($tax) * floor($cartItem->qty/3));                        }
                        if($offerType==6 && $cartItem->qty >= $offerDetail->quantity){

                            $cartIva= $cartIva - ((floatval($cartItem->tax()) * $cartItem->qty)) + (($product->tax->percentage * $offerDetail->product_price / 100 * $cartItem->qty));
                            $cartTotal=$cartTotal- ($cartItem->price* $cartItem->qty) + ($offerDetail->product_price* $cartItem->qty);
                        }

                    }
                }
            }
            $couponDiscountAmount = Arr::get($sessionData, 'coupon_discount_amount');
            $amount = ($cartTotal + $cartIva) + (float)(session()->get('shippingAmount')) + ((float)(session()->get('shippingAmount')) * 0.22)  - $couponDiscountAmount;

            $request->merge([
                'amount' => $amount ?: 0,
                'currency' => $request->input('currency', strtoupper(get_application_currency()->title)),
                'user_id' => $currentUserId,
                'shipping_method' => 'deafult',
                'shipping_option' => 4,
                'shipping_amount' => (float)(session()->get('shippingAmount')),
                'tax_amount' => $cartIva + ((float)(session()->get('shippingAmount')) * 0.22),
                'sub_total' => $cartTotal,
                'coupon_code' => session()->get('applied_coupon_code'),
                'discount_amount' => $couponDiscountAmount,
                'status' => OrderStatusEnum::RETURNED,
                'token' => $token,
                'is_finished'=>FALSE
                ]);

            $order = $this->orderRepository->getFirstBy(compact('token'));


            $order = $this->createOrderFromData($request->input(), $order);


            $sessionData['created_order'] = true;
            $sessionData['created_order_id'] = $order->id;
        }

        if (!empty($address)) {
            $addressData['order_id'] = $sessionData['created_order_id'];
        } elseif ((array)$request->input('address', [])) {
            $addressData = array_merge(
                ['order_id' => $sessionData['created_order_id']],
                (array)$request->input('address', [])
            );
        }

        $sessionData['is_save_order_shipping_address'] = EcommerceHelper::isSaveOrderShippingAddress($products);
        $sessionData = OrderHelper::checkAndCreateOrderAddress($addressData, $sessionData);

        if (!isset($sessionData['created_order_product'])) {
            $weight = 0;
            foreach (Cart::instance('cart')->content() as $cartItem) {
                $product = $this->productRepository->findById($cartItem->id);
                if ($product && $product->weight) {
                    $weight += $product->weight * $cartItem->qty;
                }
            }

            $weight = EcommerceHelper::validateOrderWeight($weight);

            $this->orderProductRepository->deleteBy(['order_id' => $sessionData['created_order_id']]);

            foreach (Cart::instance('cart')->content() as $cartItem) {
                $product = $this->productRepository->findById($cartItem->id);

                $data = [
                    'order_id' => $sessionData['created_order_id'],
                    'product_id' => $cartItem->id,
                    'product_name' => $cartItem->name,
                    'product_image' => $product->original_product->image,
                    'qty' => $cartItem->qty,
                    'weight' => $weight,
                    'price' => $cartItem->price,
                    'tax_amount' => $cartItem->tax,
                    'options' => [],
                    'product_type' => $product ? $product->product_type : null,
                ];

                if ($cartItem->options->extras) {
                    $data['options'] = $cartItem->options->extras;
                }

                if ($cartItem->options['options']) {
                    $data['product_options'] = $cartItem->options['options'];
                }

                $this->orderProductRepository->create($data);
            }

            $sessionData['created_order_product'] = Cart::instance('cart')->getLastUpdatedAt();
        }

        OrderHelper::setOrderSessionData($token, $sessionData);

        return $sessionData;
    }

    public function postSaveInformation(
        string                         $token,
        SaveCheckoutInformationRequest $request,
        BaseHttpResponse               $response,
        HandleApplyCouponService       $applyCouponService,
        HandleRemoveCouponService      $removeCouponService
    )
    {
        if (!EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        if ($token !== session('tracked_start_checkout')) {
            $order = $this->orderRepository->getFirstBy(['token' => $token, 'is_finished' => false]);

            if (!$order) {
                return $response->setNextUrl(route('public.index'));
            }
        }

        if ($paymentMethod = $request->input('payment_method')) {
            session()->put('selected_payment_method', $paymentMethod);
        }

        if (is_plugin_active('marketplace')) {
            $sessionData = array_merge(OrderHelper::getOrderSessionData($token), $request->input('address'));

            $sessionData = apply_filters(
                PROCESS_POST_SAVE_INFORMATION_CHECKOUT_ECOMMERCE,
                $sessionData,
                $request,
                $token
            );
        } else {
            $sessionData = array_merge(OrderHelper::getOrderSessionData($token), $request->input('address'));
            OrderHelper::setOrderSessionData($token, $sessionData);
            if (session()->has('applied_coupon_code')) {
                $discount = $applyCouponService->getCouponData(session('applied_coupon_code'), $sessionData);
                if (!$discount) {
                    $removeCouponService->execute();
                }
            }
        }

        $sessionData = $this->processOrderData($token, $sessionData, $request);

        return $response->setData($sessionData);
    }

    public function postCheckout(

        string                       $token,
        CheckoutRequest              $request,
        BaseHttpResponse             $response,
        HandleShippingFeeService     $shippingFeeService,
        HandleApplyCouponService     $applyCouponService,
        HandleRemoveCouponService    $removeCouponService,
        HandleApplyPromotionsService $handleApplyPromotionsService
    )
    {
        if (!EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        if (!EcommerceHelper::isEnabledGuestCheckout() && !auth('customer')->check()) {
            return $response->setNextUrl(route('customer.login'));
        }

        if (!Cart::instance('cart')->count()) {
            return $response
                ->setError()
                ->setMessage(__('No products in cart'));
        }

        $products = Cart::instance('cart')->products();

        if (!EcommerceHelper::canCheckoutForDigitalProducts($products)) {
            return $response
                ->setError()
                ->setNextUrl(route('customer.login'))
                ->setMessage(__('Your shopping cart has digital product(s), so you need to sign in to continue!'));
        }

        if (EcommerceHelper::getMinimumOrderAmount() > Cart::instance('cart')->rawSubTotal()) {
            return $response
                ->setError()
                ->setMessage(__('Minimum order amount is :amount, you need to buy more :more to place an order!', [
                    'amount' => format_price(EcommerceHelper::getMinimumOrderAmount()),
                    'more' => format_price(EcommerceHelper::getMinimumOrderAmount() - Cart::instance('cart')
                            ->rawSubTotal()),
                ]));
        }

        $sessionData = OrderHelper::getOrderSessionData($token);

        $sessionData = $this->processOrderData($token, $sessionData, $request, true);

        foreach ($products as $product) {
            if ($product->isOutOfStock()) {
                return $response
                    ->setError()
                    ->setMessage(__('Product :product is out of stock!', ['product' => $product->original_product->name]));
            }
        }



        if (is_plugin_active('marketplace')) {
            return apply_filters(
                HANDLE_PROCESS_POST_CHECKOUT_ORDER_DATA_ECOMMERCE,
                $products,
                $request,
                $token,
                $sessionData,
                $response
            );
        }

        $isAvailableShipping = EcommerceHelper::isAvailableShipping($products);

        $shippingMethodInput = $request->input('shipping_method', 'default');

        $promotionDiscountAmount = $handleApplyPromotionsService->execute($token);
        $couponDiscountAmount = Arr::get($sessionData, 'coupon_discount_amount');


        $paymentMethod = $request->input('payment_method', session('selected_payment_method'));
        if ($paymentMethod) {

            session()->put('selected_payment_method', $paymentMethod);


        }
        $shippingAmount = 0;

        $shippingData = [];
        if ($isAvailableShipping) {
            $orderTotal = Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount;
            $origin = EcommerceHelper::getOriginAddress();
            $shippingData = EcommerceHelper::getShippingData($products, $sessionData, $origin, $orderTotal, $paymentMethod);

            $shippingMethodData = $shippingFeeService->execute(
                $shippingData,
                $shippingMethodInput,
                $request->input('shipping_option')
            );

            $shippingMethod = Arr::first($shippingMethodData);
            if (!$shippingMethod) {
                throw ValidationException::withMessages([
                    'shipping_method' => trans('validation.exists', ['attribute' => trans('plugins/ecommerce::shipping.shipping_method')]),
                ]);
            }

            $shippingAmount = Arr::get($shippingMethod, 'price', 0);

            if (get_shipping_setting('free_ship', $shippingMethodInput)) {
                $shippingAmount = 0;
            }
        }

        if (session()->has('applied_coupon_code')) {
            $discount = $applyCouponService->getCouponData(session('applied_coupon_code'), $sessionData);
            if (empty($discount)) {
                $removeCouponService->execute();
            } else {
                $shippingAmount = Arr::get($sessionData, 'is_free_shipping') ? 0 : $shippingAmount;
            }
        }

        $currentUserId = 0;
        if (auth('customer')->check()) {
            $currentUserId = auth('customer')->id();
        }

        $amount = Cart::instance('cart')->rawTotal() + (float)(session()->get('shippingAmount')) - $promotionDiscountAmount - $couponDiscountAmount;

        $request->merge([
            'amount' => $amount ?: 0,
            'currency' => $request->input('currency', strtoupper(get_application_currency()->title)),
            'user_id' => $currentUserId,
            'shipping_method' => 'default',
            'shipping_option' => 3,
            'shipping_amount' => (float)(session()->get('shippingAmount')),
            'tax_amount' => Cart::instance('cart')->rawTax(),
            'sub_total' => Cart::instance('cart')->rawSubTotal(),
            'coupon_code' => session()->get('applied_coupon_code'),
            'discount_amount' => $promotionDiscountAmount + $couponDiscountAmount,
            'status' => OrderStatusEnum::COMPLETED,
            'token' => $token,
        ]);


        if (Session::get('cart_order')){
            $mOrder = $this->orderRepository->findOrFail(Session::get('cart_order'));
            Session::forget('cart_order');
            $mOrder->products()->delete();
            $order = $this->createOrderFromData($request->input(),$mOrder);
            $order->update([
                'is_confirmed' => true,
                'status' => OrderStatusEnum::COMPLETED,
            ]);
            // EditOrderJob::dispatch($order);
        }else {
            $order = $this->createOrderFromData($request->input(),null);





            $this->orderHistoryRepository->createOrUpdate([
                'action' => 'create_order_from_payment_page',
                'description' => __('Order was created from checkout page'),
                'order_id' => $order->id,
            ]);

            if ($isAvailableShipping) {
                app(ShipmentInterface::class)->createOrUpdate([
                    'order_id' => $order->id,
                    'user_id' => 0,
                    'weight' => $shippingData ? Arr::get($shippingData, 'weight') : 0,
                    'cod_amount' => ($order->payment->id && $order->payment->status != PaymentStatusEnum::COMPLETED) ? $order->amount : 0,
                    'cod_status' => ShippingCodStatusEnum::PENDING,
                    'type' => 'default',
                    'status' => ShippingStatusEnum::PENDING,
                    'price' => $order->shipping_amount,
                    'rate_id' => $shippingData ? Arr::get($shippingMethod, 'id', '') : '',
                    'shipment_id' => $shippingData ? Arr::get($shippingMethod, 'shipment_id', '') : '',
                    'shipping_company_name' => $shippingData ? Arr::get($shippingMethod, 'company_name', '') : '',
                ]);
            }

            if ($appliedCouponCode = session()->get('applied_coupon_code')) {
                DiscountFacade::getFacadeRoot()->afterOrderPlaced($appliedCouponCode);
            }

            $this->orderProductRepository->deleteBy(['order_id' => $order->id]);
            $this->addProductToOrder($order, $shippingData);
            $request->merge([
                'order_id' => $order->id,
            ]);
            OrderShippingAmount::create(
                ['shippingAmount' => session()->get('shippingAmount'),
                    'order_id' => $order->id
                ]
            );

            $paymentMethod = $request->input('payment_method', session('selected_payment_method'));

            //inja paypal mire qablesh bayad product ina sakhte she

            if ($paymentMethod == 'paypal') {
                $paypalPayment = $this->initiatePaypalPayment($order, $request);

                if ($paypalPayment!==null) {
                    return redirect()->to($paypalPayment['approval_url']);
                } else {
                    // Handle error in initiating payment
                    // You might want to log this or show an error message
                    return $response->setError()->setMessage(__('Error initiating PayPal payment'));
                }
            } else {
                $order->update([
                    'payment_id' => $order->id
                ]);
                $arguments=[
                    'account_id' => auth('customer')->user()->id,
                    'amount' => $order->amount ,
                    'user_id'=>0,
                    'currency' => 'EUR',
                    'customer_id' => auth('customer')->user()->id,
                    'charge_id'=>$order->id,
                    'payment_channel' => "bank_transfer",
                    'status'=>'completed'
                ];
                $payment = Payment::updateOrCreate(['order_id' => $order->id],$arguments);
                $payment = Payment::on('mysql2')->updateOrCreate(['order_id' => $order->id], $arguments);
                $order->update([
                    'is_confirmed' => true,
                    'status' => OrderStatusEnum::COMPLETED,
                ]);

            }

            // OrderSubmittedJob::dispatch($order);
            // ChangeOrderConfirmation::dispatch($order)->delay(now()->addMinutes(30));
        }



        Mail::to($order->user->email)->send(new OrderConfirmed($order));


        session()->forget('shippingAmount');
        session()->forget('cart');
        session()->forget('note');
        session()->forget('tracked_start_checkout');

        if(session('applied_spc')){
            $coupon = SPC::where('code', session('applied_spc'))->where('status', 1)->first();
            if($coupon->once){
                $authCustomerId = request()->user('customer')->id;
                $coupon->customers()->where('customer_id', $authCustomerId)->update(['ec_spc_customers.status' => 0]);
            }
        }
        if (session()->has($order->token)) {
            session()->forget($order->token);
        }
        session()->forget('applied_spc');
        session()->forget('discount_amount');


        $this->generateInvoice($order);


        $RealOrder=Order::where('token',$order->token)->where(function($query) {
            $query->where('shipping_method','')
                  ->orWhere('status', 'pending');})->first();
        if($RealOrder){
            $RealOrder->delete();
        }
        SaveCartController::deleteSavedCart();

        return view('plugins/ecommerce::orders.thank-you', compact('order', 'products'));
    }

    private function deleteDuplicateOrders($token)
    {
            // 1. Get all orders with is_finished=2.
            $orders = Order::where('token',$token)->get();
            foreach($orders as $order){
                $ShippingAmount=OrderShippingAmount::where('order_id',$order->id)->exists();
                if(!$ShippingAmount){
                        $order->delete();
                }
            }



    }




    private function initiatePaypalPayment($order, $request) {
        $clientId = 'ASBmAoiN-ML8YpSOcxrpMyrG9Aa7V35LxHrBHCYmqdYRR-eWtLWZ3xyd3HpSOB7_q5Eogl8xXgAFSzot'; // Your Sandbox Client ID
        $clientSecret = 'ELAKp4zsYrVzRKakVO_hhNwS0ARd7wa9zz5N-KjEVn75P3T18n1GGXteuOUXMSrQmLv7wNo0S1bLMzrz'; // Your Sandbox Secret

        // Get an access token from PayPal
        $accessToken = $this->getPaypalAccessToken($clientId, $clientSecret);

        if (!$accessToken) {
            dd('Failed to retrieve PayPal access token');
            return null;
        }
            // Set up the payment details
            $RealOrder=Order::where('token',$order->token)->where('status','pending')->first();
            if($RealOrder){
                $orderTotal=number_format($RealOrder->amount, 2, '.', '');
            }else{
                $orderTotal = number_format($order->amount, 2, '.', ''); // Format to a decimal string
            }
        // Set up the payment details
        $paymentData = [
            'intent' => 'sale',
            'redirect_urls' => [
                'return_url' => "https://marigopharma.marigo.collaudo.biz/return?orderId=$order->id", //controller
                'cancel_url' => "https://marigopharma.marigo.collaudo.biz/return-canceled-paypal?orderId=$order->id" //just the view
            ],
            'payer' => [
                'payment_method' => 'paypal'
            ],
            'transactions' => [
                [
                    'amount' => [
                        'total' => $orderTotal, // Make sure this is a correctly formatted string
                        'currency' => 'EUR'
                    ],
                    'description' => 'Purchase from My Store'
                ]
            ]
        ];

        // Send the payment creation request to PayPal
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.sandbox.paypal.com/v1/payments/payment'); // Make sure to use the sandbox URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paymentData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('CURL Error: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }
        curl_close($ch);

        $responseArray = json_decode($response, true);


        if (isset($responseArray['id'])) {
            // If the payment is created successfully
            $array=[];
            foreach ($responseArray['links'] as $link) {
                if ($link['rel'] == 'approval_url') {
                    $array['approval_url']=$link['href']; // Return the approval URL
                }
            }
            $array['id']=$responseArray['id'];
            $array['create_time']=$responseArray['create_time'];
            return $array;
        } else {
            // Log PayPal response for failed requests
            error_log('PayPal Error: ' . $response);
            return null;
        }

    }

    private function getPaypalAccessToken($clientId, $clientSecret) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.sandbox.paypal.com/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $clientSecret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

        $response = curl_exec($ch);

        if (!$response) {
            error_log('CURL Error: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }

        curl_close($ch);
        $jsonResponse = json_decode($response, true);

        return $jsonResponse['access_token'] ?? null;
    }



    public function paypalConfirmed(Request $request){

        $order = $this->orderRepository->findOrFail($request->orderId);

        if($order){
            $arguments=[
                'account_id' => auth('customer')->user()->id,
                'amount' => $order->amount,
                'user_id'=>0,
                'currency' => 'EUR',
                'customer_id' => auth('customer')->user()->id,
                'charge_id'=>$request->paymentId,
                'payment_channel' => "paypal",
                'status'=>'completed'
            ];
            $payment = Payment::updateOrCreate(['order_id' => $order->id],$arguments);
            $payment = Payment::on('mysql2')->updateOrCreate(['order_id' => $order->id], $arguments);
            $order->update([
                'is_confirmed' => true,
                'status' => OrderStatusEnum::COMPLETED,
                'payment_id'=>$payment->id
            ]);



            Mail::to($order->user->email)->send(new OrderConfirmed($order));


            session()->forget('shippingAmount');
            session()->forget('cart');
            session()->forget('note');
            session()->forget('tracked_start_checkout');
            if (session()->has($order->token)) {
                session()->forget($order->token);
            }
            if(session('applied_spc')){
                $coupon = SPC::where('code', session('applied_spc'))->where('status', 1)->first();
                if($coupon->once){
                    $authCustomerId = request()->user('customer')->id;
                    $coupon->customers()->where('customer_id', $authCustomerId)->update(['ec_spc_customers.status' => 0]);
                }
            }
            session()->forget('applied_spc');
            session()->forget('discount_amount');



            $RealOrder=Order::where('token',$order->token)->where(function($query) {
                $query->whereNull('shipping_option')
                      ->orWhere('status', 'pending');})->first();
            if($RealOrder){
                $RealOrder->delete();
            }

            SaveCartController::deleteSavedCart();
            $products=$order->products;
            $this->generateInvoice($order);

            return view('plugins/ecommerce::orders.thank-you', compact('order', 'products','payment'));


        }
    }

    public function paypalCanceled(Request $request){

    $order = $this->orderRepository->findOrFail($request->orderId);

    $RealOrder=Order::where('token',$order->token)->where('status','returned')->first();
    if($RealOrder){
        $RealOrder->delete();
    }



    $arguments=[
        'account_id' => auth('customer')->user()->id,
        'amount' => $order->amount,
        'user_id'=>0,
        'currency' => 'EUR',
        'customer_id' => auth('customer')->user()->id,
        'charge_id'=>$order->id,
        'payment_channel' => "paypal",
        'status'=>'pending'
    ];
    $payment = Payment::updateOrCreate(['order_id' => $order->id],$arguments);
    $payment = Payment::on('mysql2')->updateOrCreate(['order_id' => $order->id], $arguments);
    $order->update([
        'is_confirmed' => false,
        'is_finished'=>true,
        'status' => OrderStatusEnum::PENDING,
        'payment_id'=>$payment->id
    ]);

    OrderShippingAmount::create(
        ['shippingAmount' => session()->get('shippingAmount'),
            'order_id' => $order->id
        ]
    );



    Mail::to($order->user->email)->send(new OrderPaymentFailed($order));

    session()->forget('shippingAmount');
    session()->forget('cart');
    session()->forget('note');
    session()->forget('tracked_start_checkout');
    if (session()->has($order->token)) {
        session()->forget($order->token);
    }

    SaveCartController::deleteSavedCart();


    return redirect()->to('/cancel-paypal'); //just the view



    }

    public function retryCheckout(Request $request){

        session()->forget('shippingAmount');
        session()->forget('cart');
        session()->forget('note');
        session()->forget('tracked_start_checkout');


        SaveCartController::deleteSavedCart();

        $order=Order::where('token',$request->orderToken)->first();

        $shippingAmount=OrderShippingAmount::where('order_id',$order->id)->first();
        session([
            'shippingAmount' => $shippingAmount->shippingAmount,
            'note'=>$order->description,
            'tracked_start_checkout'=>$order->token,
            'retry-checkout'=>$order->token
        ]);
        return redirect()->to("/checkout/$order->token");



    }


    private function generateInvoice($order){

        $date = time();

        // Set the locale to Italian
        setlocale(LC_TIME, 'it_IT.UTF-8');

        // Format the date
        $formatted_date = strftime('%d %B %Y', $date);


        $order->shippingAmount->shippingAmount=(float)$order->shippingAmount->shippingAmount;
        $order->shippingAmount->save();
        $invoice = new Invoice([
            'reference_id' => $order->id,
            'reference_type' => Order::class,
            'customer_name' => $order->user->name,
            'company_name' => $order->user->codice,
            'company_logo' => null,
            'customer_email' => $order->address->email ?: $order->user->email,
            'customer_phone' => $order->user->phone,
            'customer_address' => $order->user->address,
            'customer_tax_id' => null,
            'payment_id' => $order->payment->id,
            'status' => "COMPLETED",
            'paid_at' => $order->created_at,
            'completed_at'=>$formatted_date,
            'created_at'=>$formatted_date,
            'updated_at'=>$formatted_date,
            'tax_amount' => $order->tax_amount,
            'shipping_amount' => $order->shippingAmount->shippingAmount,
            'discount_amount' => $order->discount_amount,
            'sub_total' => $order->sub_total + ($order->tax_amount),
            'amount' => $order->amount,
            'shipping_method' => 'default',
            'shipping_option' => 3,
            'coupon_code' => $order->coupon_code,
            'discount_description' => $order->discount_description,
            "formatted_date"=> $formatted_date,
            'description' => $order->description,
        ]);
        $invoice->save();


            $orderProducts = $order->products;
            $item = collect($order)
                ->put('u_id', $order->id)
                ->forget(['id','products'])
                ->mapWithKeys(function ($item, $key) {
                    if (str_ends_with($key, '_at')) {
                        $item = date('Y-m-d H:i:s', strtotime($item));
                    } elseif (is_object($item) && method_exists($item, 'getValue')) {
                        $item = $item->getValue();
                    } elseif (is_array($item)) {
                        $item = collect($item)->toJson();
                    }
                    return [$key => $item];
                })->toArray();
            DB::connection('mysql2')
                ->table('fa_ec_orders')
                ->updateOrInsert([
                    'u_id' => $item['u_id'],
                ], $item);
            if ($orderProducts->count()){
                foreach ($orderProducts as $orderProduct) {
                    DB::connection('mysql2')
                        ->table('fa_ec_order_product')
                        ->updateOrInsert([
                            'u_id'=>$orderProduct->id,
                        ], collect($orderProduct)
                            ->put('u_id',$orderProduct->id)
                            ->forget(['id','product'])
                            ->put('options',collect($orderProduct['options'])->toJson())
                            ->mapWithKeys(function ($item, $key){
                                if (str_ends_with($key, '_at')) {
                                    $item = date('Y-m-d H:i:s', strtotime($item));
                                } elseif (is_object($item) && method_exists($item, 'getValue')) {
                                    $item = $item->getValue();
                                }
                                return [$key => $item];
                            })->toArray());

                    $product = get_products([
                        'condition' => [
                            'ec_products.id' => $orderProduct->product_id,
                        ],
                        'take'   => 1,
                        'select' => [
                            'ec_products.id',
                            'ec_products.images',
                            'ec_products.name',
                            'ec_products.price',
                            'ec_products.sale_price',
                            'ec_products.sale_type',
                            'ec_products.start_date',
                            'ec_products.end_date',
                            'ec_products.sku',
                            'ec_products.is_variation',
                            'ec_products.status',
                            'ec_products.order',
                            'ec_products.created_at',
                        ],
                    ]);
                    $invoice->items()->create([
                        'reference_id' => $orderProduct->product_id,
                        'reference_type' => Product::class,
                        'name' => $orderProduct->product_name,
                        'sku'=>$product->sku,
                        'description' => null,
                        'image' => $orderProduct->product_image,
                        'qty' => $orderProduct->qty,
                        'sub_total' => $orderProduct->price,
                        'tax_amount' => $orderProduct->tax_amount,
                        'discount_amount' => 0,
                        'amount' => $orderProduct->price * $orderProduct->qty,
                        'options' => json_encode($orderProduct->options),
                    ]);

                }
            }





    }














    public function getCheckoutSuccess(string $token, BaseHttpResponse $response)
    {
        if (!EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        $order = $this->orderRepository
            ->getModel()
            ->where('token', $token)
            ->with(['address', 'products'])
            ->orderBy('id', 'desc')
            ->first();

        if (!$order) {
            abort(404);
        }

        if (!$order->payment_id) {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->setMessage(__('Payment failed!'));
        }

        if (is_plugin_active('marketplace')) {
            return apply_filters(PROCESS_GET_CHECKOUT_SUCCESS_IN_ORDER, $token, $response);
        }

        if (!$order->is_finished) {
            event(new OrderPlacedEvent($order));

            $order->is_finished = true;

            if (EcommerceHelper::isOrderAutoConfirmedEnabled()) {
                $order->is_confirmed = true;
            }

            $order->save();

            OrderHelper::decreaseProductQuantity($order);

            OrderHelper::clearSessions($token);

            if (EcommerceHelper::isOrderAutoConfirmedEnabled()) {
                OrderHistory::create([
                    'action' => 'confirm_order',
                    'description' => trans('plugins/ecommerce::order.order_was_verified_by'),
                    'order_id' => $order->id,
                    'user_id' => 0,
                ]);
            }
        }

        $products = collect();

        $productsIds = $order->products->pluck('product_id')->all();

        if (!empty($productsIds)) {
            $products = get_products([
                'condition' => [
                    ['ec_products.id', 'IN', $productsIds],
                ],
                'select' => [
                    'ec_products.id',
                    'ec_products.images',
                    'ec_products.name',
                    'ec_products.price',
                    'ec_products.sale_price',
                    'ec_products.sale_type',
                    'ec_products.start_date',
                    'ec_products.end_date',
                    'ec_products.sku',
                    'ec_products.order',
                    'ec_products.created_at',
                    'ec_products.is_variation',
                ],
                'with' => [
                    'variationProductAttributes',
                ],
            ]);
        }

        return view('plugins/ecommerce::orders.thank-you', compact('order', 'products'));
    }

    public function postApplyCoupon(
        ApplyCouponRequest       $request,
        HandleApplyCouponService $handleApplyCouponService,
        BaseHttpResponse         $response
    )
    {
        if (!EcommerceHelper::isCartEnabled()) {
            abort(404);
        }
        $result = [
            'error' => false,
            'message' => '',
        ];
        if (is_plugin_active('marketplace')) {
            $result = apply_filters(HANDLE_POST_APPLY_COUPON_CODE_ECOMMERCE, $result, $request);
        } else {
            $result = $handleApplyCouponService->execute($request->input('coupon_code'));
        }

        if ($result['error']) {
            return $response
                ->setError()
                ->withInput()
                ->setMessage($result['message']);
        }

        $couponCode = $request->input('coupon_code');

        return $response
            ->setMessage(__('Applied coupon ":code" successfully!', ['code' => $couponCode]));
    }

    public function postRemoveCoupon(
        Request                   $request,
        HandleRemoveCouponService $removeCouponService,
        BaseHttpResponse          $response
    )
    {
        if (!EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        if (is_plugin_active('marketplace')) {
            $products = Cart::instance('cart')->products();
            $result = apply_filters(HANDLE_POST_REMOVE_COUPON_CODE_ECOMMERCE, $products, $request);
        } else {
            $result = $removeCouponService->execute();
        }

        if ($result['error']) {
            if ($request->ajax()) {
                return $result;
            }

            return $response
                ->setError()
                ->setData($result)
                ->setMessage($result['message']);
        }

        return $response
            ->setMessage(__('Removed coupon :code successfully!', ['code' => session('applied_coupon_code')]));
    }

    public function getCheckoutRecover(string $token, Request $request, BaseHttpResponse $response)
    {
        if(!isset($token)){
            $token=$request->orderToken;
        }
        if (!EcommerceHelper::isCartEnabled()) {
            abort(404);
        }

        if (!EcommerceHelper::isEnabledGuestCheckout() && !auth('customer')->check()) {
            return $response->setNextUrl(route('customer.login'));
        }

        if (is_plugin_active('marketplace')) {
            return apply_filters(PROCESS_GET_CHECKOUT_RECOVER_ECOMMERCE, $token, $request);
        }

        $order = $this->orderRepository
            ->getFirstBy([
                'token' => $token,
                'is_confirmed' => false,
            ], [], ['products', 'address']);

        if (!$order) {
            abort(404);
        }
        session([
            'note' => $order->description,
            'shippingAmount'=>$order->shipping_amount
        ]);


        if (session()->has('tracked_start_checkout') && session('tracked_start_checkout') == $token) {
            $sessionCheckoutData = OrderHelper::getOrderSessionData($token);
        } else {
            session(['tracked_start_checkout' => $token]);
            $sessionCheckoutData = [
                'name' => $order->address->name,
                'email' => $order->address->email,
                'phone' => $order->address->phone,
                'address' => $order->address->address,
                'country' => $order->address->country,
                'state' => $order->address->state,
                'city' => $order->address->city,
                'zip_code' => $order->address->zip_code,
                'shipping_method' => 'default',
                'shipping_option' => 3,
                'shipping_amount' => $order->shipping_amount,
            ];
        }

        Cart::instance('cart')->destroy();
        foreach ($order->products as $orderProduct) {
            $request->merge(['qty' => $orderProduct->qty]);

            $product = $this->productRepository->findById($orderProduct->product_id);
            if ($product) {
                OrderHelper::handleAddCart($product, $request);
            }
        }

        OrderHelper::setOrderSessionData($token, $sessionCheckoutData);

        return $response->setNextUrl(route('public.checkout.information', $token))
            ->setMessage(__('You have recovered from previous orders!'));
    }

    protected function createOrderFromData(array $data, ?Order $order): Order|null|false
    {
        $data['is_finished'] = true;
        $data['description'] = session('note');
        if(session('applied_spc') && session('discount_amount')){
            $data['coupon_code']=session('applied_spc');
            $data['discount_amount']=(float)(session('discount_amount'));
        }
        if(session('shippingAmount')){
         $data['amount']+=(session('shippingAmount')*0.22);
        }
        if ($order) {
            $order->fill($data);
            $order = $this->orderRepository->createOrUpdate($order);
        } else {
            $order = $this->orderRepository->createOrUpdate($data);
        }

        if (!$order->referral()->count()) {
            $referrals = app(FootprinterInterface::class)->getFootprints();

            if ($referrals) {
                $order->referral()->create($referrals);
            }
        }

        return $order;
    }

    /**
     * @param mixed $order
     * @param array $shippingData
     * @return void
     */
    public function addProductToOrder(mixed $order, array $shippingData): void
    {
        if (auth('customer')->check()) {
            $currentUserId = auth('customer')->id();

        }
        foreach (Cart::instance('cart')->content() as $cartItem) {
            $product = Product::find($cartItem->id); // Assuming $item->id is correct


            $data = [
                'order_id' => $order->id,
                'product_id' => $cartItem->id,
                'product_name' => $cartItem->name,
                'product_image' => $product->original_product->image,
                'qty' => $cartItem->qty,
                'weight' => $shippingData ? Arr::get($shippingData, 'weight') : 0,
                'price' => $cartItem->price,
                'tax_amount' => $cartItem->tax,
                'options' => [],
                'product_type' => $product ? $product->product_type : null,
            ];

            if (optional($cartItem->options)->extras) {
                $data['options'] = $cartItem->options->extras;
            }

            if (optional($cartItem->options)->options) {
                $data['product_options'] = $cartItem->options['options'];
            }

            $this->orderProductRepository->create($data);

            //  aget variant o azin kossher product Id ro begir age collegtati bud yedune behesh ezafe kon
            $flag = false; // Reset flag for each item

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
                $offerDetail=OffersDetail::where('product_id',$product_id)->where('customer_id',$currentUserId)->where('status','active')->first();
                if($offerDetail){
                    $offer=Offers::find($offerDetail->offer_id);
                    if($offer){
                        $offerType=$offer->offer_type;
                        if($offerType==5){
                            $data = [
                                'order_id' => $order->id,
                                'product_id' => $offerDetail->gift_product_id,
                                'product_name' => Product::find($offerDetail->gift_product_id)->name,
                                'product_image' => $product->original_product->image,
                                'qty' => 1,
                                'weight' => $shippingData ? Arr::get($shippingData, 'weight') : 0,
                                'price' => 0,
                                'tax_amount' => $cartItem->tax,
                                'options' => [],
                                'product_type' => $product ? $product->product_type : null,
                                'offer_id'=>$offerDetail->offer_id
                            ];
                            OrderProduct::create($data);
                        }
                    }
                }
        }
    }
}
