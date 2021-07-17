<?php

namespace App\Admin\Actions\User;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class WalletLog extends RowAction
{
    public $name = '钱包日志';

    public function href()
    {
        return "/admin/user-wallet-logs?user_id=".$this->getKey();
    }


}