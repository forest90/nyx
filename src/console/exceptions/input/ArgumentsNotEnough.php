<?php namespace nyx\console\exceptions\input;

	// Internal dependencies
	use nyx\console\input;

	/**
	 * Not Enough Arguments Exception
	 *
	 * @package     Nyx\Console\Diagnostics
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/exceptions.html
	 */

	class ArgumentsNotEnough extends InvalidArguments
	{
		/**
		 * {@inheritDoc}
		 *
		 * @param   input\bags\Arguments    $arguments  The Arguments Bag which does not runneth over.
		 */

		public function __construct(input\bags\Arguments $cause, $message = null, $code = 0, \Exception $previous = null)
		{
			// Proceed to create a casual exception.
			parent::__construct($cause, $message ?: "Not enough arguments given. Got ".$cause->count().", expected at least ".$cause->definition()->requires(), $code, $previous);
		}
	}