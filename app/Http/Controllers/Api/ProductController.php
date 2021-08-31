<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductResource;
use App\Models\Article;
use App\Models\Order;
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
//            ->orderBy('created_at', 'desc')
            ->orderBy('sort', 'asc')
//            ->where('status', '=', 0)
            //->select('id', 'name','price','price_usdt','price_fil','content','desc','thumb')
            ->paginate();
        foreach ($list as $k => $v) {
            $buy = 0;
            $list[$k]['sale_rate'] = 0;
            if ($v->stock > 0) {
                $buy = Order::where('product_id', '=', $v->id)
                    ->where('pay_status', '=', Order::PAID_COMPLETE)
                    ->sum('number'); // 购买 T 数
                $list[$k]['sale_rate'] = number_fixed(($buy / $v->stock) * 100,2);
            }
        }
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
        $product['sale_rate'] = 0;
        if ($product->stock > 0) {
            $buy = Order::where('product_id', '=', $product->id)
                ->where('pay_status', '=', Order::PAID_COMPLETE)
                ->sum('number'); // 购买 T 数
            $product['sale_rate'] = number_fixed(($buy / $product->stock) * 100,2);
        }
        $product['buy_faq'] = Article::where('article_category_id', 5)
            ->where('status', 1)
            ->get();
        return new ProductResource($product);
    }
}
