<?php namespace nyx\console\exceptions\input;

	// Internal dependencies
	use nyx\console\exceptions;
	use nyx\console\input;

	/**
	 * Stream Aborted Exception
	 *
	 * Thrown when an input stream ends abruptly while an operation was still working with it (for instance, when the user
	 * pressed ^D).
	 *
	 * @package     Nyx\Console\Diagnostics
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/exceptions.html
	 */

	class StreamAborted extends exceptions\Input
	{
		/**
		 * @var input\Stream    The Input Stream instance which got aborted.
		 */

		private $stream;

		/**
		 * {@inheritDoc}
		 *
		 * @param   input\Stream    $stream     The Input Stream instance which got aborted.
		 */

		public function __construct(input\Stream $stream, $message = null, $code = 0, \Exception $previous = null)
		{
			$this->stream = $stream;

			// Proceed to create a casual exception.
			parent::__construct($message ?: "Input aborted.", $code, $previous);
		}

		/**
		 * Returns the Input Stream instance which got aborted.
		 *
		 * @return  input\Stream
		 */

		public function getStream()
		{
			return $this->stream;
		}
	}