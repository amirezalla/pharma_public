<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use Botble\Ecommerce\Mail\OrderConfirmed;
use Botble\Ecommerce\Models\Address;
use Botble\ACL\Traits\RegistersUsers;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Theme;
use SeoHelper;
use BaseHelper;
use EcommerceHelper;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationAccountMail;
use Botble\Ecommerce\Mail\RegisterReq;
class RegisterController extends Controller
{
    use RegistersUsers;

    protected string $redirectTo = '/';

    protected CustomerInterface $customerRepository;

    public function __construct(CustomerInterface $customerRepository)
    {
        $this->middleware('customer.guest');
        $this->customerRepository = $customerRepository;
    }

    public function showRegistrationForm()
    {
        SeoHelper::setTitle(__('Register'));

        Theme::breadcrumb()->add(__('Home'), route('public.index'))->add(__('Register'), route('customer.register'));

        if (!session()->has('url.intended')) {
            if (!in_array(url()->previous(), [route('customer.login'), route('customer.register')])) {
                session(['url.intended' => url()->previous()]);
            }
        }

        return Theme::scope('ecommerce.customers.register', [], 'plugins/ecommerce::themes.customers.register')
            ->render();
    }

    public function register(Request $request, BaseHttpResponse $response)
    {
//        $this->validator($request->input())->validate();
//
//
//        do_action('customer_register_validation', $request);

        $customer = $this->create($request->input());


        event(new Registered($customer));

        // if (EcommerceHelper::isEnableEmailVerification()) {
        //     return $this->registered($request, $customer)
        //         ?: $response
        //             ->setNextUrl(route('customer.login'))
        //             ->setMessage(__('We have sent you an email to verify your email. Please check and confirm your email address!'));
        // }

        $customer->confirmed_at = Carbon::now();
        $this->customerRepository->createOrUpdate($customer);
//        $this->guard()->login($customer); // dont login for the user and just redirect

        return $response->setNextUrl('users/verify?email='.$customer->email)
        ->setMessage("Registrato con successo! Ti abbiamo inviato un'e-mail per verificare il tuo account!");    }

    protected function validator(array $data)
    {
//        dd( $data);
        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:ec_customers',
            'password' => 'required|min:6|confirmed',
        ];

        if (is_plugin_active('captcha') && setting('enable_captcha') && get_ecommerce_setting(
                'enable_recaptcha_in_register_page',
                0
            )) {
            $rules += ['g-recaptcha-response' => 'required|captcha'];
        }

        if (request()->has('agree_terms_and_policy')) {
            $rules['agree_terms_and_policy'] = 'accepted:1';
        }


        $attributes = [
            'name' => __('Name'),
            'email' => __('Email'),
            'password' => __('Password'),
            'g-recaptcha-response' => __('Captcha'),
            'agree_terms_and_policy' => __('Term and Policy'),
        ];

        return Validator::make($data, apply_filters('ecommerce_customer_registration_form_validation_rules', $rules), [
            'g-recaptcha-response.required' => __('Captcha Verification Failed!'),
            'g-recaptcha-response.captcha' => __('Captcha Verification Failed!'),
        ], $attributes);
    }

    protected function create(array $data)
    {
//
        try {
            return DB::transaction(function () use ($data) {
                $customer = $this->customerRepository->create([
                    'name' => BaseHelper::clean($data['name']),
                    'email' => BaseHelper::clean($data['email']),
                    'password' => Hash::make($data['password']),
                    'type' => $data['type'],
                    'phone'=>$data['phone'],
                    'first_access' => Carbon::now(), // Add this line
                ]);
                Address::create([
                    'name' => BaseHelper::clean($data['name']),
                    'email' => BaseHelper::clean($data['email']),
                    'phone' => $data['phone'],
                    'state' => $data['state'],
                    'city' => $data['city'],
                    'address' => $data['address'],
                    'zip_code' => $data['zip_code'],
                    'customer_id' => $customer->id,
                    'is_default' => true,
                ]);
                return $customer;
            });
        } catch (\Throwable $e) {
            dd($e);
        }
//        Mail::to($order->user->email)->send(new OrderConfirmed($order));
    }

    protected function guard()
    {
        return auth('customer');
    }

    public function confirm(int $id, Request $request, BaseHttpResponse $response, CustomerInterface $customerRepository)
    {
        // if (!URL::hasValidSignature($request)) {
        //     abort(404);
        // }

        $customer = $customerRepository->findOrFail($id);

        $customer->confirmed_at = Carbon::now();
        $this->customerRepository->createOrUpdate($customer);

        $this->guard()->login($customer);

        return $response
            ->setNextUrl(route('customer.overview'))
            ->setMessage(__('You successfully confirmed your email address.'));
    }

    public function resendConfirmation(
        Request           $request,
        CustomerInterface $customerRepository,
        BaseHttpResponse  $response
    )
    {
        $customer = $customerRepository->getFirstBy(['email' => $request->input('email')]);

        if (!$customer) {
            return $response
                ->setError()
                ->setMessage(__('Cannot find this customer!'));
        }

        $customer->sendEmailVerificationNotification();

        return $response
            ->setMessage(__('We sent you another confirmation email. You should receive it shortly.'));
    }

    public function getVerify()
    {
        return view('plugins/ecommerce::themes.customers.verify');
    }
}
