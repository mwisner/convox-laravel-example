<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      if (in_array(config('app.env'), ['staging', 'production'])) {
          URL::forceScheme('https');
      }

      \URL::forceRootUrl(\Config::get('app.url'));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
