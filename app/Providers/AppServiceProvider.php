<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Ecommerce\Entities\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $whatsapp_setting = Setting::where('key', 'whats_app')->first()->value;
        view()->composer('ecommerce::frontend.layouts.master', function ($view) use($whatsapp_setting) {
            $view->with('whatsapp', $whatsapp_setting);
        });
    }
}
