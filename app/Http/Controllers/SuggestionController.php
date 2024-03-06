<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\offerType;
use Botble\Ecommerce\Models\Customer;
use Theme;
use Botble\Ecommerce\Models\CarouselProducts;




 
class SuggestionController extends Controller
{





    public function saveInteraction(Request $request){

        $href=request()->input('href');
        $action=request()->input('action');
        if(request()->user('customer')){
            $user=request()->user('customer')->id;
        }else{
            $user=null;
        }
        $currentTimestamp = Carbon::now()->timestamp;
        $oneMinuteAgo = Carbon::now()->subMinute()->timestamp;
        $existingRecord=DB::connection('mysql')->select('select * from ec_suggestion_data where created_at > "'.$oneMinuteAgo.'" and href='.$href.' and action="'.$action.'" and user='.$user);
        if(!$existingRecord){
            DB::connection('mysql')->table('ec_suggestion_data')->insert([
                'href'=>$href,
                'action'=>$action,
                'created_at'=>$currentTimestamp,
                'user'=>$user
            ]);
            return 'ok';
        }else{
            return 'not inserted into db';
        }
    }


    public static function getProduct($userId=11)
{
    // $lastRecord = offerType::latest()->first();

    // $navigatedProducts = self::getProductsById(
    //     DB::connection('mysql')->table('ec_suggestion_data')
    //         ->select('href', DB::raw('COUNT(*) as interaction_count'))
    //         ->where('user', $userId)
    //         ->groupBy('href')
    //         ->orderByDesc('interaction_count')
    //         ->take(100)
    //         ->pluck('href')
    //         ->toArray()
    // );

    $discountedProducts = self::getProductsById(
        DB::connection('mysql')->table('ec_pricelist')
            ->where('customer_id', $userId)
            ->whereNotNull('final_price')
            ->pluck('product_id')
            ->toArray()

    );

    // $expiry_limit = $lastRecord->expiry_limit;
    // $expiringProducts = [];
    // if (in_array($expiry_limit, [1, 3, 6])) {
    //     $expiringProducts = self::getProductsBySku(
    //         DB::connection('mysql')->table('ec_oldProducts')
    //             ->where('client_id', $userId)
    //             ->whereNotNull('scadenza')
    //             ->whereRaw("scadenza <= CURRENT_DATE + INTERVAL {$expiry_limit} MONTH")
    //             ->pluck('product')
    //             ->toArray()
    //     );
    // }

    // $productTypes = [
    //     'Offers' => $discountedProducts,
    //     'Expiring' => $expiringProducts,
    //     'Navigated' => $navigatedProducts
    // ];

    // $firstPr = $productTypes[$lastRecord->First] ?? [];
    // $secondPr = $productTypes[$lastRecord->Second] ?? [];
    // $thirdPr = $productTypes[$lastRecord->Third] ?? [];

    // $array_to_show = [];
    // foreach ([$firstPr, $secondPr, $thirdPr] as $pr) {
    //     $array_to_show = array_merge($array_to_show, $pr);
    // }

    // $array_to_show = array_slice(array_unique($array_to_show), 0, $lastRecord->show);
    // $array_to_show = array_filter($array_to_show);
    $array_to_show = $discountedProducts ;
    foreach ($array_to_show as $item) {
        $carousel = new CarouselProducts();
        $carousel->customer_id = $userId;
        $carousel->product_id = $item->id;
        $carousel->save();
    }

    return $array_to_show;
}

private static function getProductsById(array $ids, $type = null)
{
    $query = Product::whereIn('id', $ids);
    if ($type) {
        $query->where('product_type', $type);
    }
    return $query->get()->all();
}

private static function getProductsBySku(array $skus, $type = null)
{
    $query = Product::whereIn('sku', $skus);
    if ($type) {
        $query->where('product_type', $type);
    }
    return $query->get()->all();
}


    private function pushToArrayIfNotExist(&$array,$item){
        if(!in_array($item,$array)){
            $array[]=$item;
        }
    }

}
