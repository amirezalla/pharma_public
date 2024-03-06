<?php

namespace Botble\Ecommerce\Http\Controllers;

use Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Widgets\Contracts\AdminWidget;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Models\offerType;

use Carbon\Carbon;
use EcommerceHelper;
use Illuminate\Http\Request;

class OfferTypeController  extends BaseController
{

    public function view()
    {
        return view('OfferType.create');
    }

    public function add(Request $request)
    {
        $orders=$request->input('orders');
        $data= [
            'First'=>$orders[0],
            'Second'=>$orders[1],
            'Third'=>$orders[2],
            'min_price'=>$request->input('min_price'),
            'max_price'=>$request->input('max_price'),
            'show'=>$request->input('show'),
            'acquistati_in_precedenza'=>$request->input('precedenza'),
            'expiry_limit'=>$request->input('expiry_limit')
        ];
        $offertype=offerType::create($data);
        if($offertype){
            return view('OfferType.create',compact('offertype'));
        }

    }

}
