<?php namespace La2ha\Pixfile;

use Illuminate\Support\ServiceProvider;

class PixfileServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    public function boot()
    {
        $this->package('la2ha/pixfile');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['pixfile'] = $this->app->share(function ($app) {
            return new PixFile(new Helper);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('pixfile');
    }

}