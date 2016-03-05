<?php namespace nyx\framework\diagnostics\debug\handlers;

	// External dependencies
	use nyx\diagnostics\debug as base;

	// Internal dependencies
	use nyx\framework;

	/**
	 * Framework Exception Handler
	 *
	 * @package     Nyx\Framework\Diagnostics\Debug
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/diagnostics.html
	 */

	class Exception extends base\handlers\Exception
	{
		/**
		 * @var framework\Kernel    The Kernel in which the Exception was thrown.
		 */

		private $kernel;

		/**
		 * Constructs a new Framework Exception Handler.
		 *
		 * @param   framework\Kernel    $kernel     The Kernel in which the Exception was thrown.
		 */

		public function __construct(framework\Kernel $kernel)
		{
			$this->kernel = $kernel;
		}

		/**
		 * {@inheritDoc}
		 */

		protected function inspect(\Exception $exception)
		{
			return new framework\diagnostics\debug\Inspector($exception, $this->kernel, $this);
		}

		/**
		 * {@inheritDoc}
		 */

		protected function handleDelegateResponse($response, base\Inspector $inspector)
		{
			// If we received a Response or a string, send them.
			if($response instanceof \Symfony\Component\HttpFoundation\Response)
			{
				$this->kernel->terminate($this->kernel->make('request'), $this->kernel->prepareResponse($response));

				return null;
			}

			//
			return $response;
		}
	}