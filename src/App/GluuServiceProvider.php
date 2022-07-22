<?php
namespace Gluu\App\Provider;

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
        $this->app->make('Gluu\App\Controller\GluuController');
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