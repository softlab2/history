<?php

namespace Softlab\Metatags;

use SleepingOwl\Admin\Contracts\Widgets\WidgetsRegistryInterface;
use SleepingOwl\Admin\Providers\AdminSectionsServiceProvider as ServiceProvider;
use SleepingOwl\Admin\Navigation\Page;
use AdminSection;
use PackageManager;
use Illuminate\Routing\Router;
use Request;
use Config;

class AdminSectionsServiceProvider extends ServiceProvider
{

    protected $policies = [
        \Softlab\Metatags\Admin\Http\Sections\Metatags::class => \Softlab\Metatags\Admin\Policies\MetatagsSectionModelPolicy::class,
    ];

    /**
     * @var array
     */
    protected $sections = [
        \Softlab\Metatags\Models\Metatag::class => \Softlab\Metatags\Admin\Http\Sections\Metatags::class,
    ];

    public function register(){
        $adminPrefix = Config::get('sleeping_owl.url_prefix', 'cabinet');
        if( !Request::is($adminPrefix.'/*') && !Request::is($adminPrefix))
            return;
        
        parent::register();

    }
    /**
     * Register sections.
     *
     * @return void
     */
    public function boot(\SleepingOwl\Admin\Admin $admin)
    {
        $adminPrefix = Config::get('sleeping_owl.url_prefix', 'cabinet');
        if( !Request::is($adminPrefix.'/*') && !Request::is($adminPrefix))
            return;
    	//

        parent::boot($admin);

        //$this->registerNRoutes();
        $this->app->call( [ $this, 'registerNRoutes' ] );
        $this->app->call( [ $this, 'registerViews' ] );
        $this->registerNavigation();
        $this->registerMediaPackages();
        $this->registerPolicies();
    }

    private function registerNavigation()
    {
        if( file_exists(__DIR__.'/Admin/navigation.php') )
            \AdminNavigation::setFromArray(include_once(__DIR__.'/Admin/navigation.php'));
    }

    public function registerNRoutes( Router $router ) {
        $router->group( [
            'prefix'     => config( 'sleeping_owl.url_prefix' ),
            'middleware' => config( 'sleeping_owl.middleware' )
        ], function ( $router ) {
            require __DIR__.'/Admin/Http/routes.php';
        } );
    }

    private function registerMediaPackages()
    {
    }

    public function registerViews( WidgetsRegistryInterface $widgetsRegistry ) {
    }

}
