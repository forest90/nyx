<?php namespace nyx\console\exceptions\dialog;

	// Internal dependencies
	use nyx\console\dialog;

	/**
	 * Attempts Allowed Exceeded Exception
	 *
	 * @package     Nyx\Console\Diagnostics
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/exceptions.html
	 */

	class AttemptsAllowedExceeded extends InvalidAnswer
	{
		/**
		 * {@inheritDoc}
		 */

		public function __construct(dialog\questions\Validated $question, $answer = null, $message = null, $code = 0, \Exception $previous = null)
		{
			// Proceed to create a casual exception.
			parent::__construct($question, $answer, $message ?: "Exceeded the allowed number of attempts [{$question->getAttemptsAllowed()}].", $code, $previous);
		}
	}