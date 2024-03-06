<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Mail\VerificationAccountMail;
use Botble\ACL\Models\User;
use Botble\ACL\Traits\AuthenticatesUsers;
use Botble\ACL\Traits\LogoutGuardTrait;
use Botble\Ecommerce\Enums\CustomerStatusEnum;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Address;
use EcommerceHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use SeoHelper;
use Theme;
use Botble\Ecommerce\Http\Controllers\SaveCartController;
use Illuminate\Support\Facades\DB;
use App\Mail\UserRegisteredNotif;


class LoginController extends Controller
{
    use AuthenticatesUsers, LogoutGuardTrait {
        AuthenticatesUsers::attemptLogin as baseAttemptLogin;
    }

    public string $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('customer.guest', ['except' => ['logout','verify']]);
    }

    public function verify()
    {
        // if (is_null(auth('customer')->user())){
        //     return redirect('/login');
        // }

        $email=request()->email;
        if($email){

            $customer=Customer::where('email',$email)->first();
        if ($customer->email_verified_at && $customer->status=='locked'){
            return redirect('/login?verify-message=true');
        }else if($customer->email_verified_at && $customer->status=='activated'){
            return Theme::scope('ecommerce.customers.verify', ['already_active' => true], 'plugins/ecommerce::themes.customers.verify')->render();
        }
        else{

            $key = 'VERIFICATION_URL_CUSTOMER_'.$customer->id;
            if (!Cache::has($key)){
                Cache::put($key,"generated",now()->addMinutes(5));
                $url = URL::signedRoute('customer.user-verify',['id'=>$customer->id],now()->addMinutes(1440));
                Mail::to($customer->email)->send(new VerificationAccountMail($url));
            }
        }
        return Theme::scope('ecommerce.customers.verify', [], 'plugins/ecommerce::themes.customers.verify')->render();


        }else{
            return redirect('/');
        }

    }

    public function userVerify($id)
    {
        $user = Customer::query()->findOrFail($id);
        $address = Address::where("customer_id",$id)->first();
        // Mail::to("a.allahverdi@m.icoa.it")->send(new VerificationAccountMail($user));
//        Mail::to("s.akbarzadeh@m.icoa.it")->send(new VerificationAccountMail(auth()->user()));
//        dd($request->all(),auth()->user()->email);

        if (is_null($user->email_verified_at)){
            $user->update(['email_verified_at'=>now()]);
            if (Cache::has('VERIFICATION_URL_CUSTOMER_'.$user->id))
            Cache::forget('VERIFICATION_URL_CUSTOMER_'.$user->id);
            DB::connection('mysql2')->table('fa_registered_customers')->insert([
                'id'=>$user->id,
                'name' => $user->name,
                'type'=>$user->type,
                'status'=>$user->status,
                'email' => $user->email,
                'created_at' => $user->created_at,
                'confirmed_at' => $user->confirmed_at,
                'email_verified_at' => $user->email_verified_at,
                'phone'=>$user->phone,
                'city'=>$address->city,
                'address'=>$address->address,
                'cap'=>$address->zip_code,
                'regione'=>$address->state,
            ]);
            Mail::to('a.allahverdi@icoa.it')->send(new UserRegisteredNotif($user));
            return redirect('/login?verify_message=neutral');
        }else if($user->email_verified_at && $user->status == 'locked'){
            return redirect('/login?verify_message=true');
        }else{
            return redirect('/login');
        }


    }

    public function showLoginForm()
    {
        SeoHelper::setTitle(__('Login'));

        Theme::breadcrumb()->add(__('Home'), route('public.index'))->add(__('Login'), route('customer.login'));

        if (! session()->has('url.intended')) {
            if (! in_array(url()->previous(), [route('customer.login'), route('customer.register')])) {
                session(['url.intended' => url()->previous()]);
            }
        }

        return Theme::scope('ecommerce.customers.login', [], 'plugins/ecommerce::themes.customers.login')->render();
    }

    protected function guard()
    {
        return auth('customer');
    }

    public function login(Request $request)
    {
        $request->merge(['email' => $request->input('email')]);
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to log in and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        $this->sendFailedLoginResponse();
    }

    public static function staticLogout($userId)
    {
        // Flush the session
        session()->flush();

        // Assuming you're using the default guard, you can call the Auth facade statically
        auth('customer')->logout();

        // Since there's no $request object available in this static context,
        // you can't directly call loggedOut($request). You might need to adjust this part.
        // Redirecting directly to '/' as an example.
        return redirect('/');
    }

    public function logout(Request $request)
    {
        $userId=$request->user('customer')->id;
        SaveCartController::logOut($userId);
        $request->session()->flush();
        $this->guard()->logout();
        return $this->loggedOut($request) ?: redirect('/');
    }

    protected function attemptLogin(Request $request)
    {
        if ($this->guard()->validate([
            filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'codice'=>$request->email,
            'password'=>$request->password
        ])) {
            $customer = $this->guard()->getLastAttempted();

            if (EcommerceHelper::isEnableEmailVerification() && empty($customer->confirmed_at)) {
                throw ValidationException::withMessages([
                    'confirmation' => [
                        __(
                            'The given email address has not been confirmed. <a href=":resend_link">Resend confirmation link.</a>',
                            [
                                'resend_link' => route('customer.resend_confirmation', ['email' => $customer->email]),
                            ]
                        ),
                    ],
                ]);
            }

            if ($customer->status->getValue() !== CustomerStatusEnum::ACTIVATED) {
                throw ValidationException::withMessages([
                    'email' => [
                        __('Your account has been locked, please contact the administrator.'),
                    ],
                ]);
            }

            SaveCartController::reCalculateCart($customer->id);
            SaveCartController::saveCart(session('cart'),$customer->id);

            // Check for first successful login
            if (is_null($customer->first_access)) {
                // It's the customer's first login
                $customer->first_access = now();
                $customer->last_access = now();
            } else {
                // It's a subsequent login
                $customer->last_access = now();
            }
            $customer->save(); // Save the changes


            return $this->baseAttemptLogin($request);
        }

        return false;
    }
}
