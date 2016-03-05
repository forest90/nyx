<?php namespace nyx\console\events;

	// Internal dependencies
	use nyx\console\definitions;
	use nyx\console;

	/**
	 * Console After Execution Event
	 *
	 * Provides read-write access to the exit code of the Command. This code will be returned by the Application after
	 * event emission is done and everything is done executing. Per convention, exit codes greater than 0 are assumed
	 * to mean that some error occurred the execution of the Command.
	 *
	 * Please see {@see \nyx\console\definitions\Events} for information on when this Event may get triggered.
	 *
	 * @package     Nyx\Console\Events
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/events.html
	 */

	class ExecutionAfter extends Event
	{
		/**
		 * @var int     The exit code of the Command.
		 */

		private $exitCode;

		/**
		 * {@inheritDoc}
		 *
		 * @param   int     $exitCode   The exit code of the Command.
		 */

		public function __construct(console\Context $context, $exitCode, $name = definitions\Events::EXECUTION_AFTER)
		{
			$this->setExitCode($exitCode);

			parent::__construct($context, $name);
		}

		/**
		 * Sets the exit code.
		 *
		 * @param   int     $exitCode
		 */

		public function setExitCode($exitCode)
		{
			$this->exitCode = (int) $exitCode;
		}

		/**
		 * Returns the exit code.
		 *
		 * @return  int
		 */

		public function getExitCode()
		{
			return $this->exitCode;
		}
	}