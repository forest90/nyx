<?php namespace nyx\system\exceptions\call\timeouts;

	// Internal dependencies
	use nyx\system\exceptions;
	use nyx\system\call;

	/**
	 * Write Timeout Exception
	 *
	 * @package     Nyx\System\Calls
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/system/calls.html
	 */

	class Write extends exceptions\call\Timeout
	{
		/**
		 * @var string  The input that was being written to the Process when it timed out.
		 */

		private $input;

		/**
		 * {@inheritDoc}
		 *
		 * @param   string  $input  The input that was being written to the Process when it timed out.
		 */

		public function __construct($input, call\Result $result, $message = null, $code = null, \Exception $previous = null)
		{
			// Make sure the input we were trying to write is available within this exception.
			$this->input = (string) $input;

			parent::__construct($result, $message, $code, $previous);
		}

		/**
		 * Returns the input that was being written to the Process when it timed out.
		 *
		 * @return  string
		 */

		public function getInput()
		{
			return $this->input;
		}
	}