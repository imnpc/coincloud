<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleController extends Controller
{
    /**
     * 文章列表
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        AnonymousResourceCollection::wrap('list');// 资源列表默认返回 data 更换为 list

        $list = QueryBuilder::for(Article::class)
            ->allowedFilters([
                AllowedFilter::exact('article_category_id'),// 分类
            ])
            //->defaultSort('-created_at')
            ->orderBy('is_recommand','desc')
            ->orderBy('created_at','desc')
            ->where('status', '=', 1)
            ->paginate();

        return ArticleResource::collection($list);
    }

    /**
     * 文章详情
     * Display the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        return new ArticleResource($article);
    }
}
