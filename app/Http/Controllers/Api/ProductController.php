<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductResource;
use App\Models\Article;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    /**
     * 产品列表
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        AnonymousResourceCollection::wrap('list');// 资源列表默认返回 data 更换为 list

        $list = QueryBuilder::for(Product::class)
            ->orderBy('created_at', 'desc')
            ->where('status', '=', 0)
            //->select('id', 'name','price','price_usdt','price_fil','content','desc','thumb')
            ->paginate();

        return ProductResource::collection($list);
    }

    /**
     * 产品详情
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $product['buy_faq'] = Article::where('article_category_id', 5)
            ->where('status', 1)
            ->get();
        return new ProductResource($product);
    }
}
