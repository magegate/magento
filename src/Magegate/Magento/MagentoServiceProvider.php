<?php
namespace Magegate\Magento;

use Illuminate\Support\ServiceProvider;

class MagentoServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('magegate/magento');
        include __DIR__ . '/../../routes.php';
    }

    /**
	 * Register the service provider.
	 *
	 * @return void
	 */
    public function register()
    {
        $this->app['Mage'] = $this->app->share(function()
        {
            /**
             * Return the Magegate Magento Mage wrapper instance
             */
            return new \Magegate\Magento\App\Mage();
        });
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('Mage');
	}

}