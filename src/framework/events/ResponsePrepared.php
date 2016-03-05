<?php namespace nyx\framework\events;

	// External dependencies
	use Symfony\Component\HttpKernel\HttpKernelInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;

	// Internal dependencies
	use nyx\framework;

	/**
	 * Response Prepared Event
	 *
	 * Please see {@see \nyx\framework\definitions\Events} for information on when this Event may get triggered.
	 *
	 * @package     Nyx\Framework\Events
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/index.html
	 */

	class ResponsePrepared extends Kernel
	{
		/**
		 * @var Response    The Response which has been prepared.
		 */

		private $response;

		/**
		 * {@inheritDoc}
		 *
		 * @param   Response    $response   The Response which has been prepared.
		 */

		public function __construct(HttpKernelInterface $kernel, Request $request, Response $response, $name = framework\definitions\Events::RESPONSE_PREPARED, $emit = true)
		{
			$this->response = $response;

			parent::__construct($kernel, $request, $name, $emit);
		}

		/**
		 * Returns the Response which has been prepared.
		 *
		 * @return  Response
		 */

		public function getResponse()
		{
			return $this->response;
		}

		/**
		 * Sets a new Response.
		 *
		 * @param   Response    $response
		 * @return  $this
		 */

		public function setResponse(Response $response)
		{
			$this->response = $response;
		}
	}