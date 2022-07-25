<?php
namespace Gluu\Providers;

use Illuminate\Support\ServiceProvider;

class GluuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // register our controller
        $this->app->bind(\Gluu\App\GluuClient::class, function ($app){
            $gluuBaseUrl = $app['config']->get('gluu.base_url');
            $gluuClientId = $app['config']->get('gluu.client_id');
            $gluuClientSecret = $app['config']->get('gluu.client_secret');
            return new \Gluu\App\GluuClient($gluuBaseUrl, $gluuClientId, $gluuClientSecret);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}