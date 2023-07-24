<?php

namespace App\Admin\Actions\User;

use App\Models\User;
use App\Models\UserWalletLog;
use App\Models\WalletType;
use App\Services\LogService;
use App\Services\UserWalletService;
use Carbon\Carbon;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Money extends RowAction
{
    public $name = '账户充值';

    public function handle(Model $model, Request $request)
    {
        $logService = app()->make(LogService::class); // 钱包服务初始化
        $day = Carbon::now()->toDateString();
        // 获取到表单中的`type`值
        $type = $request->get('type');
        // 获取表单中的`reason`值
        $money = $request->get('money');
        $remark = $request->get('remark');
        $day = Carbon::now()->toDateString();
        // 给用户账户增加对应金额
        if (!$remark) {
            $remark = "管理员后台调整 " . $money;
        }
        $logService->userLog($model->id, $type, $money, 0, $day, UserWalletLog::FROM_ADMIN, $remark);

        return $this->response()->success('账户已充值完毕！')->refresh();
    }

    public function form()
    {
        $list = WalletType::all();
        $options = [];
        foreach ($list as $key => $value) {
            $options[$value->id] = $value->slug . '[' . $value->name . ']';
        }
        $this->radio('type', '请选择要操作的金额类型')->options($options)->default('1');
        // 文本输入框
        $this->text('money', '请输入调整的金额,支持正数和负数')->required();
        $this->text('remark', '请输入调整理由');
    }

}
