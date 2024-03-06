@php


    // UPDATE BRANDS

//    $products=DB::connection('mysql2')->select('SELECT * FROM `art_articolo` WHERE categoria=6 OR categoria=15 OR categoria=17;');

    $products=DB::connection('mysql2')->table("art_articolo")->whereIn('categoria',[6,15,17])->whereIn('fk_linea_id',[443,441,439,383,295,124])->get();
    $products = $products->map(function ($item){
            return (array)$item;
        });
    $brandsId=DB::connection('mysql2')->table("art_articolo")->select('fk_fornitore_id')->where('fk_fornitore_id',$products->pluck('fk_fornitore_id')->toArray())->get();
    $brandsId = collect($brandsId)->map(function ($item){
        return (array)$item;
    })->pluck('fk_fornitore_id')->unique();
    $brands=DB::connection('mysql2')->table("acq_fornitore")->get();
    $brands=collect($brands)->map(function ($item){
        return (array)$item;
    })->pluck('nome','pk_fornitore_id');
    $items = \Botble\Ecommerce\Models\Product::query()->get()->pluck('name')->toArray();
    $products = $products->unique(function ($item) use($items) {
            return trim($item['nome']);
        });
    try {
        \Illuminate\Support\Facades\DB::transaction(function ()use($products,$brands,$items){
            foreach ($brands as $brand){
                $brandItem =\Botble\Ecommerce\Models\Brand::updateOrCreate([
                    'name'=>$brand,
                ],[
                    'name'=>$brand
                ]);
//                if (!\Illuminate\Support\Facades\DB::table('')->where('lang_code',"en_US")->where('ec_brands_id')->count()){
//                    \Illuminate\Support\Facades\DB::table('ec_brands_translations')->insert([
//                        'lang_code'=>"en_US",
//                        'ec_brands_id'=>$brandItem->id,
//                        'name'=>$brand,
//                    ]);
//                }

                \Botble\Slug\Models\Slug::create([
                        'key'=>$brand,
                        'reference_id'=>$brandItem->id,
                        'reference_type'=>$brandItem->getMorphClass(),
                        'prefix'=>"brands"
                    ]);
            }
            foreach ($products as $product) {
                if (in_array(str_replace('&','and',trim($product['nome'])),$items)){
                    $productItem = \Botble\Ecommerce\Models\Product::query()->where('name',str_replace('&','and',trim($product['nome'])))->first();
                    $productItem->update([
                        'description' => 'Description',
                        'price' => $product['prezzo'],
                        'images' => collect([strtolower($product['codice']).'.jpg'])->toJson(),
                    ]);
                }else{
                    $productItem = \Botble\Ecommerce\Models\Product::query()->create([
                        'name' => str_replace('&','and',trim($product['nome'])),
                        'description' => 'Description',
                        'price' => $product['prezzo'],
                        'brand_id'=>\Botble\Ecommerce\Models\Brand::where('name',$brands->toArray()[$product['fk_fornitore_id']])->first()->id,
                        'images' => collect([strtolower($product['codice']).'.jpg'])->toJson(),
                    ]);
                    \Illuminate\Support\Facades\DB::table('ec_products_translations')->insert([
                        'lang_code'=>"en_US",
                        'ec_products_id'=>$productItem->id,
                        'name'=>str_replace('&','and',trim($product['nome'])),
                    ]);
                    \Botble\Slug\Models\Slug::create([
                        'key'=>\Illuminate\Support\Str::slug(str_replace('&','and',trim($product['nome']))),
                        'reference_id'=>$productItem->id,
                        'reference_type'=>$productItem->getMorphClass(),
                        'prefix'=>"products"
                    ]);
                }
                $productItem->categories()->sync([$product['fk_linea_id']]);
            }
        });
    }catch (Throwable $e){
        dd($e);
    }
dd($products,count($products));
    @dd($products);

    // foreach ($brands as $brand) {
    //     dump(DB::connection('mysql')->table('ec_brands')->updateOrInsert(
    //         [
    //             'id' => $brand->pk_fornitore_id,
    //             'name' => $brand->nome,
    //         ]
    //         ,
    //         [
    //         'status' => 'published',
    //         'order' => '0',
    //         ]
    //     ));
    // }

    // UPDATE taxes
    // $taxes=DB::connection('mysql2')->select('select * from arc_codice_iva');
    // foreach ($taxes as $tax) {
    //     dump(DB::connection('mysql')->table('ec_taxes')->updateOrInsert(
    //         [
    //             'id' => $tax->pk_codice_iva_id,
    //             'nome' => $tax->nome,
    //         ]
    //         ,
    //         [
    //         'codice'=>$tax->codice,
    //         'desc'=>$tax->descrizione,
    //         'percentage'=>$tax->percentuale,
    //         'tipo'=>$tax->tipo,
    //         'status' => 'published',
    //         ]
    //     ));
    // }


    // $taxes=DB::connection('mysql2')->select('select * from arc_codice_iva');
    // foreach ($taxes as $tax) {
    //     dump(DB::connection('mysql')->table('ec_taxes')->updateOrInsert(
    //         [
    //             'id' => $tax->pk_codice_iva_id,
    //             'nome' => $tax->nome,
    //         ]
    //         ,
    //         [
    //         'codice'=>$tax->codice,
    //         'desc'=>$tax->descrizione,
    //         'percentage'=>$tax->percentuale,
    //         'tipo'=>$tax->tipo,
    //         'status' => 'published',
    //         ]
    //     ));
    // }


        // $impegnos= App\Models\Impegno_articolo::all();
        // foreach ($impegnos as $impegno ) {
        //     dump($impegno->consumabili->name);
        // }


        // $c_i= App\Models\Customer_strument::all();
        // foreach ($c_i as $el ) {
        //     dump($el->customer->name);
        //     dump($el->strument->name);
        // }



@endphp
