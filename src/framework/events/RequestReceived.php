<?php namespace nyx\framework\events;

	// External dependencies
	use Symfony\Component\HttpKernel\HttpKernelInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;

	// Internal dependencies
	use nyx\framework;

	/**
	 * Request Received Event
	 *
	 * Please see {@see \nyx\framework\definitions\Events} for information on when this Event may get triggered.
	 *
	 * @package     Nyx\Framework\Events
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/index.html
	 */

	class RequestReceived extends Kernel
	{
		/**
		 * @var Response    The Response set for the given Request.
		 */

		private $response;

		/**
		 * {@inheritDoc}
		 *
		 * Overridden to set the default name for the Event.
		 */

		public function __construct(HttpKernelInterface $kernel, Request $request, $name = framework\definitions\Events::REQUEST_RECEIVED, $emit = true)
		{
			parent::__construct($kernel, $request, $name, $emit);
		}

		/**
		 * Returns the Response set for the given Request.
		 *
		 * @return  Response
		 */

		public function getResponse()
		{
			return $this->response;
		}

		/**
		 * Sets a Response for the given Request and stops further Event propagation.
		 *
		 * @param   Response    $response   The Response to be set for the given Request.
		 */

		public function setResponse(Response $response)
		{
			$this->response = $response;
			$this->stop();
		}

		/**
		 * Checks whether a Response has been set for the given Request.
		 *
		 * @return  bool    True when a Response has been set, false otherwise.
		 */

		public function hasResponse()
		{
			return null !== $this->response;
		}
	}