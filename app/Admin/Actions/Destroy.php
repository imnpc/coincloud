<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Destroy extends RowAction
{
    public $name = '彻底删除';

    public function handle(Model $model)
    {

        $model->forceDelete();

        return $this->response()->success('已彻底删除')->refresh();
    }

    public function dialog()
    {
        $this->confirm('确定彻底删除吗？');
    }
}