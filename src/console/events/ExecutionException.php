<?php namespace nyx\console\events;

	// Internal dependencies
	use nyx\console\definitions;
	use nyx\console;

	/**
	 * Console Execution Exception Event
	 *
	 * Provides read-write access to the uncaught Exception and therefore also allows a listener to overwrite said
	 * Exception with another one, which in turn will get re-thrown by the Application unless even another listener
	 * overwrites it again. Extends {@see ExecutionAfter} for access to the exit code (the exit code should usually
	 * be equivalent to the exception's code and greater than 0).
	 *
	 * Please see {@see \nyx\console\definitions\Events} for information on when this Event may get triggered.
	 *
	 * @package     Nyx\Console\Events
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/events.html
	 */

	class ExecutionException extends ExecutionAfter
	{
		/**
		 * @var \Exception  The Exception which was thrown.
		 */

		private $exception;

		/**
		 * {@inheritDoc}
		 *
		 * @param   int     $exitCode   The exit code of the Command.
		 */

		public function __construct(console\Context $context, \Exception $exception, $exitCode, $name = definitions\Events::EXECUTION_EXCEPTION)
		{
			$this->exception = $exception;

			parent::__construct($context, $exitCode, $name);
		}

		/**
		 * Returns the Exception which was thrown.
		 *
		 * @return  \Exception
		 */

		public function getException()
		{
			return $this->exception;
		}

		/**
		 * Replaces the Exception which was thrown. The Exception set in this Event will be thrown after event emission
		 * is done.
		 *
		 * @param   \Exception  $exception
		 */

		public function setException(\Exception $exception)
		{
			$this->exception = $exception;
		}
	}