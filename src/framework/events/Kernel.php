<?php namespace nyx\framework\events;

	// External dependencies
	use Symfony\Component\HttpKernel\HttpKernelInterface;
	use Symfony\Component\HttpFoundation\Request;
	use nyx\events;

	// Internal dependencies
	use nyx\framework;

	/**
	 * Base Kernel Event
	 *
	 * @package     Nyx\Framework\Events
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/index.html
	 */

	class Kernel extends events\Event
	{
		/**
		 * @var framework\Kernel    The Kernel of the Application.
		 */

		private $kernel;

		/**
		 * @var Request             The Request being handled.
		 */

		private $request;

		/**
		 * {@inheritDoc}
		 *
		 * Important note: By default, for convenience, Kernel Events are assumed to be emitted by the Kernel and Kernel
		 * only. We are utilizing a feature of the base Event class that allows for the emission as soon as the Event is
		 * constructed. You can override this by setting the $emit parameter to false or you can specify another Emitter
		 * instead.
		 *
		 * @param   HttpKernelInterface             $kernel     The Kernel of the Application.
		 * @param   Request                         $request    The Request being handled.
		 * @param   bool|events\interfaces\Emitter  $emit       Either a boolean to enable/disable the auto-emission
		 *                                                      via the passed in Kernel or another Emitter instance
		 *                                                      to use instead.
		 */

		public function __construct(HttpKernelInterface $kernel, Request $request, $name = null, $emit = true)
		{
			$this->kernel  = $kernel;
			$this->request = $request;
			$emitter       = null;

			// If we are to emit the Event right away, which Emitter are we to use?
			if($emit) $emitter = $emit instanceof events\interfaces\Emitter ? $emit : $kernel;

			parent::__construct($name, $emitter instanceof events\interfaces\Emitter ? $emitter : null);
		}

		/**
		 * Returns the Kernel of the Application.
		 *
		 * @return  framework\Kernel
		 */

		public function getKernel()
		{
			return $this->kernel;
		}

		/**
		 * Returns the Request being handled.
		 *
		 * @return  Request
		 */

		public function getRequest()
		{
			return $this->request;
		}
	}