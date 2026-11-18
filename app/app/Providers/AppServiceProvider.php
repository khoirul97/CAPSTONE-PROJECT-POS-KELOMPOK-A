<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

use Illuminate\Support\Facades\Event;
use App\Observers\ProdukObserver;

use App\Laravue\Models\Produk;


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
        config(['app.locale' => 'id']);
        Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Event::listen(
            'eloquent.created: *',
            function ($event, $models) {
                app(\App\Listeners\GlobalActivityLogger::class)
                    ->created($event, $models);
            }
        );

        Event::listen(
            'eloquent.updated: *',
            function ($event, $models) {
                app(\App\Listeners\GlobalActivityLogger::class)
                    ->updated($event, $models);
            }
        );

        Event::listen(
            'eloquent.deleted: *',
            function ($event, $models) {
                app(\App\Listeners\GlobalActivityLogger::class)
                    ->deleted($event, $models);
            }
        );

        Event::listen(
            'eloquent.restored: *',
            function ($event, $models) {
                app(\App\Listeners\GlobalActivityLogger::class)
                    ->restored($event, $models);
            }
        );

        Produk::observe(ProdukObserver::class);

    }
}