<?php namespace nyx\console\helpers;

	// Internal dependencies
	use nyx\console\interfaces;
	use nyx\console\input;
	use nyx\console\output;
	use nyx\console;

	/**
	 * Dialog Helper
	 *
	 * Intended as basic helper to deal with the most common use cases and as such it does not support hiding answers,
	 * as all question-related methods will return the user's answers directly, without giving access to the Question
	 * object to modify its behavior before the question is asked. If you need more granular control over the questions
	 * (including the ability to hide answers), consider simply creating the respective Question objects yourself.
	 * You will find them and their documentation under console/dialog.
	 *
	 * Both an Input and an Output instance may be set within the helper - to provide sane defaults, if either of them
	 * is not set, the helper will assume Stdin/Stdout respectively. The Output instance to use may also be passed
	 * to any of the question methods directly, which will override the defaults for that call.
	 *
	 * @package     Nyx\Console\Helpers
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/helpers.html
	 */

	class Dialog extends console\Helper
	{
		/**
		 * @var input\Stream  The Stream instance in use for interactive input.
		 */

		private $input;

		/**
		 * {@inheritDoc}
		 */

		public function __construct($name = null, interfaces\Output $output = null)
		{
			parent::__construct($name ?: 'dialog', $output);
		}

		/**
		 * Asks a basic question where the answer may be anything.
		 *
		 * @see     console\dialog\Question
		 * @param   string              $question   The question's text to be displayed to the user.
		 * @param   mixed               $default    The default answer which will be used when the user provides none.
		 * @param   interfaces\Output   $output     Refer to the class description for information about I\O defaults.
		 * @return  string                          The answer.
		 */

		public function ask($question, $default = null, interfaces\Output $output = null)
		{
			return (new console\dialog\Question($question, $default))
						->ask($this->getInput(), $output ?: $this->getOutput());
		}

		/**
		 * Asks a simple yes/no question.
		 *
		 * @see     console\dialog\questions\Confirmed
		 * @param   string              $question   The question's text to be displayed to the user.
		 * @param   mixed               $default    The default answer which will be used when the user provides none.
		 * @param   interfaces\Output   $output     Refer to the class description for information about I\O defaults.
		 * @return  bool                            The boolean representation of the answer.
		 */

		public function confirm($question, $default = true, interfaces\Output $output = null)
		{
			return (new console\dialog\questions\Confirmed($question, $default))
						->ask($this->getInput(), $output ?: $this->getOutput());
		}

		/**
		 * Asks a question for which the answer needs to pass a given truth test.
		 *
		 * Important: The validator needs to accept the user's answer and return it when it is valid or throw an
		 *            exception otherwise. If a default answer is set, it also needs to pass validation.
		 *
		 * @see     console\dialog\questions\Validated
		 * @param   string              $question   The question's text to be displayed to the user.
		 * @param   callable            $validator  The truth test which needs to be passed by an answer.
		 * @param   mixed               $default    The default answer which will be used when the user provides none
		 *                                          (also needs to pass validation).
		 * @param   int|bool            $attempts   {@see console\dialog\questions\Validated::setAllowedAttempts()}.
		 * @param   interfaces\Output   $output     Refer to the class description for information about I\O defaults.
		 * @return  mixed                           The answer after passing validation.
		 */

		public function validated($question, callable $validator, $default = null, $attempts = false, interfaces\Output $output = null)
		{
			return (new console\dialog\questions\Validated($question, $validator, $default, $attempts))
						->ask($this->getInput(), $output ?: $this->getOutput());
		}

		/**
		 * Asks a question with predefined choices.
		 *
		 * @see     console\dialog\questions\Choice
		 * @param   string              $question   The question's text to be displayed to the user.
		 * @param   array               $choices    The answer choices. {@see console\dialog\questions\Choice::addChoices()}.
		 * @param   mixed               $default    The default answer which will be used when the user provides none
		 *                                          (needs to point to one of the choices).
		 * @param   interfaces\Output   $output     Refer to the class description for information about I\O defaults.
		 * @return  mixed                           The choice the user made.
		 */

		public function choice($question, array $choices, $default = null, interfaces\Output $output = null)
		{
			return (new console\dialog\questions\Choice($question, $choices, $default))
						->ask($this->getInput(), $output ?: $this->getOutput());
		}

		/**
		 * Displays an interactive menu with predefined choices.
		 *
		 * @see     console\dialog\questions\Menu
		 * @param   string              $question   The question's text to be displayed to the user.
		 * @param   array               $choices    The answer choices. {@see console\dialog\questions\Menu::addChoices()}.
		 * @param   mixed               $default    The default answer which will be used when the user provides none
		 *                                          (needs to point to one of the choices).
		 * @param   interfaces\Output   $output     Refer to the class description for information about I\O defaults.
		 * @return  mixed                           The choice the user made.
		 */

		public function menu($question, array $choices, $default = null, interfaces\Output $output = null)
		{
			return (new console\dialog\questions\Menu($question, $choices, $default))
				->ask($this->getInput(), $output ?: $this->getOutput());
		}

		/**
		 * Sets the input stream to read from when interacting with the user.
		 *
		 * @param   input\Stream    $stream
		 */

		public function setInput(input\Stream $stream)
		{
			$this->input = $stream;
		}

		/**
		 * Returns the input Stream instance. If none is set, Stdin will be instantiated and used.
		 *
		 * @return  input\Stream
		 */

		public function getInput()
		{
			return $this->input ?: $this->input = new input\Stdin;
		}
	}