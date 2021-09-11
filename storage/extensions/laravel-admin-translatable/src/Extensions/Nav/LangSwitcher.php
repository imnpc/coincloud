<?php

namespace solutionforest\LaravelAdmin\Translatable\Extensions\Nav;

use Encore\Admin\Admin;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

class LangSwitcher implements Renderable
{
    public function render()
    {
        $script = <<<SCRIPT
$('.lang-switcher .lang-switcher-url').off('click').on('click', function (e) {
    e.preventDefault();
    location = $(this).attr("href");
});
SCRIPT;

        Admin::script($script);

        $locale = App::getLocale();
        $shortname = Lang::get('admin.lang_shortname');
        $html = "";

        collect(config('app.locales'))
            ->filter(function($value,$key) use ($locale) {
                return $value != $locale;
            })
            ->each(function ($item, $key) use (&$html) {
                $url = request()->fullUrlWithQuery(['lang' => $item]);
                $shortname = Lang::get('admin.lang_shortname', [], $item);
                $html .= '<li><a href="' . $url . '" class="lang-switcher-url" style="line-height:50px;padding:0;text-align:center;">' . $shortname . '</a></li>';
            });

        return <<<HTML
<li class="dropdown lang-switcher" style="min-width:50px;text-align:center;">
    <a href="#" class="dropdown-toggle" style="color:#fff;" data-toggle="dropdown" title="$locale"><i class="fa fa-language"></i>&nbsp;&nbsp;$shortname</a>
    <ul class="dropdown-menu" style="min-width: 50px !important;padding:0;">$html</ul>
</li>
HTML;
    }
}
