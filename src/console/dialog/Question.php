<?php namespace nyx\console\dialog;

	// External dependencies
	use nyx\connect;
	use nyx\utils;

	// Internal dependencies
	use nyx\console\interfaces;
	use nyx\console\exceptions;
	use nyx\console\input;
	use nyx\console\traits;
	use nyx\console;

	/**
	 * Question
	 *
	 * Base question class. Allows to ask a question for which the answer does not need to pass any sort of validation
	 * and which may have a default answer (used when the user provides no answer himself). An answer may also be hidden,
	 * either by setting the self::$hideAnswers flag to true before asking a question or by using the self::askHideAnswer()
	 * method directly.
	 *
	 * Provides basic facilities for child classes to retrieve a user's response, handle a default answer, hide the input
	 * and deal with the presentation of the question.
	 *
	 * @package     Nyx\Console\Dialog
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/dialog.html
	 * @todo        Consider implementing preseeds (including support for non-stream input)
	 */

	class Question
	{
		/**
		 * The traits of a Question instance.
		 */

		use traits\Presented;

		/**
		 * @var string      The question's text to be sent to the Output.
		 */

		private $text;

		/**
		 * @var mixed   The last answer to this Question. Only populated after passing validation (if set) etc. May
		 *              also be automatically set to the default answer if the respective conditions are met.
		 */

		private $answer;

		/**
		 * @var mixed   The default answer.
		 */

		private $default;

		/**
		 * @var bool    Whether the user's answers to this question should be hidden or not.
		 */

		private $hideAnswers;

		/**
		 * @var bool    Whether the user's answers may still be displayed if they cannot be properly hidden.
		 */

		private $allowFallback;

		/**
		 * @var string  A path to a shell binary that can be used to hide the answer.
		 */

		private static $shell;

		/**
		 * Constructor.
		 *
		 * @param   string  $text       The question's text to be sent to the Output.
		 * @param   mixed   $default    The default answer.
		 * @param   string  $format     The presentation format of the Question.
		 */

		public function __construct($text, $default = null, $format = '  %text% [%default%] ')
		{
			$this->setText($text);
			$this->setDefault($default);
			$this->setPresentationFormat($format);
		}

		/**
		 * Asks the user the question.
		 *
		 * @param   input\Stream        $input      The Input Stream instance from which the answer will be read.
		 * @param   interfaces\Output   $output     The Output instance to which the question should be printed.
		 * @param   bool                $force      If set to true, the answer will be displayed regardless of the
		 *                                          settings regarding hiding answers.
		 * @return  string                          The answer.
		 * @throws  exceptions\input\StreamAborted  When no data could be read from the Input Stream.
		 */

		public function ask(input\Stream $input, interfaces\Output $output, $force = false)
		{
			// If we're not forcing this and are set to hide the answer, reroute this to the hide method. Kept this way
			// to allow for a simpler API, in spite of the ugly $force flag.
			if(!$force and $this->hideAnswers) return $this->askHideAnswer($input, $output);

			$output->write($this->asText());

			try
			{
				$answer = trim($input->getStream()->line(4096));
			}
			catch(connect\exceptions\stream\Read $exception)
			{
				// Re-throw a Console-specific exception while preserving the stack-trace.
				throw new exceptions\input\StreamAborted($input, null, $exception);
			}

			// Remember the last answer within the instance.
			return $this->setAnswer($answer);
		}

		/**
		 * Asks the question and hides the user's input. Wrapper for the environment-specific input hiding methods.
		 *
		 * Note: The availability checks are duplicated inside the wrapped methods. While adding some overhead,
		 * having the methods separate also provides some better extensibility. Also note that the Input Stream
		 * will only get used on non-Windows systems with Stty available, as the other methods use the results of
		 * shell_exec calls directly to get the input.
		 *
		 * @param   input\Stream        $input      The input Stream instance from which the answer will be read.
		 * @param   interfaces\Output   $output     The Output instance to which the question should be printed.
		 * @return  string                          The answer.
		 * @throws  \RuntimeException               When the answer cannot be hidden and the method couldn't fall back
		 *                                          to asking the question the casual way.
		 */

		public function askHideAnswer(input\Stream $input, interfaces\Output $output)
		{
			// Special developer love for Windows.
			if(defined('PHP_WINDOWS_VERSION_BUILD')) return $this->askHideAnswerUsingExe($output);

			// Optimal case is when STTY is available, as we can make proper use of our Stream instance.
			if(utils\System::hasStty()) return $this->askHideAnswerUsingStty($input, $output);

			// Last attempt - use one of the supported shells to hide the answer.
			if($this->getShell()) return $this->askHideAnswerUsingShell($output);

			// Everything else failed. Shall we ask the question the casual way?
			if($this->allowFallback) return $this->ask($input, $output, true);

			throw new \RuntimeException("Unable to hide the answer for the question '{$this->getText()}'");
		}

		/**
		 * Sets whether the user's answers to this question should be hidden or not.
		 *
		 * @param   bool    $bool
		 */

		public function setHideAnswers($bool)
		{
			$this->hideAnswers = (bool) $bool;
		}

		/**
		 * Sets whether the user's answers may be displayed if they cannot be properly hidden.
		 *
		 * @param   bool    $bool
		 */

		public function setAllowFallback($bool)
		{
			$this->allowFallback = (bool) $bool;
		}

		/**
		 * Returns the user's last answer to this Question. May be equal to the default if it is set and the user did
		 * not provide an answer himself.
		 *
		 * @return  string
		 */

		public function getAnswer()
		{
			return $this->answer;
		}

		/**
		 * Sets an answer to this Question. By default the 'default answer' will be set if the provided answer
		 * does not contain any characters. Considering this behaviour, this setter uniquely also returns the value
		 * it set.
		 *
		 * @param   string  $answer
		 * @return  mixed
		 */

		public function setAnswer($answer)
		{
			return $this->answer = (strlen($answer) > 0) ? $answer : $this->getDefault();
		}

		/**
		 * Returns the default answer.
		 *
		 * @return  mixed
		 */

		public function getDefault()
		{
			return $this->default;
		}

		/**
		 * Sets the default answer.
		 *
		 * @param   mixed   $answer
		 */

		public function setDefault($answer)
		{
			$this->default = $answer;
		}

		/**
		 * Returns the question's text.
		 *
		 * @return  string
		 */

		public function getText()
		{
			return $this->text;
		}

		/**
		 * Sets the question's text.
		 *
		 * @param   string  $text               The text to set.
		 * @throws  \InvalidArgumentException   When the text is not a string.
		 */

		public function setText($text)
		{
			if(!is_string($text))
			{
				throw new \InvalidArgumentException("A question's text must be a string, ".gettype($text)." given.");
			}

			$this->text = $text;
		}

		/**
		 * Asks the question and hides the user's input using an .exe. A path to said executable may optionally be
		 * provided if you don't want to/can't use the bundled one.
		 *
		 * Note: Has no way of knowing whether the .exe succeeded in actually hiding the input (checking the exit code
		 * would be of no help). Use the bundled, default executable (courtesy of https://github.com/Seldaek/hidden-input)
		 * to be on the safe side.
		 *
		 * @param   interfaces\Output   $output     The Output instance to which the question should be printed.
		 * @param   string              $exe        A path to to an .exe which should be used to hide the answer.
		 * @return  string                          The answer.
		 * @throws  \LogicException                 When attempting to use the method outside of Windows-based systems.
		 * @throws  \InvalidArgumentException       When the given .exe could not be found or is not executable by this
		 *                                          script. The check is also performed for the bundled executable.
		 * @todo                                    Breaks (most likely) when run from within a .phar.
		 */

		protected function askHideAnswerUsingExe(interfaces\Output $output, $exe = null)
		{
			if(!utils\System::isWindows()) throw new \LogicException("Cannot hide answer: Can only hide the input using an .exe on Windows-based systems.");

			// Use the default .exe when none was given.
			$exe = $exe ?: dirname(__DIR__) . '/resources/bin/hideinput.exe';

			// Ensure the file exists and is executable.
			if(!is_executable($exe)) throw new \InvalidArgumentException("Cannot hide answer: The given path [$exe] is not executable.");

			$output->write($this->asText());

			return $this->setAnswer(rtrim(shell_exec($exe)));
		}

		/**
		 * Asks the question and hides the user's input using Stty.
		 *
		 * @param   input\Stream        $input      The input Stream instance from which the answer will be read.
		 * @param   interfaces\Output   $output     The Output instance to which the question should be printed.
		 * @return  string                          The answer.
		 * @throws  \LogicException                 When Stty is not available in this environment.
		 * @throws  exceptions\input\StreamAborted  When no data could be read from the Input Stream.
		 */

		protected function askHideAnswerUsingStty(input\Stream $input, interfaces\Output $output)
		{
			if(!utils\System::hasStty()) throw new \LogicException("Cannot hide answer: Stty does not seem to be available in this environment.");

			$answer = null;

			$output->write($this->asText());

			// Remember the mode Stty is running it so we can reset it after we're done, then force Stty to hide all input.
			$sttyMode = shell_exec('stty -g') and shell_exec('stty -echo');

			try
			{
				$answer = $this->setAnswer(rtrim($input->getStream()->line(4096)));
			}
			catch(connect\exceptions\stream\Read $exception)
			{
				// Re-throw a Console-specific exception.
				throw new exceptions\input\StreamAborted($input, null, $exception);
			}
			finally
			{
				// In any case reset Stty to its previous mode.
				shell_exec("stty $sttyMode");
			}

			$output->ln();

			return $answer;
		}

		/**
		 * Asks the question and hides the user's input using a shell.
		 *
		 * @param   interfaces\Output   $output     The Output instance to which the question should be printed.
		 * @return  string                          The answer.
		 * @throws  \LogicException                 When no supported shell is available in this environment.
		 */

		protected function askHideAnswerUsingShell(interfaces\Output $output)
		{
			// Duplicating the check from askHideAnswer() in order not to overcomplicate things by allowing to pass
			// a shell's name/path to this method, and then having to perform further support checks etc. (read:
			// developer laziness).
			if(false === $shell = $this->getShell()) throw new \LogicException('Cannot hide the answer: No supported shell could be found.');

			$output->write($this->asText());

			$readCmd = $shell === 'csh' ? 'set mypassword = $<' : 'read -r mypassword';
			$command = sprintf("/usr/bin/env %s -c 'stty -echo; %s; stty echo; echo \$mypassword'", $shell, $readCmd);

			return $this->setAnswer(rtrim(shell_exec($command)));
		}

		/**
		 * Returns the path to a shell binary that can be used internally to hide an user's answer, or false if no viable
		 * shell could be found.
		 *
		 * @return  string|bool
		 */

		protected function getShell()
		{
			if(self::$shell !== null) return self::$shell;

			foreach(['bash', 'zsh', 'ksh', 'csh'] as $name)
			{
				if($path = utils\System::getShell($name)) return self::$shell = $path;
			}

			return self::$shell = false;
		}

		/**
		 * {@inheritDoc}
		 */

		protected function getPresentationValues()
		{
			return [
				'text'    => $this->getText(),
				'default' => $this->getDefault()
			];
		}
	}