<?php

namespace Rdj\Client;

use Rdj\Client\Client;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\ServiceProvider;

class ClientServiceProvider extends ServiceProvider
{
    /**
    * {@inheritDoc}
    */
   protected $defer = true;

   /**
    * Bootstrap the application services.
    *
    * @return void
    */
   public function boot()
   {
       $this->publishes([
            __DIR__.'/config/client_restapi.php' => config_path('client_restapi.php'),
        ]);
   }

   /**
    * Register the application services.
    *
    * @return void
    */
   public function register()
   {
       $this->app->singleton('Client', function ($app) {
           return new Client(new HttpClient);
       });
   }

   /**
    * Get the services provided by the provider.
    *
    * @return array
    */
   public function provides()
   {
       return ['Client'];
   }
}
