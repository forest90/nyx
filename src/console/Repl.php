<?php namespace nyx\console;

	/**
	 * Repl
	 *
	 * Despite being a descendant of the Shell class, the Repl class differs in one major aspect from its parent -
	 * instead of passing any output to the console Application it wraps, it eval()s the code given as input.
	 * Wrapping the Application is in this case only a means to give you easier access to it and the environment
	 * it resides in. That said, it's mostly handy for quick testing of your code or performing some one-time
	 * operations instead of writing separate scripts.
	 *
	 * It is similar to the interactive shell {@link http://php.net/manual/en/features.commandline.interactive.php}
	 * built into PHP's CLI SAPI with a few differences - the basic functionality is available even without the
	 * Readline extension; you get easier access to your app's environment and the application itself; you can tune
	 * the behaviour of the console in an OO fashion.
	 *
	 * @package     Nyx\Repl\Shell
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/shell.html
	 */

	class Repl extends Shell
	{
		/**
		 * {@inheritDoc}
		 */

		public function start()
		{
			$this->before();

			// Let's make sure the scope has everything we want to expose. Obviously this can't be run in the before()
			// hook since that is a different scope. Account for that when overriding the method.
			extract($this->expose());

			// Ad infinitum. Well, hopefully not.
			while(true)
			{
				// Intercept 'exit' and '^D' to only break out of the loop and let the after() hook do its stuff,
				// but also to allow the user to use an intuitive command to leave the loop. The condition also takes
				// care of basic trimming so we end up with a clear command.
				//
				// Said trimming can't be done in the first sub-condition as pressing ^D interrupts the input stream and
				// thus equals to sending a boolean false to the Repl (which, btw, is different than returning
				// false from the eval()'d code), so we need a boolean, not a trimmed string.
				if(false === $code = $this->readLine() or 'exit' === $code = rtrim(trim(trim($code), PHP_EOL), ';'))
				{
					$this->getOutput()->ln(2); break;
				}

				// Add this line to history.
				if($this->hasReadline())
				{
					readline_add_history($code);
					readline_write_history($this->getHistoryFilePath());
				}

				// To provide some feedback to the user, we will wrap certain code in a return statement. This way
				// we will be able to parse the return value and decide what kind of feedback can be useful (for instance
				// a var_dump() for variables.
				static::isImmediate($code) and $code = "return ($code)";

				// The actual code will be eval()'d, thus output buffering has to save the day.
				ob_start();

				// Temporary holder;
				$return = null;

				try
				{
					// Let's not fail on something as clumsy as a missing semicolon, right?
					$return = eval($code.';');
				}
				catch(\Exception $exception)
				{
					if($exceptionHandler = \nyx\diagnostics\Debug::getExceptionHandler())
					{
						$exceptionHandler->handle($exception);
					}
				}

				// Code may not directly output anything but return things instead. Depending on what is returned,
				// we will provide different feedback to the user.
				if(ob_get_length() == 0)
				{
					if(is_bool($return))
					{
						$this->getOutput()->writeln($return ? '<info>true</info>' : '<error>false</error>');
					}
					elseif(is_string($return))
					{
						$this->getOutput()->writeln($return);
					}
					elseif($return !== null)
					{
						var_export($return);
					}
				}

				// Time to write the actual output of the code to our Output.
				$this->getOutput()->write(ob_get_contents());

				// Clean up.
				ob_end_clean();
				unset($return);
			}

			$this->after();
		}

		/**
		 * {@inheritDoc}
		 *
		 * We want explicit error reporting settings so we don't get spammed with error messages on a single mistake.
		 */

		protected function configure()
		{
			error_reporting(E_ALL | E_STRICT);

			ini_set("error_log", null);
			ini_set("log_errors", 1);
			ini_set("html_errors", 0);
			ini_set("display_errors", 0);
		}

		/**
		 * Returns an array which will be extracted internally by the {@see self::run()} method within its scope to
		 * allow simplified access to the exposed variables from the actual Repl. The variables will be extracted
		 * only once, before the Repl enters its loop, and you may overwrite them within the actual Repl as you
		 * please.
		 *
		 * Override this method to, for instance, expose an instance of your application, your app's kernel, your
		 * dependency injection container etc. Its merely a utility method and can be completely ignored.
		 *
		 * Note: Do not use the "code" and "return" variable names as they are used by the run() method and will be
		 *       immediately overwritten.
		 *
		 * @return  array
		 */

		protected function expose()
		{
			return ['app' => $this->getApplication()];
		}

		/**
		 * {@inheritDoc}
		 *
		 * Feeds Readline with the internal and user-defined functions, global variables, constants and the variables
		 * exposed to the Repl's scope using self::expose(). Any of the above, when defined within the running Repl
		 * itself, will also be available, except for casual variables since there is no way for this method to access
		 * them in a coherent fashion.
		 *
		 * Note: This method will always return all possible values, regardless of the actual input in the line.
		 *       Readline will still be able to perform its own magic on this set of strings and give somewhat
		 *       accurate results.
		 */

		protected function suggest($line, $position)
		{
			// We are only going to use the internal and user defined functions, so we need to separate them.
			$functions = get_defined_functions();

			return array_merge
			(
				array_keys(get_defined_constants()),
				array_keys($GLOBALS),
				$functions['internal'],
				$functions['user'],
				array_keys($this->expose())
			);
		}

		/**
		 * Determines if the given line of code qualifies to be returned directly. Borrowed (with slight modifications)
		 * from FuelPHP 1.4.
		 *
		 * @author  Phil Sturgeon
		 */

		protected static function isImmediate($line)
		{
			$skip =
			[
				'class', 'declare', 'die', 'echo', 'exit', 'for', 'foreach', 'function', 'global', 'if', 'include',
				'include_once', 'print', 'require', 'require_once',	'return', 'static', 'switch', 'unset', 'while'
			];

			$okeq = ['===', '!==', '==', '!=', '<=', '>='];

			$code = '';
			$sq = false;
			$dq = false;

			for($i = 0; $i < strlen($line); $i++)
			{
				$c = $line{$i};

				if($c == "'")
				{
					$sq = !$sq;
				}
				elseif($c == '"')
				{
					$dq = !$dq;
				}
				elseif(($sq) or ($dq) and $c == "\\")
				{
					++$i;
				}
				else
				{
					$code .= $c;
				}
			}

			$code = str_replace($okeq, '', $code);

			if(strcspn($code, ';{=') != strlen($code)) return false;

			foreach(preg_split('/[^a-z0-9_]/i', $code) as $i)
			{
				if(in_array($i, $skip)) return false;
			}

			return true;
		}
	}