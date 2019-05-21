<?php

namespace Softlab\History;

use Illuminate\Support\ServiceProvider;
use Modules;
use Request;
use Config;

class HistoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        //$this->boot(new \Softlab\Metatags\AdminSectionsServiceProvider(app()));
        Modules::register($this);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        require __DIR__ . '/helpers.php';

        $this->app->singleton('history', function () {
            return \Softlab\History\History::getInstance();
        });                                                                                                                       

        $adminPrefix = Config::get('sleeping_owl.url_prefix', 'cabinet');
        if( !Request::is($adminPrefix.'/*') && !Request::is($adminPrefix))
            app()->register(new \Softlab\History\AdminSectionsServiceProvider(app()));
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'history'
        ];
    }

    public function loadModule(){
        return [
            'alias'=>'history',
            'policies'=>[],
            'title'=>'История изменений'
        ];
    }
}
