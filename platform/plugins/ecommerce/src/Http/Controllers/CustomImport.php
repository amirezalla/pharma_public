<?php

namespace Botble\Ecommerce\Http\Controllers;

use Assets;
use BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Exports\TemplateProductExport;
use Botble\Ecommerce\Http\Requests\BulkImportRequest;
use Botble\Ecommerce\Http\Requests\ProductRequest;
use Botble\Ecommerce\Imports\ProductImport;
use Botble\Ecommerce\Imports\ValidateProductImport;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\ProductAttribute;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Botble\Ecommerce\Mail\Welcome;

class CustomImport extends BaseController
{
    private function generateRandomString($length=8){
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $specialChars = '!@#$%^&*()_-+=<>?';  // You can modify this to include/exclude any special characters

        // Ensure one character from each set
        $password = [
            $lowercase[rand(0, strlen($lowercase) - 1)],
            $uppercase[rand(0, strlen($uppercase) - 1)],
            $numbers[rand(0, strlen($numbers) - 1)],
            $specialChars[rand(0, strlen($specialChars) - 1)]
        ];




        // All combined characters
        $allChars = $lowercase . $uppercase . $numbers . $specialChars;

        // Fill the rest
        for ($i = 4; $i < $length; $i++) {
            $password[] = $allChars[rand(0, strlen($allChars) - 1)];
        }

        // Shuffle and convert the array of characters back into a string
        shuffle($password);
        return implode('', $password);
    }

    function getCustomerTypeName($tipologia) {
        $customerTypes = [
            30 => 'Farmacia',
            31 => 'Parafarmacia',
            32 => 'Dentista',
            33 => 'StudioMedico',
            34 => 'AltroPharma',
            999=>'test'
        ];

        return $customerTypes[$tipologia] ?? null;
    }

    private function checkDeletedUsers(){
        $deletedUsers = DB::connection('mysql')->table('ec_customers')->where('status', 'deleted')->get();


        foreach ($deletedUsers as $deletedUser) {
            // Check if the user's codice exists in the source database
            $existsInSource = DB::connection('mysql2')->table('cli_cliente') // Replace with your source table name
                                ->where('codice', $deletedUser->codice)
                                ->exists();

            if ($existsInSource) {
                // Update the user's status to 'activated' in the destination database
                DB::connection('mysql')->table('ec_customers')
                    ->where('codice', $deletedUser->codice)
                    ->update(['status' => 'activated']);
            }
        }
        }

        public function regione(){
            // $this->str_scheda();
            $regione=DB::connection('mysql2')->select('select * from arc_regione where fk_nazione_id=118');
            $regUpdated=array();
            foreach($regione as $reg){
                $row=DB::connection('mysql')->table('cities')->updateOrInsert(
                    [
                        'id'=>$reg->pk_regione_id,
                        'name'=>$reg->nome
                    ],
                    ['state_id'=>$reg->pk_regione_id]);
                    if($row==true){
                        array_push($regUpdated, $row);
                    }
            }
            return view('plugins/ecommerce::customImport.clienti-foreign-keys', compact('regUpdated'));
        }

        private function agents(){
            $agents=DB::connection('mysql2')->select('select * from cli_agente');
            $agenteUpdated=array();
            foreach($agents as $agent){
                $row=DB::connection('mysql')->table('ec_agent')->updateOrInsert([
                    'id'=>$agent->pk_agente_id,
                    'codice'=>$agent->codice
                ],[
                    'nome'=>$agent->nome,
                    'cognome'=>$agent->cognome,
                    'tipologia'=>$agent->tipologia,
                    'email'=>$agent->email,
                    'cellulare'=>$agent->cellulare,
                ]);
                if($row==true){
                    array_push($agenteUpdated, $row);
                }
            }
            // if(empty($agenteUpdated)) return 'No connected record updated';
            // else return $agenteUpdated;



        }

    public function users() {

        $this->agents();
        $this->checkDeletedUsers();

        // $users = DB::connection('mysql2')->select('select * from cli_cliente where tipologia IN (30,31,32,33,34) and email IS NOT NULL');

        $users = DB::connection('mysql2')->select('select * from cli_cliente where tipologia IN (999) and email IS NOT NULL');

        foreach($users as $user) {
            $tipologia=$user->tipologia;
            $typeName = $this->getCustomerTypeName($tipologia);
            $exists = DB::connection('mysql')->table('ec_customers')->where('codice', $user->codice)->exists();
            if (!$exists) {
                if($user->email){
                    $registered = DB::connection('mysql')->table('ec_customers')->where('email', $user->email)->first();
                    if($registered){
                        DB::connection('mysql')->table('ec_customers')->where('email', $registered->email)->update([
                            'codice' => $user->codice,
                            'name' => $user->nome,
                            'type'=>$typeName,
                            'codice_fiscale' => $user->codice_fiscale,
                            'piva' => $user->piva,
                            'agent_id' => $user->fk_agente_id,
                            'region_id' => $user->fk_regione_id,
                            'pec' => $user->pec,
                            'status'=>'activated',
                            'flag_isola'=>$user->flag_isola
                        ]);
                        $registeredExists=DB::connection('mysql2')->table('fa_registered_customers')->where('email', $registered->email)->exists();
                        if($registeredExists){
                            DB::connection('mysql2')->table('fa_registered_customers')->where('email', $registered->email)->delete();
                        }
                    }
                    else{
                        $password = $this->generateRandomString();  // Generate the password only for new users
                        $email=$user->email;
                        $row=DB::connection('mysql')->table('ec_customers')->insert([
                            'id'=>$user->pk_cliente_id,
                            'codice' => $user->codice,
                            'name' => $user->nome,
                            'type'=>$typeName,
                            'status'=>'activated',
                            'codice_fiscale' => $user->codice_fiscale,
                            'piva' => $user->piva,
                            'password' => bcrypt($password),
                            'email' => $email,
                            'agent_id' => $user->fk_agente_id,
                            'region_id' => $user->fk_regione_id,
                            'pec' => $user->pec,
                            'flag_isola'=>$user->flag_isola
                        ]);
                        Mail::to($user->email)->send(new Welcome($user->nome,$user->email,$user->codice,$password));
                    }
                }
                else{
                    $password=null;
                    $email=null;
                    $row=DB::connection('mysql')->table('ec_customers')->insert([
                        'id'=>$user->pk_cliente_id,
                        'codice' => $user->codice,
                        'name' => $user->nome,
                        'type'=>$typeName,
                        'status'=>'activated',
                        'codice_fiscale' => $user->codice_fiscale,
                        'piva' => $user->piva,
                        'password' => bcrypt($password),
                        'email' => $email,
                        'agent_id' => $user->fk_agente_id,
                        'region_id' => $user->fk_regione_id,
                        'pec' => $user->pec,
                        'flag_isola'=>$user->flag_isola
                    ]);
                }


                $provincia=DB::connection('mysql2')->table('arc_provincia')->where('pk_provincia_id', $user->fk_provincia_id)->first();
                $regione=DB::connection('mysql2')->table('arc_regione')->where('pk_regione_id', $user->fk_regione_id)->first();

                DB::connection('mysql')->table('ec_customer_addresses')->insert([
                    'customer_id' => $user->pk_cliente_id,
                    'phone' => '0000000000',
                    'email' => $user->email?$user->email:'null@null.com',
                    'country' => "IT",
                    'zip_code' => $user->cap ? $user->cap:'00000',
                    'name' => $user->nome,
                    'is_default' => 1,
                    'city' => ($user->fk_regione_id)?$regione->nome:'NULL',
                    'address' => "default",
                    'state' => ($user->fk_provincia_id)?$provincia->nome:'NULL'
                ]);

            }elseif($exists){
                // Fetch the user from the 'ec_customers' table to check email and password.
                $existingUser = DB::connection('mysql')->table('ec_customers')->where('codice', $user->codice)->first();

                // If the user doesn't have an email and password in the 'ec_customers' table
                if (!$existingUser->email && !$existingUser->password) {

                    // Check if the user has an email in the other database ($user->email in this case)
                    if ($user->email) {

                        $password = $this->generateRandomString(); // Generate the password
                        Mail::to($user->email)->send(new Welcome($user->nome, $user->email, $user->codice, $password));

                        // Update the 'ec_customers' table with the generated password
                        DB::connection('mysql')->table('ec_customers')->where('codice', $user->codice)->update([
                            'password' => bcrypt($password),
                            'email' => $user->email,
                        ]);
                    }
                }else{
                    // If the user has the email and passowrd in the 'ec_customers' table
                    if($existingUser->email!==$user->email){  // But the email is different from 'cli_cliente'
                        DB::connection('mysql')->table('ec_customers')->where('codice', $user->codice)->update(['email'=>$user->email]);
                    }
                }

                // Update the rest of the user data in the 'ec_customers' table
                DB::connection('mysql')->table('ec_customers')->where('codice', $user->codice)->update([
                    'codice' => $user->codice,
                    'name' => $user->nome,
                    'type'=>$typeName,
                    'codice_fiscale' => $user->codice_fiscale,
                    'piva' => $user->piva,
                    'agent_id' => $user->fk_agente_id,
                    'region_id' => $user->fk_regione_id,
                    'pec' => $user->pec,
                    'flag_isola'=>$user->flag_isola
                ]);

                // Fetch province and region details
                $provincia = DB::connection('mysql2')->table('arc_provincia')->where('pk_provincia_id', $user->fk_provincia_id)->first();
                $regione = DB::connection('mysql2')->table('arc_regione')->where('pk_regione_id', $user->fk_regione_id)->first();

                // Update the user address details
                DB::connection('mysql')->table('ec_customer_addresses')->where('customer_id', $user->pk_cliente_id)->update([
                    'phone' => '0000000000',
                    'email' => $user->email ? $user->email : 'null@null.com',
                    'country' => "IT",
                    'zip_code' => $user->cap ? $user->cap : '00000',
                    'name' => $user->nome,
                    'is_default' => 1,
                    'city' => ($user->fk_regione_id) ? $regione->nome : 'NULL',
                    'address' => "default",
                    'state' => ($user->fk_provincia_id) ? $provincia->nome : 'NULL'
                ]);
            }
        }


        // Remove the users that are no longer in the source
        $sourceIds = array_column($users, 'codice');
        $allUserIds = DB::connection('mysql')->table('ec_customers')->pluck('codice')->toArray();
        $usersToRemove = array_diff($allUserIds, $sourceIds);
        DB::connection('mysql')
        ->table('ec_customers')
        ->whereIn('codice', $usersToRemove)
        ->update([
            'status' => 'Deleted',
            'email' => null,
            'password' => null
        ]);

        $usersUpdatedCount = count($users);


        $currentUrl = request()->url(); // Gets the URL with query parameters

        if (strpos($currentUrl, 'clientiImportSchedule') !== false) {
            echo'ok';
        }else{
            // return 'ok';
            return view('plugins/ecommerce::customImport.clienti-foreign-keys', compact('usersUpdatedCount'));
        }
    }

    private function updateRegistered(){

        //



    }


    public function sconto()
    {
        $inputs = request()->all();
        $products = $inputs['products'];
        $users = $inputs['users'];
        $region = $inputs['region'];
        $where = '';
        foreach ($region as $reg) {
            $where .= 'region_id=' . $reg . ' or';
        }
        $last = strrpos($where, ' or');
        $where = substr($where, 0, $last);
        $SourceProducts = DB::connection('mysql')->select('select id from ec_customers where ' . $where);

        foreach ($SourceProducts as $src) {
            array_push($users, $src->id);
        }
        dd($users);
    }




    public function taxes()
    {
        $taxes = DB::connection('mysql2')->select('select * from arc_codice_iva');
        $taxes_updated = array();
        foreach ($taxes as $tax) {
            $row = DB::connection('mysql')->table('ec_taxes')->updateOrInsert(
                [
                    'id' => $tax->pk_codice_iva_id,
                    'nome' => $tax->nome,
                ]
                ,
                [
                    'codice' => $tax->codice,
                    'desc' => $tax->descrizione,
                    'percentage' => $tax->percentuale,
                    'tipo' => $tax->tipo,
                    'status' => 'published',
                ]
            );
            if ($row == true) array_push($taxes_updated, $row);
        }
        if (empty($taxes_updated)) return 'No tax record updated';
        else return $taxes_updated;
    }

    public function brands()
    {
        $brands = DB::connection('mysql2')->select('select * from acq_fornitore');
        $brands_updated = array();
        foreach ($brands as $brand) {
            $row = DB::connection('mysql')->table('ec_brands')->updateOrInsert(
                [
                    'id' => $brand->pk_fornitore_id,
                ],
                [
                    'name' => $brand->nome,
                    'website'=>'/products?brands[]='.$brand->pk_fornitore_id,
                    'status' => 'published',
                    'order' => '0',
                ]
            );
            
            if ($row == true) {
                array_push($brands_updated, $row);
            }
        }
        if (empty($brands_updated)) return 'No brands record updated';
        else return $brands_updated;
    }


    public function importproduct()
    {

        // UPDATE BRANDS

//    $products=DB::connection('mysql2')->select('SELECT * FROM `art_articolo` WHERE categoria=6 OR categoria=15 OR categoria=17;');
            $products = DB::connection('mysql2')->table("art_articolo")->whereIn('categoria', [6, 15, 17])->whereIn('fk_linea_id', [443, 441, 439, 383, 295, 124])->get();
            $products = $products->map(function ($item) {
                return (array)$item;
            });
            $productsWithoutVariants=$products->filter(function ($item) {
                return !strlen($item['variante_1']);
            });

            $variant_keys = $products->map(function ($item) {
                if (!empty($item['variante_1'])) { // Check if 'variante_1' has a value
                    $words = explode(' ', $item['nome']);
                    return end($words); // Return the last word
                }
                return null; // Return null if 'variante_1' is empty
            })->filter()->unique();

            $variants = $products->filter(function ($item) {
                return !empty($item['variante_1']) || !empty($item['variante_2']) || !empty($item['variante_3']);
            })->groupBy(function ($item) use ($variant_keys) {
            //     // Split the product name into words.
            $words = explode(' ', $item['nome']);
                // Remove the last word if it's a variant key.
                $lastWord = end($words);
                if ($variant_keys->contains($lastWord)) {
                    array_pop($words);
                }
                // Return the product name without the variant as the group key.
                return implode(' ', $words);
            });
            

            // ->groupBy(function ($item) use ($variant_keys) {
            //     // Split the product name into words.
            //     $words = explode(' ', $item['nome']);
            //     // Remove the last word if it's a variant key.
            //     $lastWord = end($words);
            //     if ($variant_keys->contains($lastWord)) {
            //         array_pop($words);
            //     }
            //     // Return the product name without the variant as the group key.
            //     return implode(' ', $words);
            // });
            $brandsId = DB::connection('mysql2')->table("art_articolo")->select('fk_fornitore_id')->where('fk_fornitore_id', $products->pluck('fk_fornitore_id')->toArray())->get();
            $brandsId = collect($brandsId)->map(function ($item) {
                return (array)$item;
            })->pluck('fk_fornitore_id')->unique();
            $brands = DB::connection('mysql2')->table("acq_fornitore")->get();
            $brands = collect($brands)->map(function ($item) {
                return (array)$item;
            })->pluck('nome', 'pk_fornitore_id');

        Product::truncate();
        \Illuminate\Support\Facades\DB::table('ec_products_translations')->truncate();
        \Illuminate\Support\Facades\DB::table('ec_product_variation_items')->truncate();
        \Illuminate\Support\Facades\DB::table('ec_product_variations')->truncate();
        \Illuminate\Support\Facades\DB::table('ec_product_with_attribute_set')->truncate();
        \Illuminate\Support\Facades\DB::table('ec_tax_products')->truncate();
        \Illuminate\Support\Facades\DB::table('slugs')->where('prefix','products')->delete();

        $items = \Botble\Ecommerce\Models\Product::query()->get()->pluck('name')->toArray();

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($products,$productsWithoutVariants, $variants, $brands, $items) {

                foreach ($brands as $brand) {
                    $brandItem = \Botble\Ecommerce\Models\Brand::updateOrCreate([
                        'name' => $brand,
                    ], [
                        'name' => $brand
                    ]);
                    \Botble\Slug\Models\Slug::create([
                        'key' => $brand,
                        'reference_id' => $brandItem->id,
                        'reference_type' => $brandItem->getMorphClass(),
                        'prefix' => "brands"
                    ]);
                }
                foreach ($productsWithoutVariants as $productsWithoutVariant) {
                    $product_name = str_replace('&', 'and', trim($productsWithoutVariant['nome']));
                    $price = $productsWithoutVariant['prezzo'];
                    $taxId = $productsWithoutVariant['fk_codice_iva_id'];
                    $productId=$productsWithoutVariant['pk_articolo_id'];
                    if (in_array($product_name, $items)) {
                        $productItem = \Botble\Ecommerce\Models\Product::query()->where('name', $product_name)->first();
                        $productItem->update([
                            'description' => 'Description',
                            'price' => $price,
                            'tax_id' => $taxId,
                            'images' => collect([strtolower($productsWithoutVariant['codice']) . '.jpg'])->toJson(),
                        ]);
                    }else{
                        $productItem = $this->_generateProduct($productId,$product_name,$productsWithoutVariant,$price,$brands,$taxId);
                        // product_id va tax_id be table ec_tax_products ezafe shavad
                        $this->_generateTranslationProduct($product_name,$productItem);
                        $this->_generateSlugProduct($product_name,$productItem);
                    }
                    $productItem->categories()->sync([$productsWithoutVariant['fk_linea_id']]);
                }
                foreach ($variants as $variantItems) {
                    foreach ($variantItems as $item) {
                        if ($item['variante_2']) {
                            $order1 = intval(ProductAttribute::query()->where('attribute_set_id', 1)->max('order'));
                            ProductAttribute::query()->firstOrCreate([
                                'title' => $item['variante_2'],
                                'attribute_set_id' => 1,
                            ], [
                                'title' => $item['variante_2'],
                                'slug' => Str::slug($item['variante_2']),
                                'attribute_set_id' => 1,
                                'status' => "published",
                                'order' => $order1 == 0 ? 0 : $order1++,
                            ]);
                        }
                        if ($item['variante_3']) {
                            $order2 = intval(ProductAttribute::query()->where('attribute_set_id', 3)->max('order'));
                            ProductAttribute::query()->firstOrCreate([
                                'title' => $item['variante_3'],
                                'attribute_set_id' => 3,
                            ], [
                                'title' => $item['variante_3'],
                                'slug' => Str::slug($item['variante_3']),
                                'attribute_set_id' => 3,
                                'status' => "published",
                                'order' => $order2 == 0 ? 0 : $order2++,
                            ]);
                        }
                    }
                }
                foreach ($variants as $product_name=>$products) {
                    $variationItems = collect($products)->map(function ($item){
                        return collect()->when(strlen($item['variante_2']),function (Collection $collection)use ($item){
                            $var2 = ProductAttribute::where('attribute_set_id',1)->where('title', $item['variante_2'])->first();
                            if ($var2) {
                                $collection->put('variante_2',$var2->toArray());
                            }
                        })->when(strlen($item['variante_3']),function (Collection $collection)use ($item){
                            $var3 = ProductAttribute::where('attribute_set_id',3)->where('title', $item['variante_3'])->first();
                            if ($var3) {
                                $collection->put('variante_3',$var3->toArray());
                            }
                        });
                    });
                    $product = collect($products)->first();
                    $price = $product ? $product['prezzo'] : 0;
                    $taxId= $product['fk_codice_iva_id'];
                    $productId=$product['pk_articolo_id'];
                    if (in_array(str_replace('&', 'and', trim($product_name)), $items)) {
                        $productItem = \Botble\Ecommerce\Models\Product::query()->where('name', str_replace('&', 'and', trim($product_name)))->first();
                        $productItem->update([
                            'description' => 'Description',
                            'price' => $price,
                            'tax_id' => $taxId,
                            'sku'=>$product['codice'],
                            'images' => collect([strtolower($product['codice']) . '.jpg'])->toJson(),
                        ]);
                    } else {
                        $productItem = $this->_generateProduct($productId,$product_name,$product,$price,$brands,$taxId);
                        $this->_generateTranslationProduct($product_name,$productItem);
                        $this->_generateSlugProduct($product_name,$productItem);
                        if ($variationItems->count()) {
                            $assigned = false;
                            foreach ($variationItems as $key=>$variationItem) {
                                if (count($variationItem)){
                                    $cProduct=$products->first(function ($item)use($variationItem){
                                        $variationItem=$variationItem->toArray();
                                        if (count($variationItem) == 1){
                                            return $item[array_key_first($variationItem)] == $variationItem[array_key_first($variationItem)]['title'];
                                        }else{
                                            return $item[array_key_first($variationItem)] == $variationItem[array_key_first($variationItem)]['title'] && $item[array_key_last($variationItem)] == $variationItem[array_key_last($variationItem)]['title'];
                                        }
                                    });
                                }else{
                                    $cProduct=null;
                                }
                                if ($cProduct && !$assigned) $assigned=true;
                                $productVariation = ProductVariation::create([
                                    'product_id' => Product::create([
                                        'name' => $productItem->name,
                                        'description' => $productItem->description,
                                        'price' => $productItem->price,
                                        'is_variation' => true,
                                        'cost_per_item' => null,
                                        'sku'=>$cProduct ? $cProduct['codice']:null,
                                        'tax_id' => $productItem->tax_id,
                                        'brand_id' => \Botble\Ecommerce\Models\Brand::where('name', $brands->toArray()[$product['fk_fornitore_id']])->first()->id,
                                        'images' => $cProduct ? collect([strtolower($cProduct['codice']) . '.jpg'])->toJson() : collect([strtolower($product['codice']) . '.jpg'])->toJson(),
                                    ])->id,
                                    'configurable_product_id' => $productItem->id,
                                    'is_default'=>$assigned
                                ]);
                                $productVariation->productAttributes()->attach($variationItem->pluck('id')->unique()->toArray());
                            }
                            DB::table("ec_product_with_attribute_set")->insert($variationItems->flatten(1)
                                ->pluck('attribute_set_id')
                                ->unique()
                                ->map(function ($item)use($productItem){
                                return [
                                    'product_id'=>$productItem->id,
                                    'attribute_set_id'=>$item,
                                    'order'=>0
                                ];
                            })->toArray());
                        }
                    }
                    $productItem->categories()->sync([$product['fk_linea_id']]);
                }
            });
        } catch (Throwable $e) {
            dd($e);
        }
        return redirect()->back()->withSuccess('IT WORKS!');
    }

    public function pricelist() {
        set_time_limit(600); // Extend maximum execution time
// Assuming 'codice' is not null or empty for relevant customers
$customerIds = Customer::whereNotNull('codice')->pluck('id');

        // Delete pricelist entries for customers not in the fetched list
        DB::connection('mysql')->table('ec_pricelist')->whereNotIn('customer_id', $customerIds)->delete();

        // Process cli_scontistica in chunks
        DB::connection('mysql2')->table('cli_scontistica')
            ->whereIn('fk_cliente_id', $customerIds)
            ->orderBy('fk_cliente_id') // Adjust with the appropriate primary key or indexed column
            ->chunk(15000, function ($oldRows) {
                $pricelist = [];

                foreach ($oldRows as $oldRow) {
                    $pricelist[] = [
                        'customer_id' => $oldRow->fk_cliente_id,
                        'product_id'  => $oldRow->fk_articolo_id,
                        'final_price' => $oldRow->prezzo,
                    ];
                }

                if (!empty($pricelist)) {
                    DB::connection('mysql')->table('ec_pricelist')->insert($pricelist);
                }
            });
            $pricelist = [];

            return view('plugins/ecommerce::customImport.foreign-keys', compact('pricelist'));
    }
    

    private function _generateProduct($productId,$product_name,$product,$price,$brands,$taxId)
    {
        $productItem = new \Botble\Ecommerce\Models\Product([
            'name' => str_replace('&', 'and', $product_name),
            'description' => 'Description',
            'price' => $price,
            'tax_id' => $taxId,
            'sku'=>$product['codice'],
            'brand_id' => \Botble\Ecommerce\Models\Brand::where('name', $brands->toArray()[$product['fk_fornitore_id']])->first()->id,
            'images' => collect([strtolower($product['codice']) . '.jpg'])->toJson(),
        ]);

        $productItem->id = $productId; // Manually setting the ID
        $productItem->save();

        $tax = Tax::find($taxId);
        if ($tax){
            DB::table('ec_tax_products')->insert([
                'product_id'=>$productItem->id,
                'tax_id'=>$tax->id,
            ]);
        }
        return $productItem;
    }

    private function _generateSlugProduct($product_name, $product)
    {
        return \Botble\Slug\Models\Slug::create([
            'key' => \Illuminate\Support\Str::slug(str_replace('&', 'and', trim($product_name))),
            'reference_id' => $product->id,
            'reference_type' => $product->getMorphClass(),
            'prefix' => "products"
        ]);
    }

    private function _generateTranslationProduct($product_name, $product)
    {
        return \Illuminate\Support\Facades\DB::table('ec_products_translations')->insert([
            'lang_code' => "en_US",
            'ec_products_id' => $product->id,
            'name' => str_replace('&', 'and', trim($product_name)),
        ]);
    }

}
