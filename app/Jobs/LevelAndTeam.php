<?php

namespace App\Jobs;

use App\Models\Level;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LevelAndTeam implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务最大尝试次数。
     *
     * @var int
     */
    public $tries = 5;

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
    public $description = "更新会员级别和团队";

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
        try {
            // 设置等级
            $users = User::all();
            foreach ($users as $k => $v) {
                $id = $v->id;
                $ids = User::unlimitedCollectionById($id);
                // 如果存在下级
                if (count($ids) > 0) {
                    $users_son1 = [];
                    foreach ($ids as $key => $value) {
                        $users_son1[] = $value['id'];
                    }
                    // 下级数组
                    $users1 = array_values($users_son1);
                    // 求其所有的下级用户的总业绩之和
                    $total = Order::whereIn('user_id', $users1)
                        ->where('pay_status', '=', Order::PAID_COMPLETE)
                        ->sum('number'); // 购买 T 数

                    $check_level = Level::where('min', '<=', $total)
                        ->where('max', '>=', $total)
                        ->first();
                    if ($check_level) {
                        $users[$k]->update(['level_id' => $check_level->id]);
                    }
                }
            }

            // 设置团队（团队长id）
            $list = User::all();
            foreach ($list as $k => $v) {
                $level = $v->level_id;
                if ($level > 1) {
                    $id = $v->id;
                    $ids = User::unlimitedCollectionById($id);
                    // 如果存在下级
                    if (count($ids) > 0) {
                        $users_son1 = [];
                        foreach ($ids as $key => $value) {
                            $users_son1[] = $value['id'];
                        }
                        // 下级数组
                        $users1 = array_values($users_son1);
                        // 将所有下级 设置team
                        User::whereIn('id', $users1)
                            ->update(['team_id' => $id]);
                    }
                }
            }
        } catch
        (\Exception $e) {
            Log::error(__METHOD__ . '|' . __METHOD__ . '执行失败', ['day' => $day, 'error' => $e->getMessage()]);
        }
    }
}
