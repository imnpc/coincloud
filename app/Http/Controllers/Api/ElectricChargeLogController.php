<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ElectricChargeLogResource;
use App\Models\ElectricChargeLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\QueryBuilder\QueryBuilder;

class ElectricChargeLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        AnonymousResourceCollection::wrap('list');// 资源列表默认返回 data 更换为 list

        $lists = QueryBuilder::for(ElectricChargeLog::class)
            ->defaultSort('-created_at')
            ->where('user_id', '=', auth('api')->id())
            ->paginate();

        return ElectricChargeLogResource::collection($lists);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ElectricChargeLog  $electricChargeLog
     * @return \Illuminate\Http\Response
     */
    public function show(ElectricChargeLog $electricChargeLog)
    {
        $this->authorize('own', $electricChargeLog);
        return new ElectricChargeLogResource($electricChargeLog);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ElectricChargeLog  $electricChargeLog
     * @return \Illuminate\Http\Response
     */
    public function edit(ElectricChargeLog $electricChargeLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ElectricChargeLog  $electricChargeLog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ElectricChargeLog $electricChargeLog)
    {
        $this->authorize('own', $electricChargeLog);
        $user = $request->user();

        // 支付状态 0-已完成 1-未提交 2-审核中
        if ($electricChargeLog->pay_status == 0) {
            $data['message'] = "电费支付已完成";
            return response()->json($data, 403);
        }

        $upload = upload_images($request->file('pay_image'), 'electriccharge', $user->id);

        $electricChargeLog->update([
            'pay_image' => $upload->path,
            'pay_status' => 2,
            'pay_time' => Carbon::now(),
        ]);

        $data['message'] = "电费支付凭证已提交";
        return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ElectricChargeLog  $electricChargeLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(ElectricChargeLog $electricChargeLog)
    {
        //
    }
}
