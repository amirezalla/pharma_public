@php
//    $products=DB::connection('mysql2')->table("art_articolo")->whereIn('categoria',[6,15,17])->whereIn('fk_linea_id',[443,441,439,383,295,124])->get();
        $taxes=DB::connection('mysql2')->select('select * from arc_codice_iva');
               $taxes_updated=array();
               foreach ($taxes as $tax) {
                 $row=DB::connection('mysql')->table('ec_taxes')->updateOrInsert(
                   [
                       'id' => $tax->pk_codice_iva_id,

                   ]
                   ,
                   [
                       'nome' => $tax->nome,
                       'codice'=>$tax->codice,
            'desc'=>$tax->descrizione,
            'percentage'=>$tax->percentuale,
            'tipo'=>$tax->tipo,
            'status' => 'published',
//                       'title' => $tax->nome,
////                   'codice'=>$tax->codice,
////                   'desc'=>$tax->descrizione,
//                   'percentage'=>$tax->percentuale,
////                   'tipo'=>$tax->tipo,
//                   'status' => 'published',
                   ]
               );
               if($row==true) array_push($taxes_updated, $row);
               }
               if(empty($taxes_updated)) return 'No tax record updated';
               else return $taxes_updated;


@endphp
