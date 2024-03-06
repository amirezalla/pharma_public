<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Botble\Ecommerce\Models\SPC;
use App\Models\EmailLog;
use Botble\Ecommerce\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;



class MailLogController extends Controller
{

    public function list(){
        $list = EmailLog::orderBy('data_invio', 'desc')->paginate(15);
        return view('plugins/ecommerce::mail-log.list',compact('list'));
    }

    public function delete(){

    }

    public function download(Request $request){

            $id = $request->input('id');
            $log = EmailLog::find($id);
        if($log){
            $htmlData=$log->email;
            $response = Response::stream(function () use ($htmlData) {
                echo $htmlData;
            }, 200, [
                'Content-Type' => 'text/html',
                'Content-Disposition' => "attachment; filename='{$log->oggetto}.html'",
            ]);

            return $response;
        }else{
            return false;
        }



    }

public function filter(Request $request)
{
    $query = EmailLog::query();

    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where('codice_cliente', 'LIKE', "%{$search}%")
              ->orWhere('oggetto', 'LIKE', "%{$search}%")
              ->orWhere('nome_cliente', 'LIKE', "%{$search}%")
              ->orWhere('email_destinatario', 'LIKE', "%{$search}%");
    }

    if ($request->filled('from_date')) {
        $query->whereDate('data_invio', '>=', $request->input('from_date'));
    }

    if ($request->filled('to_date')) {
        $query->whereDate('data_invio', '<=', $request->input('to_date'));
    }

    $query->orderBy('data_invio', 'desc');
    $list = $query->paginate(15); // or any other number for pagination
    return view('plugins/ecommerce::mail-log.list',compact('list'));
}






}
