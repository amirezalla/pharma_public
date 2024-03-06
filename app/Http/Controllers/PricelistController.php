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


class PricelistController extends BaseController
{

    public static function pricelist()
    {
        // Check if the customer is authenticated
        if (auth('customer')->check()) {
            $customer_id = auth('customer')->user()->id;

            $productIds = DB::table('ec_pricelist')
                ->where('customer_id', $customer_id)
                ->pluck('product_id');
            if($productIds->count()>0){
                $products = Product::whereIn('id', $productIds->take(6))->get();
                return $products;
            }else{
                return false;
            }

            $products = Product::whereIn('id', $productIds)->get();

            return $products;
        } else {
            // Handle the case where no customer is authenticated
            // You might want to return an empty collection or null
            return false; // or 'return null;'
        }
    }



}
