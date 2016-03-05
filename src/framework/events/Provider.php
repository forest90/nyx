<?php namespace nyx\framework\events;

	/**
	 * Events Service Provider
	 *
	 * @package     Nyx\Framework
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/index.html
	 * @todo        Completely move away from Laravel's Event system in favour of the Emitter we've already got available
	 *              in the Kernel.
	 */

	class Provider extends \Illuminate\Support\ServiceProvider
	{
		/**
		 * {@inheritDoc}
		 */

		public function register()
		{
			$this->app->bind('events', $this->app->share(function($app)
			{
				return new \Illuminate\Events\Dispatcher($app);
			}));
		}
	}