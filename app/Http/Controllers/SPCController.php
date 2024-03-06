<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Botble\Ecommerce\Models\SPC;
use Botble\Ecommerce\Models\Segnalarsi;
use Botble\Ecommerce\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;



class SPCController extends Controller
{



    


    public  function getSegnalarsiList(){
        $list=Segnalarsi::paginate(15);
        return view('plugins/ecommerce::segnalarsi.list',compact('list'));
    }
    public function removeSegnal($id){

    }
    public function store(Request $request)
    {
        $request->validate([
            'couponcode' => 'required|string',
            'amount' => 'nullable|numeric',
            'min_price' => 'nullable|numeric',
            'max_price' => 'nullable|numeric',
            'start_date' => 'required|date',
            'expiring_date' => 'required|date',
            'couponType' => 'required|string',
            'once' => 'nullable|boolean',
            'users' => 'required',
        ]);

        if($request->start_date == now()->toDateString()){
            $status=true;
        }else{
            //Activate job
            $status=false;
        }
        // Creating a new SPC instance and storing data
        $spc = SPC::create([
            'code' => $request->couponcode,
            'type' => $request->couponType,
            'amount' => $request->amount,
            'min_order' => $request->min_price,
            'max_order' => $request->max_price,
            'start_date' => $request->start_date,
            'expiring_date' => $request->expiring_date,
            'once'=>$request->once,
            'status'=>$status
        ]);
        $users = $request->users;
        if ($users !== 'all') {
            foreach ($users as $userId) {
                $customer = Customer::find($userId);
                if ($customer) {
                    // Attaching Customer to SPC, assuming you have the relationship set up in your models
                    $spc->customers()->attach($customer->id, ['status' => $status]); // replace 'active' with the actual status you want to set
                }
            }
        }else{
            $customers = Customer::all();
            foreach($customers as $customer){
                $spc->customers()->attach($customer->id, ['status' => $status]); // replace 'active' with the actual status you want to set
            }
        }
        return response()->json(['success' => 'Data has been stored successfully!']);
    }


    public function delete(Request $request)
    {
        $spc = SPC::findOrFail($request->id);
        $spc->delete();

        return response()->json(['success' => 'Offer DELETED successfully.'], 200);
    }


    public function applyCoupon(Request $request)
    {
        // Retrieve the coupon code from the request
        $couponCode = $request->input('coupon_code');

        // Find the coupon by the provided code
        $coupon = SPC::where('code', $couponCode)->where('status', 1)->first();
        if (!$coupon) {
            return response()->json(['error' => 'Codice coupon non valido o inattivo'], 200);
        }

        // Check if the coupon has expired
        if (now() > $coupon->expiring_date) {
            return response()->json(['error' => 'Siamo spiacenti, ma Il codice Coupon che hai inserito non è più valido'], 200);
        }

        // Check if the coupon is yet to start
        if (now() < $coupon->start_date) {
            return response()->json(['error' => 'Il coupon non è ancora attivo'], 200);
        }

        // ... Additional validations like minimum order amount, user eligibility, etc ...
        if ($coupon->min_order != null && $request->order_amount < $coupon->min_order) {
            $dif = $coupon->min_order - $request->order_amount;
            $dif = $dif . '€';
            return response()->json(['error' => "Hai bisogno di $dif in più per applicare il coupon"], 200);
        }

        $authCustomerId = request()->user('customer')->id;

        if ($coupon->customers()->exists()) {
            // Check if the authenticated customer is allowed to use this coupon.
            if (!$coupon->customers()
                ->where('ec_spc_customers.customer_id', $authCustomerId)
                ->where('ec_spc_customers.status', 1)
                ->exists()) {
                // If the authenticated customer is not one of the attached customers, return an error response.
                return response()->json(['error' => "Questo coupon non è applicabile al tuo account"], 200);
            }
        }

        // Apply the coupon logic here e.g. calculate the discounted amount
        if ($coupon->type == 1) {
            $newTotal = $request->shipping_amount * ((100 - $coupon->amount) / 100);
        } elseif ($coupon->type == 2) {
            $newTotal = ($coupon->amount >= $request->shipping_amount) ? 0 : $request->shipping_amount - $coupon->amount;
        } else {
            $newTotal = 0;
        }

        $discountAmount = $request->shipping_amount - $newTotal;
        // If everything is ok, return the new order total or some other relevant information
        $request->session()->put('applied_spc', $coupon->code);
        $request->session()->put('discount_amount', $discountAmount);

        return response()->json(['success' => 'Coupon applicato con successo', 'new_total' => $newTotal, 'applied_spc' => $coupon->code, 'discount_amount' => $discountAmount], 200);

    }

    public function removeCoupon(Request $request)
    {
        // Retrieve the applied coupon code
        // This depends on where and how you have stored the applied coupon details, it could be session, database, etc.
        $appliedCouponCode = $request->session()->get('applied_spc');

        if (!$appliedCouponCode) {
            return response()->json(['error' => 'No coupon applied.'], 404);
        }

        // Reverse the effect of the coupon (e.g., remove the discount from the user's cart)
        // ...

        // Remove the coupon details from wherever you have stored it
        $request->session()->forget('applied_spc');

        return response()->json(['success' => 'Coupon rimosso con successo']);
    }

    public function getSpedizioneView(){
        $shipping_rules = [
            [
                'region' => 'Campania e Lazio',
                'type' => ['Farmacia', 'Parafarmacia', 'Altro Pharma'],
                'order_amount' => '<300 euros',
                'shipping_costs' => '10,00 euros'
            ],
            [
                'region' => 'Campania e Lazio',
                'type' => ['Farmacia', 'Parafarmacia', 'Altro Pharma'],
                'order_amount' => '>=300 euros',
                'shipping_costs' => '5,00 euros'
            ],
            [
                'region' => 'All other regions',
                'type' => ['Farmacia', 'Parafarmacia', 'Altro Pharma'],
                'order_amount' => 'any',
                'shipping_costs' => '10,00 euros'
            ],
            [
                'region' => 'Any',
                'type' => ['Studio Medico e Dentista'],
                'order_amount' => 'any',
                'shipping_costs' => 'To be determined' // add a specific cost if available
            ]
        ];
        page_title()->setTitle('Config spedizione');
        $spedizione = DB::select("SELECT * FROM config_spedizione");
        $spedizione=$spedizione[0];
        return view('plugins/ecommerce::spedizione.view',compact('spedizione','shipping_rules'));
    }

    public  function getSpedizioneListView(){
        $offers=SPC::all();
        return view('plugins/ecommerce::spedizione.offers-list',compact('offers'));
    }

    public function createOfferView(){
        page_title()->setTitle('Shipping offer creation');
        return view('plugins/ecommerce::spedizione.create-offer');
    }

    public function customersExport( Request $request ){
        $id=$request->input('id');
        $coupon = SPC::find($id);
        $csvData = "Codice cliente,Nome,email\n";

        foreach ($coupon->customers->all() as $customer) {

            $used = $customer->pivot->status == 0 ? 'used' : 'not used';
            $csvData .= "{$customer->codice},{$customer->name},{$customer->email},{$used}\n";
        }

        $response = Response::stream(function () use ($csvData) {
            echo $csvData;
        }, 200, [
            'filename'=>$coupon->code,
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename='{$coupon->code}.xlsx'",
        ]);

        return $response;



    }





}
