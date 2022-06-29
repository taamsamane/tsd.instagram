<?php

namespace TSD\Instagram\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class InstagramServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {

            $this->publishes([
              __DIR__.'/../../config/config.php' => config_path('instagram.php'),
            ], 'config');
        
          }
    }
    public function register()
    {
        App::bind('InstaBasic', function () {
            return new \TSD\Instagram\Services\BasicDisplay;
        });

        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'instagram');

    }
}
