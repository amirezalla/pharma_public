<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\MailLogController;
use App\Http\Controllers\MarchiController;
use Botble\Ecommerce\Jobs\OrderSubmittedJob;
use Botble\Ecommerce\Mail\OrderConfirmed;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\SPCController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\CreaSconto;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\CaptchaHandler;
use Botble\Ecommerce\Http\Controllers\Fronts\PublicCheckoutController;
use Botble\Ecommerce\Http\Controllers\Fronts\PublicCartController;
use App\Http\Controllers\SuggestionController;
use Botble\Ecommerce\Http\Controllers\OfferTypeController;





use App\Http\Controllers\QuestionnaireController;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;
\Illuminate\Support\Facades\Route::get('/import', function () {
    $products = \Botble\Ecommerce\Models\Product::all();
    try {
        return \Illuminate\Support\Facades\DB::transaction(function () {
            $items = \Botble\Ecommerce\Models\Product::all();
            foreach ($items as $item) {
                $item = collect($item)
                    ->put('u_id', $item->id)
                    ->forget(['id', 'original_price','created_at','updated_at', 'front_sale_price', 'product_collections','variation_info'])
                    ->mapWithKeys(function ($item, $key) {
                        if (is_object($item) && method_exists($item, 'getValue')){
                            $item = $item->getValue();
                        }elseif (is_array($item)){
                            $item =collect($item)->toJson();
                        }
                        return [$key => $item];
                    })->toArray();
                \Illuminate\Support\Facades\DB::connection('farma2')
                    ->table('ec_products')
                    ->updateOrInsert([
                        'u_id' => $item['u_id'],
                    ], $item);
            }
        });
    } catch (Throwable $e) {
        dd($e);
    }
});



Route::post('/welcome_mail', [QuestionnaireController::class, 'welcomeMail'])->name('welcomeMail');



Route::get('/importP', function () {
    return view('import');
});
Route::get('/importBrand', function () {
    return view('importbrand');
});
Route::get('/importTax', function () {
    return view('importtaxes');
});
//Route::get('/marchi', function () {
//    return view('Brands.show');
//});
//Route::get('/marchi', [MarchiController::class, 'index']);
// Route::prefix('/admin/ecommerce/questionnaires')
//     ->name('admin.ecommerce.questionnaires.')
//     ->middleware('auth')
//     ->group(function (Router $router) {
//         $router->get('/', [QuestionnaireController::class, 'questionnaires'])->name('index');
//         $router->get('/create', [QuestionnaireController::class, 'create'])->name('create');
//         $router->post('/store', [QuestionnaireController::class, 'store'])->name('store');
//         $router->post('/check-active-changes', [QuestionnaireController::class, 'checkActiveChanges'])->name('check-active-changes');
//         $router->put('/{id}', [QuestionnaireController::class, 'update'])->name('update');
//         $router->delete('/{questionnaire}', [QuestionnaireController::class, 'delete'])->name('delete');
//         $router->get('/{id}/edit', [QuestionnaireController::class, 'edit'])->name('edit');
//         $router->put('/{questionnaire}/active', [QuestionnaireController::class, 'active'])->name('active');
//         $router->put('/{questionnaire}/inactive', [QuestionnaireController::class, 'inactive'])->name('inactive');
//   });
// Route::get('/questionindex', [QuestionnaireController::class, 'index'])
//     ->middleware(['check.auth.customer'])
//     ->name('questionary.index');
// Route::get('/questionnaire/thank-you', [QuestionnaireController::class, 'thankYou'])
//     ->middleware(['check.auth.customer'])
//     ->name('questionnaire.thank-you');
// Route::post('/saveanswer', [QuestionnaireController::class, 'saveAnswers'])
//     ->middleware(['check.auth.customer'])
//     ->name('questionary.save-answers');
Route::prefix('/admin/ecommerce/questionnaires')
    ->name('admin.ecommerce.questionnaires.')
    ->middleware('auth')
    ->group(function (Router $router) {
        $router->get('/', [QuestionnaireController::class, 'questionnaires'])->name('index');
        $router->get('/create', [QuestionnaireController::class, 'create'])->name('create');
        $router->post('/store', [QuestionnaireController::class, 'store'])->name('store');
        $router->post('/check-active-changes', [QuestionnaireController::class, 'checkActiveChanges'])->name('check-active-changes');
        $router->get('/ajax/users', [QuestionnaireController::class, 'ajaxUsers'])->name('ajax-users');
        $router->get('/{id}', [QuestionnaireController::class, 'show'])->name('show');
        $router->put('/{id}', [QuestionnaireController::class, 'update'])->name('update');
        $router->delete('/{questionnaire}', [QuestionnaireController::class, 'delete'])->name('delete');
        $router->get('/{id}/edit', [QuestionnaireController::class, 'edit'])->name('edit');
        $router->post('/{id}/send-email/customers', [QuestionnaireController::class, 'sendEmailToCustomers'])->name('send-email-to-customers');
        $router->put('/{questionnaire}/active', [QuestionnaireController::class, 'active'])->name('active');
        $router->put('/{questionnaire}/inactive', [QuestionnaireController::class, 'inactive'])->name('inactive');

    });




    Route::prefix('/admin/ecommerce/offerte')
    ->name('admin.ecommerce.offerte.')

    ->group(function (Router $router) {
        $router->get('/create', [CreaSconto::class, 'getCreateView'])->name('create-view');
        $router->get('/exportOffer', [CreaSconto::class, 'exportOffer'])->name('exportOffer');
        $router->get('/', [CreaSconto::class, 'getListView'])->name('list-view');
        $router->post('/update-offer', [CreaSconto::class, 'updateOffer'])->name('update-offer');
        $router->post('/delete-offer', [CreaSconto::class, 'delete'])->name('delete-offer');
        $router->post('/checkProductHasActiveOffer', [CreaSconto::class, 'checkProductHasActiveOffer'])->name('checkProductHasActiveOffer');
        $router->post('/deactiveProductInoffer', [CreaSconto::class, 'deactiveProductInoffer'])->name('deactiveProductInoffer');
        $router->post('/exportOfferDetails', [CreaSconto::class, 'exportOfferDetails'])->name('exportOfferDetails');
        $router->post('/getOfferbyCustomerId', [CreaSconto::class, 'getOfferbyCustomerId'])->name('getOfferbyCustomerId');
        $router->post('/getStrumentOfUser', [CreaSconto::class, 'getStrumentOfUser'])->name('getStrumentOfUser');
        $router->post('/getListino', [CreaSconto::class, 'getListino'])->name('getListino');
        $router->post('/deactiveCustomerInoffer', [CreaSconto::class, 'deactiveCustomerInoffer'])->name('deactiveCustomerInoffer');
        $router->get('/edit/{id}', [CreaSconto::class, 'editView'])->name('edit');
        $router->post('/updateExpirationDate', [CreaSconto::class, 'updateExpirationDate'])->name('updateExpirationDate');


    });
Route::get('/admin/ecommerce/offertype', [OfferTypeController::class, 'view'])->name('admin.ecommerce.offertype.view');
Route::post('/admin/ecommerce/offertype/add', [OfferTypeController::class, 'add'])->name('admin.ecommerce.offertype.add');

    Route::get('/admin/ecommerce/offertelist', [CreaSconto::class, 'viewList'])->name('admin.ecommerce.offertype.view.list');
    Route::post('/get-customers-by-product', [CreaSconto::class, 'getCustomersByProduct']);
    Route::post('/filter-customers', [CreaSconto::class, 'filterCustomers']);
    Route::post('/checkIfBetter', [CreaSconto::class, 'CheckIfBetter']);
    Route::post('/saveOffer', [CreaSconto::class, 'saveOffer']);




Route::get('/questionindex', [QuestionnaireController::class, 'index'])
    ->middleware(['check.auth.customer'])
    ->name('questionary.index');

Route::post('/checkEmailAlreadyexists', [QuestionnaireController::class, 'checkEmailAlreadyexists']);

Route::get('/questionnaire/thank-you', [QuestionnaireController::class, 'thankYou'])
    ->middleware(['check.auth.customer'])
    ->name('questionnaire.thank-you');
Route::post('/saveanswer', [QuestionnaireController::class, 'saveAnswers'])
    ->middleware(['check.auth.customer'])
    ->name('questionary.save-answers');


    Route::post('/admin/spc_store', [SPCController::class, 'store']);
    Route::post('/admin/spc_deactive', [SPCController::class, 'deactive']);
    Route::post('/spc_apply', [SPCController::class, 'applyCoupon']);
    Route::post('/spc_remove', [SPCController::class, 'removeCoupon']);



        Route::prefix('/admin/ecommerce/spedizione')
        ->name('admin.ecommerce.spedizione.')
        ->group(function (Router $router) {

            $router->get('/view', [SPCController::class, 'getSpedizioneView'])->name('view');
            $router->get('/list', [SPCController::class, 'getSpedizioneListView'])->name('list');
            $router->get('/create-offer', [SPCController::class, 'createOfferView'])->name('create-offer');
            $router->post('/customers_export', [SPCController::class, 'customersExport'])->name('customers_export');
            $router->post('/delete', [SPCController::class, 'delete'])->name('delete');
            $router->post('/update', [CreaSconto::class, 'spedizioneUpdate'])->name('update');
            Route::get('/shipping-form', [ShippingController::class, 'showForm'])->name('shipping.form');
            Route::post('/calculate-shipping', [ShippingController::class, 'calculateShipping'])->name('calculate.shipping');


        });

        Route::prefix('/captcha-validator')->group(function (Router $router) {

            $router->post('/contact-form', [CaptchaHandler::class, 'validateContactForm'])->name('validateContactForm');
            $router->post('/register', [CaptchaHandler::class, 'validateRegisterForm'])->name('validateRegisterForm');
            $router->post('/login', [CaptchaHandler::class, 'validateLoginForm'])->name('validateLoginForm');



        });

        Route::prefix('/refresh-captcha')->group(function (Router $router) {

            $router->get('/contact-form', [CaptchaHandler::class, 'refreshContactForm'])->name('refreshContactForm');
            $router->get('/register', [CaptchaHandler::class, 'refreshRegisterForm'])->name('refreshRegisterForm');
            $router->get('/login', [CaptchaHandler::class, 'refreshLoginForm'])->name('refreshLoginForm');



        });

 Route::get('/pricelist', [\App\Http\Controllers\PricelistController::class, 'pricelist'])
     ->middleware(['check.auth.customer'])
     ->name('pricelist.index');
Route::get('/checksession', [QuestionnaireController::class, 'checksession']);

Route::get('/return', [PublicCheckoutController::class, 'paypalConfirmed']);
Route::get('/return-canceled-paypal', [PublicCheckoutController::class, 'paypalCanceled']);
Route::get('/retry-checkout', [PublicCheckoutController::class, 'retryCheckout']);

Route::prefix('/admin/ecommerce/mail-log')
    ->name('admin.ecommerce.mail-log.')
    ->group(function (Router $router) {

        $router->get('/list', [MailLogController::class, 'list'])->name('list');
        $router->get('/filter', [MailLogController::class, 'filter'])->name('filter');
        $router->post('/delete', [MailLogController::class, 'delete'])->name('delete');
        $router->post('/download', [MailLogController::class, 'download'])->name('download');

    });

    Route::get('/empty-cart', [PublicCartController::class, 'emptyCart']);


    Route::post('/saveInteraction', [SuggestionController::class, 'saveInteraction']);
    Route::post('/updateFilter', [FilterController::class, 'updateFilter']);
