<?php

namespace App\Http\Controllers;

use Botble\Ecommerce\Models\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Agent;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductTag;
use Botble\Ecommerce\Models\Regione;
use Botble\Ecommerce\Models\Offers;
use Botble\Ecommerce\Models\OffersDetail;
use Botble\Ecommerce\Models\offerType;
use Botble\Ecommerce\Models\PriceList;
use Botble\Ecommerce\Models\ProductCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use App\Jobs\OfferDeactivationJob;
use Carbon\Carbon;
//use LDAP\Result;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Response;
use Throwable;


class MarchiController extends BaseController
{
//    public function index()
//    {
//        $cards = [
//            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/copertina-prontoleggo.jpg', 'title' => 'Prontoleggo – Occhiali da letture', ],
//            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/evi-brand-italia.jpg', 'title' => 'Brand Italia – Linea antizanzare, maschere viso e linea arnica', 'description' => 'Description for card 2'],
//            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/nuvita.jpg', 'title' => 'Nuvita – Puericultura Leggera', 'description' => 'Description for card 3'],
//            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/evi-petformance.jpg', 'title' => 'Petformance – Articoli per la salute, il benessere e l’igiene di cani e gatti', 'description' => 'Description for card 4'],
//            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/test-rapidi.jpg', 'title' => 'Test Rapidi Professionali e Self Test', 'description' => 'Description for card 4'],
//            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/mix-mascherine.jpg', 'title' => 'Mascherine Protettive – FFP2 e Chirurgiche', 'description' => 'Description for card 4'],
//            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/img-termoscanner-pusiossimetri-catalogo-marigo.jpg', 'title' => 'Termoscanner e Pulsossimetri', 'description' => 'Description for card 4'],
//            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/cat-accessories.jpg', 'title' => 'Beautytime – Make up', 'description' => 'Description for card 4'],
//            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/cat-cosmetics.jpg', 'title' => 'Beautytime – Linea viso e Detersione', 'description' => 'Description for card 4'],
//            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/cat-make-up.jpg', 'title' => 'Beautytime – Accessori', 'description' => 'Description for card 4'],
//            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/cat-rose-gold.jpg', 'title' => 'Beautytime – Gold rose', 'description' => 'Description for card 11'],
//            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/cat-smokey-eye.jpg', 'title' => 'Beautytime – Smokey eye', 'description' => 'Description for card 11'],
//            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/cat-travel-set.jpg', 'title' => 'Beautytime – Travel set', 'description' => 'Description for card 11'],
//            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/cat-hair-accesories.jpg', 'title' => 'Beautytime – Lookrezia', 'description' => 'Description for card 11'],
//            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/cat-lime.jpg', 'title' => 'Beautytime – Lime personalizzate', 'description' => 'Description for card 11'],
//            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/copertina-pasante-1.jpg', 'title' => 'Pasante – Profilattici', 'description' => 'Description for card 11'],
//            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/rowo-compresse-caldo-freddo.jpg', 'title' => 'Röwo – Compresse caldo freddo', 'description' => 'Description for card 11'],
//            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/copertina-prontoleggo-sunglasses.jpg', 'title' => 'Prontoleggo – Sunglasses', 'description' => 'Description for card 11'],
//
//            // Add more cards as needed
//        ];
//
////        $categories = \Botble\Ecommerce\Models\ProductCategory::all();
//        return view('Brands.show', compact('cards',));
//    }
}
