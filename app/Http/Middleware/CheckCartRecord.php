<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use Botble\Ecommerce\Models\SaveCart;
use Botble\Ecommerce\Models\Product;
use Cart;
use Botble\Ecommerce\Http\Controllers\Customers\LoginController;



class CheckCartRecord
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
        // if ($user=$request->user('customer')) {
        //     $user_id = $user->id;
        //     $cartRecord = SaveCart::where('user_id', $user_id)->first();

        //     if ($cartRecord && !session()->has('cart') ) {
        //         LoginController::staticLogout($user_id);
        //     }elseif($cartRecord && session()->has('cart')){
        //         $cart = session('cart');
        //         if(!isset($cart['cart'])){
        //             LoginController::staticLogout($user_id);
        //         }
        //     }else{
        //         return $next($request);
        //     }
        // }else{
        // }

    }
}