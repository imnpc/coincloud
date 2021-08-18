<?php

namespace App\Jobs;

use App\Models\WalletType;
use Cache;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoUpdateCoinPrice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务最大尝试次数。
     *
     * @var int
     */
    public $tries = 1;

    /**
     * 任务运行的超时时间。
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * 任务描述
     * @var string
     */
    public $description = "更新实时币价";

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $day = Carbon::yesterday()->toDateString();// 获得日期
        $today = Carbon::now()->toDateString();// 获得日期
        try {
            // 实时币价 需要缓存下
            $wallet = WalletType::where('id', '>', 3)
                ->orderBy('sort', 'asc')
                ->get();
            foreach ($wallet as $k => $v) {
                $coins[$k]['name'] = $v['slug'];
                $coins[$k]['image'] = $v['icon_url'];
                $coins[$k]['price'] = huobiusdt(strtolower($v['slug']));
            }

            Cache::put('indexcoins', $coins, 3600);
        } catch (\Exception $e) {
            Log::error(__METHOD__ . '|' . __METHOD__ . '执行失败', ['day' => $day, 'error' => $e]);
        }
    }
}
