<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;

class ClearCache extends Action
{
    protected $selector = '.clear-cache';

    public function handle(Request $request)
    {
        return $this->response()->success('清除缓存成功!')->refresh();
    }

    public function dialog()
    {
        $this->confirm('您确认要清除缓存吗?');
    }

    public function html()
    {
        return <<<HTML
<li>
    <a class="clear-cache" href="#">
      <i class="fa fa-trash"></i>
      <span>清除缓存</span>
    </a>
</li>
HTML;
    }
}
