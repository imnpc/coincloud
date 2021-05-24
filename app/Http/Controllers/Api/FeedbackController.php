<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeedbackResource;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;

class FeedbackController extends Controller
{
    /**
     * 问题反馈列表
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        AnonymousResourceCollection::wrap('list');// 资源列表默认返回 data 更换为 list

        $list = QueryBuilder::for(Feedback::class)
            ->defaultSort('-created_at')
            ->where('user_id', '=', auth('api')->id())
            //->where('status', '=', 1)
            ->select('id', 'content', 'reply')
            ->paginate();

        return FeedbackResource::collection($list);
    }

    /**
     * 提交问题反馈
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'text' => 'required|string', // 反馈内容
        ]);

        $user = $request->user();

        Feedback::create([
            'user_id' => auth('api')->id(),
            'content' => $request->text,
        ]);

        $data['message'] = "问题反馈提交成功";
        return response()->json($data, 200);
    }

    /**
     * 问题反馈详情
     * Display the specified resource.
     *
     * @param \App\Models\Feedback $feedback
     * @return \Illuminate\Http\Response
     */
    public function show(Feedback $feedback)
    {
        return new FeedbackResource($feedback);
    }

}
