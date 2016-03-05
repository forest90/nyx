<?php namespace nyx\console\exceptions\dialog;

	// Internal dependencies
	use nyx\console\dialog;

	/**
	 * Invalid Choice Exception
	 *
	 * @package     Nyx\Console\Diagnostics
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/exceptions.html
	 */

	class InvalidChoice extends InvalidAnswer
	{
		/**
		 * {@inheritDoc}
		 */

		public function __construct(dialog\questions\Choice $question, $answer, $message = null, $code = 0, \Exception $previous = null)
		{
			// Proceed to create a casual exception.
			parent::__construct($question, $answer, $message ?: "The given choice is invalid. Has to be one of: ".implode(', ', $question->getLabels()), $code, $previous);
		}
	}