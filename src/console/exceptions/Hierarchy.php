<?php namespace nyx\console\exceptions;

	// Internal dependencies
	use nyx\console;

	/**
	 * Hierarchy Exception
	 *
	 * Exception thrown when an inconsistency within the command hierarchy occurred. Please refer to the specific
	 * exceptions (for instance ChainViolation) for more information.
	 *
	 * The $cause of this exception is the instance that caused said inconsistency. The $superior is the instance
	 * which raised the warning about it and/or the instance to which the $cause should adhere. The cause will
	 * always be at the very least a console\Command, while the superior will be at least a console\Suite. Refer
	 * to the docs about the command hierarchy in Nyx console applications for more information.
	 *
	 * @package     Nyx\Console\Diagnostics
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/exceptions.html
	 */

	class Hierarchy extends \LogicException
	{
		/**
		 * @var console\Command  The Command which caused the inconsistency.
		 */

		private $cause;

		/**
		 * @var console\Suite   The superior to which the cause of the exception should adhere.
		 */

		private $superior;

		/**
		 * {@inheritDoc}
		 *
		 * @param   console\Command     $command    The cause of the inconsistency.
		 * @param   console\Suite       $superior   The superior to which the cause of the exception should adhere.
		 */

		public function __construct(console\Command $command, console\Suite $superior, $message = null, $code = 0, \Exception $previous = null)
		{
			$this->cause    = $command;
			$this->superior = $superior;

			// Proceed to create a casual exception.
			parent::__construct($message, $code, $previous);
		}

		/**
		 * Returns the Command instance which is the cause of the inconsistency.
		 *
		 * @return  console\Command
		 */

		public function getCause()
		{
			return $this->cause;
		}

		/**
		 * Returns the superior to which the cause of the exception should have adhered.
		 *
		 * @return  console\Suite
		 */

		public function getSuperior()
		{
			return $this->superior;
		}
	}