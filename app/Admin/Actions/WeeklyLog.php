<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class WeeklyLog extends RowAction
{
    public $name = '详细列表';

    public function href()
    {
        return "/admin/weekly-logs?weekly_id=".$this->getKey();
    }

}
