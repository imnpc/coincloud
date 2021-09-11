<?php

namespace solutionforest\LaravelAdmin\Translatable;

use Encore\Admin\Extension;

class Translatable extends Extension
{
    public $name = 'laravel-admin-translatable';

    public $views = __DIR__.'/resources/views';

    public $assets = __DIR__.'/resources/assets';

    public $menu = [
        'title' => 'Translatable',
        'path'  => 'laravel-admin-translatable',
        'icon'  => 'fa-gears',
    ];
}
