<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Restore extends RowAction
{
    public $name = '恢复';

    public function handle(Model $model)
    {
        $model->restore();
//        $model->forceDelete();

        return $this->response()->success('已恢复')->refresh();
    }

    public function dialog()
    {
        $this->confirm('确定恢复吗？');
    }
}