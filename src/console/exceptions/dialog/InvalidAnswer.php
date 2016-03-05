<?php namespace nyx\console\exceptions\dialog;

	// Internal dependencies
	use nyx\console\exceptions;
	use nyx\console\dialog;

	/**
	 * Invalid Answer Exception
	 *
	 * @package     Nyx\Console\Diagnostics
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/exceptions.html
	 */

	class InvalidAnswer extends exceptions\Dialog
	{
		/**
		 * @var string  The answer which caused the exception.
		 */

		private $answer;

		/**
		 * {@inheritDoc}
		 *
		 * @param   string  $answer     The answer which caused the exception. It *should* be passed as under certain
		 *                              circumstances it might get modified internally and the answer set in the Question
		 *                              instance might differ. If it's not passed, the answer set in the Question will
		 *                              be fetched.
		 */

		public function __construct(dialog\Question $question, $answer = null, $message = null, $code = 0, \Exception $previous = null)
		{
			$this->answer = (!empty($answer) and is_string($answer)) ? $answer : $question->getAnswer();

			// Proceed to create a casual exception.
			parent::__construct($question, $message ?: "The given answer [$answer] is invalid.", $code, $previous);
		}

		/**
		 * Returns the answer which caused the exception.
		 *
		 * @return  string
		 */

		public function getAnswer()
		{
			return $this->answer;
		}
	}