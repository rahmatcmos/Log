<?php namespace ThunderID\Log;

use View, Validator, App, Route, Auth, Request, Redirect;
use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider {

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
		\ThunderID\Log\Models\Log::observe(new \ThunderID\Log\Models\Observers\LogObserver);
		\ThunderID\Log\Models\Log::observe(new \ThunderID\Log\Models\Observers\ProcessingLogObserver);
		\ThunderID\Log\Models\ProcessLog::observe(new \ThunderID\Log\Models\Observers\ProcessLogObserver);
		\ThunderID\Log\Models\ErrorLog::observe(new \ThunderID\Log\Models\Observers\ErrorLogObserver);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		\ThunderID\Log\Models\Log::observe(new \ThunderID\Log\Models\Observers\LogObserver);
		\ThunderID\Log\Models\Log::observe(new \ThunderID\Log\Models\Observers\ProcessingLogObserver);
		\ThunderID\Log\Models\ProcessLog::observe(new \ThunderID\Log\Models\Observers\ProcessLogObserver);
		\ThunderID\Log\Models\ErrorLog::observe(new \ThunderID\Log\Models\Observers\ErrorLogObserver);
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
