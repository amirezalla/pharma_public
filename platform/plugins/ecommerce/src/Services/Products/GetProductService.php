<?php

namespace Botble\Ecommerce\Services\Products;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use EcommerceHelper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Botble\Ecommerce\Models\Wishlist;
use Illuminate\Support\Str;
use Botble\Ecommerce\Models\OffersDetail;
use Botble\Ecommerce\Models\Offers;

class GetProductService
{
    protected ProductInterface $productRepository;

    public function __construct(ProductInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getProduct(
        Request $request,
        $category = null,
        $brand = null,
        array $with = [],
        array $withCount = [],
        array $conditions = []
    ): Collection|LengthAwarePaginator {
        $num = (int)$request->input('num');
        $shows = EcommerceHelper::getShowParams();

        if (! array_key_exists($num, $shows)) {
            $num = (int)theme_option('number_of_products_per_page', 12);
        }

        $queryVar = [
            'keyword' => $request->input('q'),
            'brands' => (array)$request->input('brands', []),
            'categories' => (array)$request->input('categories', []),
            'tags' => (array)$request->input('tags', []),
            'collections' => (array)$request->input('collections', []),
            'attributes' => (array)$request->input('attributes', []),
            'max_price' => $request->input('max_price'),
            'min_price' => $request->input('min_price'),
            'sort_by' => $request->input('sort-by'),
            'num' => $num,
        ];

        if ($category) {
            $queryVar['categories'] = array_merge($queryVar['categories'], [$category]);
        }

        if ($brand) {
            $queryVar['brands'] = array_merge(($queryVar['brands']), [$brand]);
        }

        $countAttributeGroups = 1;
        if (count($queryVar['attributes'])) {
            $countAttributeGroups = DB::table('ec_product_attributes')
                ->whereIn('id', $queryVar['attributes'])
                ->distinct('attribute_set_id')
                ->count('attribute_set_id');
        }

        $orderBy = [
            'ec_products.order' => 'ASC',
            'ec_products.created_at' => 'DESC',
        ];

        if (! EcommerceHelper::isReviewEnabled() && in_array($queryVar['sort_by'], ['rating_asc', 'rating_desc'])) {
            $queryVar['sort_by'] = 'date_desc';
        }

        $params = array_merge([
                'paginate' => [
                    'per_page' => $queryVar['num'],
                    'current_paged' => (int)$request->query('page', 1),
                ],
                'with' => $with,
                'withCount' => $withCount,
            ], EcommerceHelper::withReviewsParams());

        switch ($queryVar['sort_by']) {
            case 'date_asc':
                $orderBy = [
                    'ec_products.created_at' => 'asc',
                ];

                break;
            case 'date_desc':
                $orderBy = [
                    'ec_products.created_at' => 'desc',
                ];

                break;
            case 'price_asc':
                $orderBy = [
                    'products_with_final_price.final_price' => 'asc',
                ];

                break;
            case 'price_desc':
                $orderBy = [
                    'products_with_final_price.final_price' => 'desc',
                ];

                break;
            case 'name_asc':
                $orderBy = [
                    'ec_products.name' => 'asc',
                ];

                break;
            case 'name_desc':
                $orderBy = [
                    'ec_products.name' => 'desc',
                ];

                break;
            case 'rating_asc':
                if (EcommerceHelper::isReviewEnabled()) {
                    $orderBy = [
                        'reviews_avg' => 'asc',
                    ];
                }

                break;
            case 'rating_desc':
                if (EcommerceHelper::isReviewEnabled()) {
                    $orderBy = [
                        'reviews_avg' => 'desc',
                    ];
                }

                break;
        }

        if (! empty($conditions)) {
            $params['condition'] = array_merge([
                'ec_products.status' => BaseStatusEnum::PUBLISHED,
                'ec_products.is_variation' => 0,
            ], $conditions);
        }

            $wishlist=array();
            if($request->input('wishlist')==1){
                $userid=$request->input('userid');
                $wishlist_ids=Wishlist::where('customer_id',$userid)->pluck('product_id')->toArray();
                if($wishlist_ids){
                    $query="SELECT * FROM `ec_products` WHERE product_type='physical' and (";
                    foreach($wishlist_ids as $wishlist_id){
                        $query.=" id = ".$wishlist_id." or";
                    }
                    $query = rtrim($query, " or");
                    $wishlist_list=DB::connection('mysql')->select($query." )");
                    if(!empty($wishlist_list)){
                        foreach($wishlist_list as $w){
                            array_push($wishlist,$w->id);
                        }
                    }
                }else{

                }
            }

            $discountedProducts=array();
            if($request->input('discounted')==1){
                $userid=$request->input('userid');
                $offerDetail=OffersDetail::where('customer_id',$userid)->where('status','active')->get();
                $discountedProducts=$offerDetail->pluck('product_id')->unique()->toArray();
            }

            $recentiProducts = [];
            $allProducts = [];
            $countProducts = []; // To hold the count of each ID
            
            if ($request->input('recenti') == 1) {
                $userid = $request->input('userid');
            
                // Fetching source orders based on fk_cliente_id
                $sourceOrders = DB::connection('mysql2')->select("SELECT * FROM cli_acquistato WHERE fk_cliente_id = ? ORDER BY fk_articolo_id ASC", [$userid]);
            
                foreach ($sourceOrders as $order) {
                    // Count the occurrences of each product ID
                    if (!isset($countProducts[$order->fk_articolo_id])) {
                        $countProducts[$order->fk_articolo_id] = 0;
                    }
                    $countProducts[$order->fk_articolo_id]++;
                    
                    $allProducts[] = ['id' => $order->fk_articolo_id, 'order' => $order->pk_acquistato_id];
                }
            
                // Sort the products based on the frequency of each ID
                usort($allProducts, function($a, $b) use ($countProducts) {
                    // If the counts are equal, sort by ID for consistency
                    if ($countProducts[$a['id']] == $countProducts[$b['id']]) {
                        return $a['id'] <=> $b['id'];
                    }
                    // Sort by count in descending order
                    return $countProducts[$b['id']] <=> $countProducts[$a['id']];
                });
            
                // Extracting the IDs ensuring uniqueness and maintaining the order based on count
                foreach ($allProducts as $product) {
                    if (!in_array($product['id'], $recentiProducts)) {
                        $recentiProducts[] = $product['id'];
                    }
                }
            }
            

        $products = $this->productRepository->filterProducts([
            'wish'=>$request->input('wishlist'),
            'wishlist'=>$wishlist,
            'disc'=>$request->input('discounted'),
            "discounted"=>$discountedProducts,
            "recenti"=>$recentiProducts,
            'keyword' => $queryVar['keyword'],
            'min_price' => $queryVar['min_price'],
            'max_price' => $queryVar['max_price'],
            'categories' => $queryVar['categories'],
            'tags' => $queryVar['tags'],
            'collections' => $queryVar['collections'],
            'brands' => $queryVar['brands'],
            'attributes' => $queryVar['attributes'],
            'count_attribute_groups' => $countAttributeGroups,
            'order_by' => $orderBy,
        ], $params);

        if ($queryVar['keyword'] && is_string($queryVar['keyword'])) {
            $products->setCollection(BaseHelper::sortSearchResults($products->getCollection(), $queryVar['keyword'], 'name'));
        }


        return $products;
    }
}
