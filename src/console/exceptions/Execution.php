<?php namespace nyx\console\exceptions;

	// Internal dependencies
	use nyx\console;

	/**
	 * Execution Exception
	 *
	 * Exception thrown when a generic error during command execution occurs. Please refer to the specific
	 * exceptions (for instance CommandDisabled) for more information.
	 *
	 * @package     Nyx\Console\Diagnostics
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/exceptions.html
	 */

	class Execution extends \RuntimeException
	{
		/**
		 * @var console\Context     The Context which resulted in this Exception.
		 */

		private $context;

		/**
		 * {@inheritDoc}
		 *
		 * @param   console\Context     $context    The Context which resulted in this Exception.
		 */

		public function __construct(console\Context $context, $message = null, $code = 0, \Exception $previous = null)
		{
			$this->context = $context;

			// Proceed to create a casual exception.
			parent::__construct($message ?: "An unknown error occurred while executing the command.", $code, $previous);
		}

		/**
		 * Returns the Context that was to be executed.
		 *
		 * @return  console\Context
		 */

		public function getContext()
		{
			return $this->context;
		}
	}