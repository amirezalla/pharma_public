<?php

namespace Botble\Ecommerce\Http\Controllers;

use App\Traits\HasBackupTable;
use Assets;
use BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Exports\TemplateProductExport;
use Botble\Ecommerce\Http\Requests\BulkImportRequest;
use Botble\Ecommerce\Http\Requests\ProductRequest;
use Botble\Ecommerce\Imports\ProductImport;
use Botble\Ecommerce\Imports\ValidateProductImport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Excel;
use Hash;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Throwable;

class CustomExport extends BaseController
{
    use HasBackupTable;

    function isConnected($connectionName = 'mysql') {
        try {
            $pdo = DB::connection($connectionName)->getPdo();

            if($pdo) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            // You might want to log the exception here or handle it in some other way.
            return false;
        }
    }

    public function customer()
    {
        $this->generateBackupTable(['ec_customers']);
    }

    public function product()
    {
        $this->generateBackupTable(['ec_products']);
    }

    public function productToDb()
    {
        try {
            return \Illuminate\Support\Facades\DB::transaction(function () {
                $items = \Botble\Ecommerce\Models\Product::all();
                foreach ($items as $item) {
                    $item = collect($item)
                        ->put('u_id', $item->id)
                        ->forget(['id', 'confezione', 'linea_id', 'original_price', 'front_sale_price', 'product_collections'])
                        ->mapWithKeys(function ($item, $key) {
                            if (str_ends_with($key,'_at')) {
                                $item = date('Y-m-d H:i:s' , strtotime($item));
                            } elseif (is_object($item) && method_exists($item, 'getValue')) {
                                $item = $item->getValue();
                            } elseif (is_array($item)) {
                                $item = collect($item)->toJson();
                            }
                            return [$key => $item];
                        })->toArray();
                    \Illuminate\Support\Facades\DB::connection('mysql2')
                        ->table('fa_ec_products')
                        ->updateOrInsert([
                            'u_id' => $item['u_id'],
                        ], $item);
                }
                return redirect()->back();
            });
        } catch (Throwable $e) {
            return redirect()->back();
        }
    }
    public function dbconection()
    {
        try {
            $pdo = DB::connection('mysql2')->getPdo();

            if($pdo instanceof PDO) {
                echo "Connected to mysql2";
            } else {
                echo "Not connected to mysql2";
            }

        } catch (\Exception $e) {
            // This catch block will handle any exceptions that may arise, such as if the connection string is invalid.
            echo "Failed to connect to mysql2. Error: " . $e->getMessage();
        }
    }
    public function customerToDb2()
    {
        try {
            return \Illuminate\Support\Facades\DB::transaction(function () {
                $items = \Botble\Ecommerce\Models\Customer::all();
                foreach ($items as $item) {
                    $item = collect($item)
                        ->put('u_id', $item->id)
                        ->forget(['id',])
                        ->mapWithKeys(function ($item, $key) {
                            if (str_ends_with($key,'_at')) {
                                $item = date('Y-m-d H:i:s' , strtotime($item));
                            } elseif (is_object($item) && method_exists($item, 'getValue')) {
                                $item = $item->getValue();
                            } elseif (is_array($item)) {
                                $item = collect($item)->toJson();
                            }
                            return [$key => $item];
                        })->toArray();
                    \Illuminate\Support\Facades\DB::connection('mysql2')
                        ->table('fa_ec_customers')
                        ->updateOrInsert([
                            'u_id' => $item['u_id'],
                        ], $item);
                }
                return redirect()->back();
            });
        } catch (Throwable $e) {
            dd($e);
            return redirect()->back();
        }
    }

    public function orderToDb()
    {

        try {
            return \Illuminate\Support\Facades\DB::transaction(function () {
                $items = \Botble\Ecommerce\Models\Order::all();
                foreach ($items as $item) {
                    $item = collect($item)
                        ->put('u_id', $item->id)
                        ->forget(['id','confezione'])
                        ->mapWithKeys(function ($item, $key) {
                            if (str_ends_with($key,'_at')) {
                                $item = date('Y-m-d H:i:s' , strtotime($item));
                            } elseif (is_object($item) && method_exists($item, 'getValue')) {
                                $item = $item->getValue();
                            } elseif (is_array($item)) {
                                $item = collect($item)->toJson();
                            }
                            return [$key => $item];
                        })->toArray();
                    \Illuminate\Support\Facades\DB::connection('mysql2')
                        ->table('fa_ec_orders')
                        ->updateOrInsert([
                            'u_id' => $item['u_id'],
                        ], $item);
                }
                return redirect()->back();
            });
        } catch (Throwable $e) {
            return redirect()->back();
        }
    }

    public function order()
    {
        $this->generateBackupTable(['ec_orders']);
    }

    public function viewdiscount($id)
    {
        $discounts = DB::connection('mysql')->select('select * from ec_pricelist where discount_id=' . "'" . $id . "'");
        $products = array();
        $users = array();
        foreach ($discounts as $discount) {
            $pr = DB::connection('mysql')->select('select * from ec_products where id=' . "'" . $discount->product_id . "'");
            array_push($products, $pr[0]);
            $user = DB::connection('mysql')->select('select * from ec_customers where id=' . "'" . $discount->customer_id . "'");
            array_push($users, $user[0]);
            $percentage = (1 - ($discount->final_price / $discount->price)) * 100;
        }
        // $products = array_values(array_unique($products));
        // $users = array_values(array_unique($users));
        $users = collect($users);
        $users = $users->unique()->values()->all();
        $products = collect($products);
        $products = $products->unique()->values()->all();


        return view('plugins/ecommerce::discounts.view', compact('products', 'users', 'percentage'));
    }


    public function sconto()
    {
        $inputs = request()->all();
        $name = $inputs['discount-name'];
        $percentage = $inputs['discount-percentage'];
        $products = $inputs['products'];
        $ec_products = array();
        foreach ($products as $product) {
            $pr = DB::connection('mysql')->select('select id,price from ec_products where sku=' . "'" . $product . "'");
            array_push($ec_products, $pr[0]);
        }
        $users = $inputs['users'];
        if (request()->has('region')) {
            $region = $inputs['region'];
            $where = '';
            foreach ($region as $reg) {
                $where .= ' region_id=' . $reg . ' or';
            }
            $last = strrpos($where, ' or');
            $where = substr($where, 0, $last);
            $SourceProducts = DB::connection('mysql')->select('select id from ec_customers where ' . $where);
            foreach ($SourceProducts as $src) {
                array_push($users, $src->id);
            }
        }

        $users = array_map('intval', $users);
        $discount_id = $randomId = rand(1000000, 9999999);
        foreach ($users as $user) {
            foreach ($ec_products as $product) {
                DB::connection('mysql')->table('ec_pricelist')->where('customer_id', $user)->where('product_id', $product->id)->where('status', 'activated')->update([
                    'status' => 'deactivated',
                ]);
                $row = DB::connection('mysql')->table('ec_pricelist')->insert(
                    [
                        'customer_id' => $user,
                        'product_id' => $product->id,
                        'discount_id' => $discount_id,
                        'price' => $product->price,
                        'final_price' => ($product->price * (100 - $percentage)) / 100,
                        'status' => 'activated'
                    ]);


            }
        }
        DB::connection('mysql')->table('ec_discounts')->insert([
            'id' => $discount_id,
            'title' => $name,
            'code' => $discount_id,
            'value' => $percentage
        ]);
        return redirect('https://dev.marigo.collaudo.biz/admin/discounts');

    }


    public function foreignKeys()
    {
        $taxes = $this->taxes();
        $brands = $this->brands();
        $linee = $this->linea();
        $strumenti = $this->strumenti();
        $this->expiring();
        return view('plugins/ecommerce::customImport.foreign-keys', compact('taxes', 'brands', 'linee', 'strumenti'));

    }

    public function expiring()
    {
        $oldProducts = DB::connection('mysql2')->select('select * from cli_acquistato where scadenza > CURDATE()');
        foreach ($oldProducts as $oldProduct) {
            $product_id = $oldProduct->fk_articolo_id;
            $product = DB::connection('mysql2')->select("select * from art_articolo where pk_articolo_id=$product_id");
            $data = ['client_id' => $oldProduct->fk_cliente_id,
                'product' => $product[0]->codice,
                'scadenza' => $oldProduct->scadenza
            ];
            $row = DB::connection('mysql')->table('ec_oldProducts')->insertOrIgnore($data);
        }
    }

    public function consumabili()
    {
        $SourceProducts = DB::connection('mysql2')->select('select * from art_articolo where flag_scheda_strumento=0 and categoria=1 ');
        $UpdatedProducts = array();
        foreach ($SourceProducts as $SourceProduct) {
            $row = DB::connection('mysql')->table('ec_products')->updateOrInsert([
                'sku' => $SourceProduct->codice,
            ], [
                'price' => $SourceProduct->prezzo,
                'name' => $SourceProduct->nome,
                'linea_id' => $SourceProduct->fk_linea_id,
                'brand_id' => $SourceProduct->fk_fornitore_id,
                'tax_id' => $SourceProduct->fk_codice_iva_id,
                'confezione' => $SourceProduct->confezione,
                'status' => 'published',
                'length' => $SourceProduct->lunghezza,
                'wide' => $SourceProduct->profondita,
                'height' => $SourceProduct->altezza,
                'weight' => $SourceProduct->peso,
                'product_type' => 'physical'
            ]);
            $ec = DB::connection('mysql')->select("select * from ec_products where product_type='physical' ");
            foreach ($ec as $e) {
                DB::connection('mysql')->table('ec_product_category_product')->updateOrInsert(
                    [
                        'category_id' => 31,
                        'product_id' => $e->id,
                    ]
                );
            }
            if ($row == true) array_push($UpdatedProducts, $row);
        }
        return view('plugins/ecommerce::customImport.success', compact('UpdatedProducts'));

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
        $products = DB::connection('mysql')->select("select * from ec_products where product_type='physical'");
        foreach ($products as $product) {
            $row = DB::connection('mysql')->table('ec_tax_products')->updateOrInsert(['tax_id' => $product->tax_id, 'product_id' => $product->id], []);
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
                    'name' => $brand->nome,
                ]
                ,
                [
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

    public function linea()
    {
        $linee = DB::connection('mysql2')->select('select * from art_linea');
        $linea_updated = array();
        foreach ($linee as $linea) {
            $row = DB::connection('mysql')->table('ec_lines')->updateOrInsert(
                [
                    'id' => $linea->pk_linea_id,
                    'nome' => $linea->nome,
                    'linea' => $linea->linea
                ]
                ,
                [

                    'categoria' => NULL,
                    'gruppo' => NULL,
                    'status' => 'published'
                ]
            );
            if ($row == true) {
                array_push($linea_updated, $row);
            }
        }
        if (empty($linea_updated)) return 'No linee record updated';
        else return $linea_updated;
    }


    public function strumenti()
    {

        $strumenti = DB::connection('mysql2')->select('select * from art_articolo where flag_scheda_strumento=1');
        $strumentiUpdated = array();
        foreach ($strumenti as $strumento) {
            $ConnectedStrumenti = $this->ConnectedStrumenti($strumento);

            $row = DB::connection('mysql')->table('ec_products')->updateOrInsert(
                [
                    'id' => $strumento->pk_articolo_id,
                    'name' => $strumento->nome,

                ]
                ,
                [
                    'status' => 'published',
                    'product_type' => 'digital',
                    'image' => 'products/3.jpg',
                    'price' => 0,
                    'linea_id' => $strumento->fk_linea_id,
                    'brand_id' => $strumento->fk_fornitore_id,
                    'sku' => $strumento->codice

                ]
            );
            $row = DB::connection('mysql')->table('ec_product_tags')->updateOrInsert(
                [
                    'id' => $strumento->codice,
                    'name' => $strumento->nome,
                ]
                ,
                [
                    'status' => 'published',
                ]
            );

            $ec = DB::connection('mysql')->select("select * from ec_products where product_type='digital'");
            foreach ($ec as $e) {
                DB::connection('mysql')->table('ec_product_category_product')->updateOrInsert(
                    [
                        'category_id' => 32,
                        'product_id' => $e->id,
                    ]
                );
                // DB::connection('mysql')->table('ec_product_tags')->updateOrInsert(
                //     [
                //         'id' => $e->id,

                //     ]
                //     ,
                //     [
                //         'name' => $e->name,
                //         'status'=>'published'
                //     ]
                // );
                DB::connection('mysql')->table('slugs')->updateOrInsert(
                    [
                        'reference_id' => $e->id,
                        'reference_type' => 'Botble\Ecommerce\Models\Product',

                    ]
                    ,
                    [
                        'key' => $e->name,
                        'prefix' => 'products',

                    ]
                );
                DB::connection('mysql')->table('slugs')->updateOrInsert(
                    [
                        'reference_id' => $e->id,
                        'reference_type' => 'Botble\Ecommerce\Models\ProductTag'
                    ]
                    ,
                    [
                        'key' => $e->name,
                        'prefix' => 'product-tags',

                    ]
                );
            }


            if ($row == true) {
                array_push($strumentiUpdated, $row);
            }
        }
        if (empty($strumentiUpdated)) return 'No strumenti record updated';
        else return $strumentiUpdated;

    }


    public function ConnectedStrumenti($product)
    {
        $row = DB::connection('mysql')->table('ec_product_tag_product')->updateOrInsert([
            'product_id' => $product->codice,
            'tag_id' => $product->codice,
        ], []);
        $Impegnos = [
            $product->fk_impegno1_id,
            $product->fk_impegno2_id,
            $product->fk_impegno3_id,
            $product->fk_impegno4_id,
            $product->fk_impegno5_id,
            $product->fk_impegno6_id,
            $product->fk_impegno7_id,
            $product->fk_impegno8_id
        ];
        if (!empty($Impegnos)) {

            $PairUpdated = array();
            foreach ($Impegnos as $Impegno) {
                if ($Impegno != 0) {
                    //get struments of that impegno
                    $tags = DB::connection('mysql2')->select("select * from art_articolo where flag_scheda_strumento=0 and (fk_impegno1_id=$Impegno or fk_impegno2_id=$Impegno or fk_impegno3_id=$Impegno or fk_impegno4_id=$Impegno or fk_impegno5_id=$Impegno or fk_impegno6_id=$Impegno or fk_impegno7_id=$Impegno or fk_impegno8_id=$Impegno )");

                    foreach ($tags as $tag) {
                        $row = DB::connection('mysql')->table('ec_product_tag_product')->updateOrInsert(
                            [
                                'product_id' => $tag->codice,
                                'tag_id' => $product->codice,
                            ], []);
                        if ($row == true) {
                            array_push($PairUpdated, $row);
                        }
                    }
                }
            }

            if (empty($PairUpdated)) return 'No connected record updated';
            else return $PairUpdated;
        } else {
            return false;
        }


    }


    public function users()
    {
        $users = DB::connection('mysql2')->select('select * from cli_cliente where tipologia=1 or tipologia=2 or tipologia=3 or tipologia=4 or tipologia=5 or tipologia=18 or tipologia=19 and email IS NOT NULL');
        $usersUpdated = array();
        foreach ($users as $user) {
            $row = DB::connection('mysql')->table('ec_customers')->updateOrInsert(
                [
                    'id' => $user->pk_cliente_id,
                    'codice' => $user->codice,
                    'name' => $user->nome
                ],
                [
                    'email' => $user->email,
                    'region_id' => $user->fk_regione_id,
                    'pec' => $user->pec,
                    'codice_fiscale' => $user->codice_fiscale,
                    'piva' => $user->piva
                ]);
            if ($row == true) {
                array_push($usersUpdated, $row);
            }
        }
        if (empty($usersUpdated)) return 'No connected record updated';
        else return $usersUpdated;


    }

    public function regione()
    {
        $regione = DB::connection('mysql2')->select('select * from arc_regione where fk_nazione_id=118');
        $regUpdated = array();
        foreach ($regione as $reg) {
            $row = DB::connection('mysql')->table('cities')->updateOrInsert(
                [
                    'id' => $reg->pk_regione_id,
                    'name' => $reg->nome
                ],
                ['state_id' => $reg->pk_regione_id]);
            if ($row == true) {
                array_push($regUpdated, $row);
            }
        }
        if (empty($regUpdated)) return 'No connected record updated';
        else return $regUpdated;


    }

    private function user_tags()
    {

    }


}
