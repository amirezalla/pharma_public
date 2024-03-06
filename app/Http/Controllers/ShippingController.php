<?php


namespace App\Http\Controllers;

use Botble\Ecommerce\Models\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Agent;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductTag;
use Botble\Ecommerce\Models\Regione;
use Botble\Ecommerce\Models\Offers;
use Botble\Ecommerce\Models\OffersDetail;
use Botble\Ecommerce\Models\offerType;
use Botble\Ecommerce\Models\PriceList;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use App\Jobs\OfferDeactivationJob;
use App\Models\Shipping;

use Carbon\Carbon;
use LDAP\Result;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Response;
use Throwable;


class ShippingController extends BaseController
{

    public function showForm()
    {
        return view('shipping.form');
    }

    public function calculateShipping(Request $request)
    {
        $region = $request->input('region');
        $customerType = $request->input('customer_type');
        $orderAmount = $request->input('order_amount');

        $shippingCost = Shipping::getShippingCost($region, $customerType, $orderAmount);

        return view('shipping.show', ['shippingCost' => $shippingCost]);
    }


}
