<?php namespace nyx\framework\diagnostics\debug;

	// External dependencies
	use Illuminate\Config;
	use nyx\diagnostics;
	use nyx\framework;
	use nyx\utils;

	/**
	 * Debug Service Provider
	 *
	 * Important note (for testing mostly): This Service Provider assumes that the Request does not change during
	 * the lifecycle of your Kernel, ie. a Displayer appropriate for the given Request type will get registered
	 * only once and will not change for subsequent calls to the handle() method of the Exception Handler.
	 *
	 * Important note: The registration of the Delegates is deferred until an Exception actually gets handled, which
	 * means the Delegates are not configurable until then. You can override them in several ways:
	 * - Extend this Service Provider;
	 * - Create a 'diagnostics.debug.displayer' binding in your Kernel before an exception occurs, in which case
	 *   the bundled Displayers will not get registered at all. You could do that by creating an event listener for the
	 *   diagnostics\definitions\Events::DEBUG_EXCEPTION_BEFORE Event with a priority higher than 0, so that it gets
	 *   called before the listener in this Service Provider;
	 * - Callables in your configuration;
	 *
	 * @package     Nyx\Framework\Diagnostics\Debug
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/index.html
	 */

	class Provider extends \Illuminate\Support\ServiceProvider
	{
		/**
		 * @var framework\Kernel    The Application's Kernel.
		 */

		protected $app;

		/**
		 * @var Config\Repository   The Application's Configuration.
		 */

		protected $config;

		/**
		 * {@inheritDoc}
		 */

		public function __construct(framework\Kernel $kernel)
		{
			$this->config = $kernel->make('config');

			parent::__construct($kernel);
		}

		/**
		 * {@inheritDoc}
		 */

		public function register()
		{
			// We need both the Error and Exception handlers instantiated as soon as possible so they can get registered
			// with PHP as the respective handlers.
			$this->app->instance('diagnostics.debug.handlers.error', $error = new diagnostics\debug\handlers\Error($this->config->get('debug.threshold')));
			$error->setEmitter($this->app)->register($error);

			$this->app->instance('diagnostics.debug.handlers.exception', $exception = new handlers\Exception($this->app));
			$exception->setEmitter($this->app)->register($exception);

			// We are going to register a listener for when an Exception begins to get handled. This way we can defer
			// the instantiation of our Delegates and only register those which are appropriate for the given
			// context.
			$this->app->on(diagnostics\definitions\Events::DEBUG_EXCEPTION_BEFORE, [$this, 'onException']);
		}

		/**
		 * Registers the Delegates with the given Exception Handler. Listener for the
		 * diagnostics\definitions\Events::DEBUG_EXCEPTION_BEFORE Event.
		 *
		 * @param   diagnostics\debug\Event $event
		 */

		public function onException(diagnostics\debug\Event $event)
		{
			// If a Displayer is already bound, we are not going to override it. See the notes in the class description.
			if($this->app->bound('diagnostics.debug.displayer')) return;

			if(php_sapi_name() !== 'cli')
			{
				$this->registerDisplayer();

				// Add the Delegate.
				$event->getHandler()->add($this->app->make('diagnostics.debug.displayer'));
			}
		}

		/**
		 *
		 */

		protected function registerDisplayer()
		{
			/* @var \Illuminate\Http\Request $request */
			$request = $this->app->make('request');

			// Well, well, which Displayer should we use in this request context?
			if($request->wantsJson() or $request->ajax())
			{
				$this->registerJsonDisplayer();
			}
			// If debugging is not enabled (either set to false or not set) we are going to print a simple error by
			// default.
			elseif(!$this->config->get('debug.enabled'))
			{
				$this->registerPlainPageDisplayer();
			}
			else
			{
				$this->registerDebugPageDisplayer();
			}
		}

		/**
		 *
		 */

		protected function registerDebugPageDisplayer()
		{
			$this->app->bindShared('diagnostics.debug.displayer', function($kernel) {

				// If we resolve to a null, which happens when the 'debug.displayers.debug' config key is not set at
				// all, we are going to instantiate our default.
				if(null === $class = $this->resolveFromConfig('displayers.debug'))
				{
					$instance = new delegates\DebugPageDisplayer();
					$instance->setResourcesPath($this->getResourcePath());
					$instance->setEditor($this->getEditor());

					return $instance;
				}

				// We either got a string with a class name now or a callable. If it's a callable from the config we
				// are going to call it and return it as is.
				if(!is_string($class)) return $class($kernel);

				// Otherwise create the instance.
				return new $class;
			});
		}

		/**
		 *
		 */

		protected function registerPlainPageDisplayer()
		{
			$this->app->bindShared('diagnostics.debug.displayer', function($kernel) {

				// If we resolve to a null, which happens when the 'debug.displayers.plain' config key is not set at
				// all, we are going to instantiate our default.
				if(null === $class = $this->resolveFromConfig('displayers.plain'))
				{
					$instance = new delegates\PlainPageDisplayer();
					$instance->setResourcesPath($this->getResourcePath());

					return $instance;
				}

				// We either got a string with a class name now or a callable. If it's a callable from the config we
				// are going to call it and return it as is.
				if(!is_string($class)) return $class($kernel);

				// Otherwise create the instance.
				return new $class;
			});
		}

		/**
		 *
		 */

		protected function registerJsonDisplayer()
		{
			$this->app->bindShared('diagnostics.debug.displayer', function($kernel) {

				// If we resolve to a null, which happens when the 'debug.displayers.json' config key is not set at
				// all, we are going to instantiate our default.
				$class = $this->resolveFromConfig('displayers.json', 'nyx\framework\diagnostics\debug\delegates\JsonDisplayer');

				// We either got a string with a class name now or a callable. If it's a callable from the config we
				// are going to call it and return it as is.
				if(!is_string($class)) return $class($kernel);

				// Otherwise create the instance.
				return new $class;
			});
		}

		/**
		 * @return  callable|string
		 */

		protected function resolveFromConfig($key, $default = null)
		{
			if(null === $class = $this->config->get('debug.'.$key, $default)) return null;

			// If we got a callable configured, we are going to call it and use the result.
			if(is_callable($class)) return $class;

			// Ensure what's remaining at this point is a string - can't work with anything else.
			if(is_string($class)) return $class;

			// Well, too bad.
			throw new \InvalidArgumentException("Invalid configuration value for [debug.{$key}]. Expected a callable or string, got [".gettype($class)."] instead.");
		}

		/**
		 * Returns the resource path for the delegates.
		 *
		 * @return  string
		 */

		protected function getResourcePath()
		{
			return $this->config->get('debug.resources', __DIR__.'/resources');
		}

		/**
		 * Returns the editor/IDE to which the delegates should adjust their output.
		 *
		 * @return  string|callable
		 */

		protected function getEditor()
		{
			return $this->config->get('debug.editor', 'sublime');
		}
	}