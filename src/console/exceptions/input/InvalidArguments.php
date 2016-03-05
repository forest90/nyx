<?php namespace nyx\console\exceptions\input;

	// Internal dependencies
	use nyx\console\input;

	/**
	 * Invalid Arguments Exception
	 *
	 * @package     Nyx\Console\Diagnostics
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/exceptions.html
	 */

	class InvalidArguments extends InvalidParameters
	{
		/**
		 * @var input\bags\Arguments    The Arguments Bag which is invalid.
		 */

		private $arguments;

		/**
		 * {@inheritDoc}
		 *
		 * @param   input\bags\Arguments    $arguments  The Arguments Bag which is invalid.
		 */

		public function __construct(input\bags\Arguments $cause, $message = null, $code = 0, \Exception $previous = null)
		{
			$this->arguments = $cause;

			// Proceed to create a casual exception.
			parent::__construct($message, $code, $previous);
		}

		/**
		 * Returns the Arguments Bag which caused the exception.
		 *
		 * @return  string
		 */

		public function getArguments()
		{
			return $this->arguments;
		}
	}