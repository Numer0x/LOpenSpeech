<?php

namespace Zacz\LOpenSpeech;

use Illuminate\Support\ServiceProvider;

class OpenSpeechProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/OpenSpeech.php' => config_path('OpenSpeech.php'),
        ]);

    }

    public function register()
    {
        $this->app->bind('OpenSpeech', function ($app) {
            return new OpenSpeech($app['config']);
        });
    }


}
