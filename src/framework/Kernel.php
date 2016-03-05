<?php namespace nyx\framework;

	// External dependencies
	use Symfony\Component\HttpKernel\HttpKernelInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Illuminate\Config;
	use Illuminate\Filesystem;
	use Illuminate\Foundation;
	use Illuminate\Http;
	use Illuminate\Support;

	/**
	 * HTTP Kernel
	 *
	 * Currently extending the base Application from Laravel to make it compatible with Laravel's infrastructure.
	 *
	 * @package     Nyx\Framework
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/index.html
	 */

	class Kernel extends Foundation\Application implements \nyx\events\interfaces\Emitter
	{
		/**
		 * The traits of an Application Kernel.
		 */

		use \nyx\events\traits\Emitter;

		/**
		 * {@inheritDoc}
		 */

		public function __construct(array $paths, $env = 'development')
		{
			// Bind the environment and the installation paths.
			$this['env'] = $env;

			// We want the Stack Builder and the Filesystem easily available.
			$this->bindShared('stack.builder', function() {return new \Stack\Builder;});
			$this->bindShared('files', function() {return new Filesystem\Filesystem;});

			// Register all given paths with a prefix.
			foreach($paths as $key => $value) $this->instance("path.{$key}", $value);

			// Make the Kernel itself available in the DI container.
			$this->instance('app', $this);
			$this->instance('config', $config = new Config\Repository(new Config\FileLoader($this->make('files'), $paths['config']), $env));
			$this->instance('Illuminate\Container\Container', $this);

			// Required by the ServiceProvider.
			$this->register(new events\Provider($this));

			// Set the Kernel in the Facade (setting this here since the configuration may depend on it).
			Support\Facades\Facade::setFacadeApplication($this);

			$this->configure($config);
		}

		/**
		 * Boots the Kernel, builds its Stack, handles the Request and then sends the Response.
		 *
		 * @param   Request $request
		 * @return  $this
		 */

		public function run(Request $request = null)
		{
			$this->instance('request', $request ?: $request = Http\Request::createFromGlobals());
			$this->boot();

			// Build our Stacked Kernel.
			$stack = $this->make('stack');

			// Even though the default StackedHttpKernel aliases its handle() method to the main app, we need to
			// assume both the Builder and the resulting Kernel can be customized.
			$response = $stack->handle($request);

			// Terminate the Stack.
			$stack->terminate($request, $response);
		}

		/**
		 * {@inheritDoc}
		 */

		public function boot()
		{
			// Obviously, break away if we've already booted.
			if($this->booted) return $this;

			// Build our Stacked Kernel. Ensure our internal middlewares are at the very top.
			/* @var \Stack\StackedHttpKernel $kernel */
			$this->bindShared('stack', function() {

				return $this->make('stack.builder')
					->unshift('Illuminate\Session\Middleware', $this->make('session'), $this->bound('session.reject') ? $this->make('session.reject') : null)
					->unshift('Illuminate\Cookie\Queue', $this->make('cookie'))
					->unshift('Illuminate\Cookie\Guard', $this->make('encrypter'))
					->resolve($this);
			});

			// Spin up the Service Providers.
			foreach($this->serviceProviders as $provider) $provider->boot();

			$this->booted = true;

			return $this;
		}

		/**
		 * {@inheritDoc}
		 */

		public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
		{
			// For now, we are going to stick with Laravel's Requests for their additional utility, so we need to
			// convert the instance if it's the base Symfony Request.
			$this->instance('request', $request = $request instanceof Http\Request ? $request : Http\Request::createFromBase($request));

			// Make sure the Facade updates correctly as well.
			Support\Facades\Facade::clearResolvedInstance('request');

			// The Request is ready to be processed.
			$event = new events\RequestReceived($this, $request);

			// If we got a Response at this point already, we can skip dispatching it.
			$response = $event->hasResponse() ? $event->getResponse() : $this->make('router')->dispatch($request);

			// Prepare the Response and emit the appropriate Event. Return the Response from within the Event.
			return $this->prepareResponse($response);
		}

		/**
		 * Prepares the given value as a Response object.
		 *
		 * @param   mixed       $value
		 * @return  Response
		 */

		public function prepareResponse($value)
		{
			$request = $this->make('request');

			if(!$value instanceof Response) $value = new Http\Response($value);

			$value->prepare($request);

			return (new events\ResponsePrepared($this, $request, $value))->getResponse();
		}

		/**
		 * {@inheritDoc}
		 */

		public function terminate(Request $request, Response $response)
		{
			$response->send();

			new events\ResponseSent($this, $request, $response);
		}

		/**
		 * Configures the Kernel based on the Config passed.
		 *
		 * @param   Config\Repository   $config     The configuration to use.
		 */

		protected function configure(Config\Repository $config)
		{
			// For easier referencing.
			$app = $config->get('app');

			// Register the Facades.
			Foundation\AliasLoader::getInstance($app['aliases'])->register();

			// Register our Service Providers.
			(new Foundation\ProviderRepository($this->make('files'), $app['manifest']))->load($this, $app['providers']);
		}
	}