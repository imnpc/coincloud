<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Lin\Huobi\HuobiSpot;
use Lin\Okex\OkexV5;

class UtilsController extends Controller
{
    /**
     * 火币行情K线图
     * @param  Request  $request
     * @return mixed
     */
    public function huobiKline(Request $request)
    {
        $huobi = new HuobiSpot();

        $data = $huobi->market()->getHistoryKline([
            'symbol' => $request->symbol, // 交易对 	btcusdt, ethbtc等
            'period' => $request->period, // 返回数据时间粒度，也就是每根蜡烛的时间区间 	1min, 5min, 15min, 30min, 60min, 4hour, 1day, 1mon, 1week, 1year
            'size' => $request->size, // 返回 K 线数据条数 	[1-2000]
        ]);

        return $data;
    }

    /**
     * 火币 市场深度数据
     * @param  Request  $request
     * @return mixed
     */
    public function huobiDepth(Request $request)
    {
        $huobi = new HuobiSpot();

        $data = $huobi->market()->getDepth([
            'symbol' => $request->symbol, // 交易对 	btcusdt, ethbtc等
            'depth' => $request->depth, // 返回深度的数量 	5，10，20
            'type' => $request->type, // 深度的价格聚合度，具体说明见下方 	step0，step1，step2，step3，step4，step5
        ]);

        return $data;
    }

    /**
     * 火币 最近市场成交记录
     * @param  Request  $request
     * @return mixed
     */
    public function huobiTrade(Request $request)
    {
        $huobi = new HuobiSpot();

        $data = $huobi->market()->getTrade([
            'symbol' => $request->symbol, // 交易对 	btcusdt, ethbtc等
        ]);

        return $data;
    }

    /**
     * 火币 最近24小时行情数据
     * @param  Request  $request
     * @return mixed
     */
    public function huobiDetail(Request $request)
    {
        $huobi = new HuobiSpot();

        $data = $huobi->market()->getDetail([
            'symbol' => $request->symbol, // 交易对 	btcusdt, ethbtc等
        ]);

        return $data;
    }

    /**
     * 美元转人民币
     * @param  Request  $request
     * @return string|\Torann\Currency\Currency
     */
    public function usdToCny(Request $request)
    {
        $data['usd'] = $request->usd;
        $data['cny'] = currency($request->usd, 'USD', 'CNY',false);

        return $data;
    }

    /**
     * 获得近期交易记录
     * @param  Request  $request
     * @return mixed
     */
    public function getHistoryTrade(Request $request)
    {
        $huobi = new HuobiSpot();

        $data = $huobi->market()->getHistoryTrade([
            'symbol' => $request->symbol, // 交易对 	btcusdt, ethbtc等
            'size' => $request->size, // 返回 K 线数据条数 	[1-2000]
        ]);

        return $data;
    }

    /**
     * OKEX 获取交易产品K线数据
     * @param  Request  $request
     * @return mixed
     */
    public function getCandles(Request $request)
    {
        $market = new OkexV5();

        $data = $market->market()->getHistoryCandles([
            'instId' => $request->instId, // 产品ID，如BTC-USDT
            'bar' => $request->bar, // 时间粒度，默认值1m 如 [1m/3m/5m/15m/30m/1H/2H/4H] 香港时间开盘价k线：[6H/12H/1D/2D/3D/1W/1M/3M/6M/1Y]
            'limit' => $request->limit, // 分页返回的结果集数量，最大为300，不填默认返回100条
            'after' => $request->after, // 请求此时间戳之前（更旧的数据）的分页内容，传的值为对应接口的ts
            'before' => $request->before, // 请求此时间戳之后（更新的数据）的分页内容，传的值为对应接口的ts
        ]);

        return $data;
    }
}
