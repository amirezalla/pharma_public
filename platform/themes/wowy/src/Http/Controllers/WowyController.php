<?php

namespace Theme\Wowy\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\Ecommerce\Repositories\Interfaces\FlashSaleInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Theme\Http\Controllers\PublicController;
use Cart;
use EcommerceHelper;
use Illuminate\Http\Request;
use Theme;
use Theme\Wowy\Http\Resources\BrandResource;
use Theme\Wowy\Http\Resources\PostResource;
use Theme\Wowy\Http\Resources\ProductCategoryResource;
use Theme\Wowy\Http\Resources\ReviewResource;

class WowyController extends PublicController
{
    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxCart(Request $request, BaseHttpResponse $response)
    {
        if (! $request->ajax()) {
            return $response->setNextUrl(route('public.index'));
        }

        return $response->setData([
            'count' => Cart::instance('cart')->count(),
            'html' => Theme::partial('cart-panel'),
        ]);
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getFeaturedProducts(Request $request, BaseHttpResponse $response)
    {
        if (! $request->ajax() || ! $request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        $data = [];

        $products = get_featured_products(array_merge([
            'take' => (int)$request->input('limit', 8),
            'with' => [
                'slugable',
                'variations',
                'productLabels',
                'variationAttributeSwatchesForProductList',
            ],
        ], EcommerceHelper::withReviewsParams()));

        foreach ($products as $product) {
            $data[] = view(
                Theme::getThemeNamespace() . '::views.ecommerce.includes.product-item-small',
                compact('product')
            )->render();
        }

        return $response->setData($data);
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param PostInterface $postRepository
     * @return BaseHttpResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function ajaxGetPosts(Request $request, BaseHttpResponse $response, PostInterface $postRepository)
    {
        if (! $request->ajax() || ! $request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        $posts = $postRepository->getFeatured(4, ['slugable']);

        return $response
            ->setData(PostResource::collection($posts))
            ->toApiResponse();
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getFeaturedProductCategories(Request $request, BaseHttpResponse $response)
    {
        if (! $request->ajax() || ! $request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        $categories = get_featured_product_categories(['take' => null]);

        return $response->setData(ProductCategoryResource::collection($categories));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
//    public static function ajaxGetFeaturedBrands(Request $request, BaseHttpResponse $response)
//    {
//        if (! $request->ajax() || ! $request->wantsJson()) {
//            return $response->setNextUrl(route('public.index'));
//        }
//
//        $brands = get_featured_brands();
//
//        return $response->setData(BrandResource::collection($brands));
//    }
    public static function ajaxGetFeaturedBrands()
    {
        // if (! $request->ajax() || ! $request->wantsJson()) {
        //     return $response->setNextUrl(route('public.index'));
        // }

        $brands = get_featured_brands();

        return $brands;
    }
    public static function ajaxGetMarchi()
    {
        $cards = [
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/copertina-prontoleggo.jpg', 'title' => 'Prontoleggo – Occhiali da lettura','catalog' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/cat-prontoleggo-2023-settembre.pdf' ],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/evi-brand-italia.jpg', 'title' => 'Brand Italia – Linea antizanzare, maschere viso e linea arnica', 'catalog' => 'Description for card 2'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/nuvita.jpg', 'title' => 'Nuvita – Puericultura Leggera', 'catalog' => 'Description for card 3'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/evi-petformance.jpg', 'title' => 'Petformance – Articoli per la salute, il benessere e l’igiene di cani e gatti', 'catalog' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/pet-formance.pdf'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/test-rapidi.jpg', 'title' => 'Test Rapidi Professionali e Self Test', 'catalog' => 'Description for card 4'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/mix-mascherine.jpg', 'title' => 'Mascherine Protettive – FFP2 e Chirurgiche', 'catalog' => 'Description for card 4'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/img-termoscanner-pusiossimetri-catalogo-marigo.jpg', 'title' => 'Termoscanner e Pulsossimetri', 'catalog' => 'Description for card 4'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/cat-accessories.jpg', 'title' => 'Beautytime – Make up', 'catalog' => 'Description for card 4'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/cat-cosmetics.jpg', 'title' => 'Beautytime – Linea viso e Detersione', 'catalog' => 'Description for card 4'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/cat-make-up.jpg', 'title' => 'Beautytime – Accessori', 'catalog' => 'Description for card 4'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/cat-rose-gold.jpg', 'title' => 'Beautytime – Gold rose', 'catalog' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/linea-gold-rose-beautytime.pdf'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/cat-smokey-eye.jpg', 'title' => 'Beautytime – Smokey eye', 'catalog' => 'Description for card 11'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/cat-travel-set.jpg', 'title' => 'Beautytime – Travel set', 'catalog' => 'Description for card 11'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/cat-hair-accesories.jpg', 'title' => 'Beautytime – Lookrezia', 'catalog' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/linea-eco-beautytime.pdf'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/cat-lime.jpg', 'title' => 'Beautytime – Lime personalizzate', 'catalog' => 'Description for card 11'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/copertina-pasante-1.jpg', 'title' => 'Pasante – Profilattici', 'catalog' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/pasante.pdf'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/rowo-compresse-caldo-freddo.jpg', 'title' => 'Röwo – Compresse caldo freddo', 'catalog' => 'Description for card 11'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/catalog/copertina-prontoleggo-sunglasses.jpg', 'title' => 'Prontoleggo – Sunglasses', 'catalog' => ''],

            // Add more cards as needed
        ];

//        $categories = \Botble\Ecommerce\Models\ProductCategory::all();
//        return view('Brands.show', compact('cards',));
        return $cards;
    }
    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param ProductInterface $productRepository
     * @return BaseHttpResponse
     */
    public function ajaxGetProductReviews(
        $id,
        Request $request,
        BaseHttpResponse $response,
        ProductInterface $productRepository
    ) {
        if (! $request->ajax() || ! $request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        $product = $productRepository->getFirstBy([
            'id' => $id,
            'status' => BaseStatusEnum::PUBLISHED,
            'is_variation' => 0,
        ]);

        if (! $product) {
            abort(404);
        }

        $star = (int)$request->input('star');
        $perPage = (int)$request->input('per_page', 10);

        $reviews = EcommerceHelper::getProductReviews($product, $star, $perPage);

        if ($star) {
            $message = __(':total review(s) ":star star" for ":product"', [
                'total' => $reviews->total(),
                'product' => $product->name,
                'star' => $star,
            ]);
        } else {
            $message = __(':total review(s) for ":product"', [
                'total' => $reviews->total(),
                'product' => $product->name,
            ]);
        }

        return $response
            ->setData(ReviewResource::collection($reviews))
            ->setMessage($message)
            ->toApiResponse();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param ProductInterface $productRepository
     * @return BaseHttpResponse
     */
    public function ajaxGetRelatedProducts(
        $id,
        Request $request,
        BaseHttpResponse $response,
        ProductInterface $productRepository
    ) {
        if (! $request->ajax() || ! $request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        $product = $productRepository->findOrFail($id);

        $products = get_related_products($product, (int)$request->input('limit'));

        $data = [];
        foreach ($products as $product) {
            $data[] = view(
                Theme::getThemeNamespace() . '::views.ecommerce.includes.product-item',
                compact('product')
            )->render();
        }

        return $response->setData($data);
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param FlashSaleInterface $flashSaleRepository
     * @return BaseHttpResponse
     */
    public function ajaxGetFlashSales(
        Request $request,
        BaseHttpResponse $response,
        FlashSaleInterface $flashSaleRepository
    ) {
        if (! $request->ajax()) {
            return $response->setNextUrl(route('public.index'));
        }

        $flashSales = $flashSaleRepository->getModel()
            ->notExpired()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->with([
                'products' => function ($query) use ($request) {
                    $reviewParams = EcommerceHelper::withReviewsParams();

                    if (EcommerceHelper::isReviewEnabled()) {
                        $query->withAvg($reviewParams['withAvg'][0], $reviewParams['withAvg'][1]);
                    }

                    return $query
                        ->where('status', BaseStatusEnum::PUBLISHED)
                        ->limit((int) $request->input('limit', 2))
                        ->withCount($reviewParams['withCount']);
                },
            ])
            ->get();

        if (! $flashSales->count()) {
            return $response->setData([]);
        }

        $data = [];
        foreach ($flashSales as $flashSale) {
            foreach ($flashSale->products as $product) {
                if (! EcommerceHelper::showOutOfStockProducts() && $product->isOutOfStock()) {
                    continue;
                }

                $data[] = Theme::partial('flash-sale-product', compact('product', 'flashSale'));
            }
        }

        return $response->setData($data);
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetProducts(Request $request, BaseHttpResponse $response)
    {
        if (! $request->ajax() || ! $request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        $products = get_products_by_collections(array_merge([
            'collections' => [
                'by' => 'id',
                'value_in' => [$request->input('collection_id')],
            ],
            'take' => 8,
            'with' => [
                'slugable',
                'variations',
                'productCollections',
                'variationAttributeSwatchesForProductList',
            ],
        ], EcommerceHelper::withReviewsParams()));

        $data = [];
        foreach ($products as $product) {
            $data[] = view(
                Theme::getThemeNamespace() . '::views.ecommerce.includes.product-item',
                compact('product')
            )->render();
        }

        return $response->setData($data);
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetProductsByCategoryId(
        Request $request,
        BaseHttpResponse $response,
        ProductInterface $productRepository
    ) {
        if (! $request->ajax() || ! $request->wantsJson()) {
            return $response->setNextUrl(route('public.index'));
        }

        $categoryId = $request->input('category_id');

        if (! $categoryId) {
            return $response;
        }

        $products = $productRepository->getProductsByCategories(array_merge([
            'categories' => [
                'by' => 'id',
                'value_in' => [$categoryId],
            ],
            'take' => 8,
        ], EcommerceHelper::withReviewsParams()));

        $data = [];
        foreach ($products as $product) {
            $data[] = view(Theme::getThemeNamespace() . '::views.ecommerce.includes.product-item', compact('product'))
                ->render();
        }

        return $response->setData($data);
    }

    /**
     * @param Request $request
     * @param int $id
     * @param BaseHttpResponse $response
     * @return mixed
     */
    public function getQuickView(Request $request, $id, BaseHttpResponse $response)
    {
        if (! $request->ajax()) {
            return $response->setNextUrl(route('public.index'));
        }

        $product = get_products(array_merge([
            'condition' => [
                'ec_products.id' => $id,
                'ec_products.status' => BaseStatusEnum::PUBLISHED,
            ],
            'take' => 1,
            'with' => [
                'slugable',
                'tags',
                'tags.slugable',
                'options' => function ($query) {
                    return $query->with('values');
                },
            ],
        ], EcommerceHelper::withReviewsParams()));

        if (! $product) {
            return $response->setNextUrl(route('public.index'));
        }

        list($productImages, $productVariation, $selectedAttrs) = EcommerceHelper::getProductVariationInfo($product);

        return $response->setData(Theme::partial('quick-view', compact('product', 'selectedAttrs', 'productImages')));
    }
}
