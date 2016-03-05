<?php namespace nyx\console\exceptions;

	// Internal dependencies
	use nyx\console;

	/**
	 * Dialog Exception
	 *
	 * @package     Nyx\Console\Diagnostics
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/exceptions.html
	 */

	class Dialog extends \RuntimeException
	{
		/**
		 * @var console\dialog\Question     The Question instance which raised the exception.
		 */

		private $question;

		/**
		 * {@inheritDoc}
		 *
		 * @param   console\dialog\Question $question   The Question instance which raised the exception.
		 */

		public function __construct(console\dialog\Question $question, $message = null, $code = 0, \Exception $previous = null)
		{
			$this->question = $question;

			// Proceed to create a casual exception.
			parent::__construct($message ?: "An error occurred while processing the question '{$question->getText()}'.", $code, $previous);
		}

		/**
		 * Returns the Question instance which raised the exception.
		 *
		 * @return  console\dialog\Question
		 */

		public function getQuestion()
		{
			return $this->question;
		}
	}