<?php namespace nyx\system\exceptions;

	// Internal dependencies
	use nyx\system;

	/**
	 * System Call Exception
	 *
	 * Generic exception used by the Call subcomponent to denote an exception that occurred after a Process has already
	 * been started, ie. its Result has already been instantiated.
	 *
	 * @package     Nyx\System\Calls
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/system/calls.html
	 */

	class Call extends \RuntimeException
	{
		/**
		 * @var system\call\Result  The Result of the Call which caused the exception.
		 */

		private $result;

		/**
		 * {@inheritDoc}
		 *
		 * @param   system\call\Result  $result     The Result of the Call which caused the exception.
		 */

		public function __construct(system\call\Result $result, $message = null, $code = null, \Exception $previous = null)
		{
			// Make sure the Result is available within this exception.
			$this->result = $result;

			// Some data to feed to the base exception.
			$code    = $code    !== null ? $code    : $result->getCode();
			$message = $message !== null ? $message : $result->getErrors();

			// Proceed to create the base exception.
			parent::__construct($message, $code, $previous);
		}

		/**
		 * Returns the Result of the Call.
		 *
		 * @return  system\call\Result  The Result of the Call which caused the exception.
		 */

		public function getResult()
		{
			return $this->result;
		}
	}