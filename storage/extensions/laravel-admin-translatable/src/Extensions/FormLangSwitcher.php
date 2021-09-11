<?php

namespace solutionforest\LaravelAdmin\Translatable\Extensions;

use Encore\Admin\Facades\Admin;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

class FormLangSwitcher implements Renderable{


    public function render()
    {
        $currentLang = config('app.locale');

        $script = <<<SCRIPT
        $('[curr_lang="$currentLang"]').each(function() { $(this).closest('.form-group').show(); } );
        $('.form-lang-switcher .dropdown-menu .dropdown-item').off('click').on('click', function (e) {
                e.preventDefault();
                // disable all lang first

                $('[curr_lang]').each(function() { $(this).closest('.form-group').hide(); } );
                var currLang = $(this).data('lang');
                $('[curr_lang="'+currLang+'"]').each(function() { $(this).closest('.form-group').show(); } );
        });    
        SCRIPT;

        Admin::script($script);

        $locale = App::getLocale();
        $html = "";

        collect(config('app.locales'))
            ->each(function ($item, $key) use (&$html) {
                $shortname = Lang::get('admin.lang_shortname', [], $item);
                $html .= '<li><a class="dropdown-item" data-lang="'.$item.'" href="#">'.$shortname.'</a></li>';
            });
        $translation = Lang::get('admin.translation');
        return <<<HTML
<!-- Single button -->
<div class="btn-group pull-right form-lang-switcher" style="margin-right: 5px">
  <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">$translation<span class="caret"></span>
  </button>
  <ul class="dropdown-menu">$html</ul>
</div>
HTML;
    }
}