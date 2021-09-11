<?php

namespace solutionforest\LaravelAdmin\Translatable;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Illuminate\Support\ServiceProvider;
use \solutionforest\LaravelAdmin\Translatable\Extensions\Form\Translatable as TField;
use solutionforest\LaravelAdmin\Translatable\Extensions\Nav\LangSwitcher;
use solutionforest\LaravelAdmin\Translatable\Http\Middleware\Locale;

class TranslatableServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(Translatable $extension)
    {
        if (! Translatable::boot()) {
            return ;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'laravel-admin-translatable');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/solutionforest/laravel-admin-translatable')],
                'laravel-admin-translatable'
            );
        }

        $this->app->booted(function () {
            $isFieldEnable = config("admin.extensions.laravel-admin-translatable.options.isFieldEnable" , true);
            if($isFieldEnable) {
                Form::extend('translatable', TField::class);
            }
            $isNavEnable = config("admin.extensions.laravel-admin-translatable.options.isNavEnable" , true);
            if($isNavEnable) {
                Admin::navbar(function (\Encore\Admin\Widgets\Navbar $navbar) {
                    $navbar->right(new LangSwitcher());
                });
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerMiddleware();
    }

    /**
     * Register route middleware.
     */
    protected function registerMiddleware()
    {
        app('router')->aliasMiddleware('locale', Locale::class);
    }
}