<?php namespace nyx\system;

	// External dependencies
	use nyx\utils;

	/**
	 * Call
	 *
	 * Acts as a Process Builder for *blocking* Processes.
	 *
	 * @package     Nyx\System\Calls
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/system/calls.html
	 * @todo        Silent piping by creating command strings simply separated by a | ?
	 */

	class Call
	{
		/**
		 * The traits of a Call instance.
		 */

		use traits\Located;
		use traits\Dependent;

		/**
		 * @var string  The command to execute.
		 */

		private $command;

		/**
		 * @var array   The arguments supplied for this command.
		 */

		private $arguments = [];

		/**
		 * @var array   Environment variables.
		 */

		private $environment;

		/**
		 * @var bool    Whether this Call should inherit $_ENV global variables on top of its own environment variables.
		 */

		private $inheritEnvironment = true;

		/**
		 * @var array   Options to be used by proc_open() when the Call gets executed.
		 */

		private $options;

		/**
		 * @var Call    A command piped to this one, if any.
		 */

		private $next;

		/**
		 * @var bool    Whether the Call should be executed in TTY mode.
		 */

		private $tty;

		/**
		 * @var bool    Whether the Call should be tailored to a Windows platform.
		 */

		private $enhanceWindowsCompatibility = true;

		/**
		 * @var bool    Whether the Call should be tailored to PHP build with the "--enable-sigchild" flag.
		 */

		private $enhanceSigchildCompatibility;

		/**
		 * Constructs a new instance of a system Call.
		 *
		 * @param   string                  $command        The command to execute.
		 * @param   array                   $arguments      Arguments for the command.
		 * @param   string|                 $directory      The working directory in which the call will be executed.
		 * @param   array                   $environment    Environment variables - defaults to the current environment.
		 * @param   array                   $options        Options to be used by proc_open() when the Call gets executed.
		 * @throws  \RuntimeException                       When the proc_open() function is not available.
		 */

		public function __construct($command, array $arguments = null, $directory = null, array $environment = null, array $options = [])
		{
			// Make sure we'll be able to actually execute the Call when the time is right.
			if(!self::$dependenciesMet) $this->dependsOn(Dependency::TYPE_FUNCTION, 'proc_open');

			// Set the mandatory command for this Call.
			$this->setCommand($command);

			// Additional arguments and environment variables.
			if(null !== $arguments)   $this->setArguments($arguments);
			if(null !== $environment) $this->setEnvironment($environment);

			// Set the current working directory.
			if(null !== $directory)
			{
				$this->setDirectory($directory);
			}
			// Windows; If the CWD got changed using chdir(), proc_open will default the CWD to the dir where PHP started.
			// GNU/Linux: PHP builds with the --enable-maintainer-zts flag are affected.
			// @see https://bugs.php.net/bug.php?id=51800 and @see https://bugs.php.net/bug.php?id=50524
			elseif(defined('ZEND_THREAD_SAFE') or utils\System::isWindows())
			{
				$this->setDirectory(getcwd());
			}

			// Default options for proc_open().
			$this->options = array_replace(
			[
				'suppress_errors' => true,
				'binary_pipes'    => true
			], $options);

			$this->enhanceSigchildCompatibility = !utils\System::isWindows() and utils\Php::hasFlag('enable-sigchild');
		}

		/**
		 * Executes the call using the pre-configured command.
		 *
		 * @param   string|null         $input              Content that will be passed to the process.
		 * @param   callable            $callback           A callable to call whenever new output is available, either
		 *                                                  normal output or error output.
		 * @param   int                 $timeout            The timeout in seconds.
		 * @return  call\Process|call\Result                Either a Process when executing in Async mode or a Result
		 *                                                  when executing in blocking mode.
		 */

		public function execute($input = null, callable $callback = null, $timeout = 60)
		{
			$flags = [
				call\Process::WINDOWS  => utils\System::isWindows() ? $this->enhanceWindowsCompatibility : null,
				call\Process::SIGCHILD => utils\Php::hasFlag('enable-sigchild') ? $this->enhanceSigchildCompatibility : null,
				call\Process::TTY      => true === $this->tty
			];

			// Instantiate the Process and start it right away.
			$process = (new call\Process(
				$this->toString(),
				$this->directory,
				$this->inheritEnvironment ? ($this->environment ? $this->environment + $_ENV : null) : $this->environment,
				$this->options,
				$flags,
				$timeout)
			)->start($input, $callback);

			// Let a separate method do the actual execution so it's easier to implement different types of Calls.
			return $this->doExecute($process, $callback, $timeout);
		}

		/**
		 * Returns the command to be executed.
		 *
		 * @return  string
		 */

		public function getCommand()
		{
			return $this->command;
		}

		/**
		 * Sets the command to be executed.
		 *
		 * @param   string  $command    The command to be executed.
		 * @return  $this
		 */

		public function setCommand($command)
		{
			$this->command = (string) $command;

			return $this;
		}

		/**
		 * Returns the arguments for this Call.
		 *
		 * @return  array|null
		 */

		public function getArguments()
		{
			return $this->arguments;
		}

		/**
		 * Sets the arguments for this Call.
		 *
		 * @param   array   $arguments  The unescaped arguments to set.
		 * @return  $this
		 */

		public function setArguments(array $arguments)
		{
			$this->arguments = $arguments;

			return $this;
		}

		/**
		 * Adds a single unescaped argument to this Call.
		 *
		 * @param   string  $argument   The unescaped argument to add.
		 * @return  $this
		 */

		public function addArgument($argument)
		{
			$this->arguments[] = $argument;

			return $this;
		}

		/**
		 * Returns the environment variables for the Call.
		 *
		 * @return  array|null
		 */

		public function getEnvironment()
		{
			return $this->environment;
		}

		/**
		 * Sets the environment variables for the Call. An environment variable's value must be a string. All other
		 * types will be ignored.
		 *
		 * @param   array   $environment
		 * @return  $this
		 */

		public function setEnvironment(array $environment)
		{
			// Ensure all values are strings.
			$environment = array_filter($environment, function ($value)
			{
				return is_string($value);
			});

			$this->environment = [];

			foreach($environment as $key => $value)
			{
				$this->environment[(binary) $key] = (binary) $value;
			}

			return $this;
		}

		/**
		 * Sets whether the Call should inherit $_ENV global variables on top of its own environment variables.
		 *
		 * @param   bool    $bool
		 * @return  $this
		 */

		public function setInheritEnvironmentVariables($bool = true)
		{
			$this->inheritEnvironment = $bool;

			return $this;
		}

		/**
		 * Returns the options to be used by proc_open().
		 *
		 * @return  array   The options.
		 */

		public function getOptions()
		{
			return $this->options;
		}

		/**
		 * Sets the options to be used by proc_open().
		 *
		 * @param   array   $options    The options.
		 * @return  $this
		 */

		public function setOptions(array $options)
		{
			$this->options = $options;

			return $this;
		}

		/**
		 * Sets a particular option to be used by proc_open().
		 *
		 * @param   string  $name   The name of the option to set.
		 * @param   mixed   $value  The value of the option to set.
		 * @return  $this
		 */

		public function setOption($name, $value)
		{
			$this->options[$name] = $value;

			return $this;
		}

		/**
		 * Sets whether the Call should be executed in TTY mode or not.
		 *
		 * @param   bool    $tty    True to enable TTY mode, false otherwise.
		 * @return  $this
		 */

		public function setTty($tty)
		{
			$this->tty = (bool) $tty;

			return $this;
		}

		/**
		 * Pipe another Call to this one, so that this Call's output
		 * is directly sent as input to the piped command.
		 *
		 * @param   Call    $call   The call to pipe.
		 * @return  $this
		 */

		public function pipe(Call $call)
		{
			// Cannot pipe a Call to itself, need to clone it first.
			if($this === $call) $call = clone $call;

			$this->next = $call;

			return $this;
		}

		/**
		 * Returns the Call piped to this one, if any.
		 *
		 * @return  Call
		 */

		public function getPipedCall()
		{
			return $this->next;
		}

		/**
		 * Checks whether Windows compatibility is set to be enhanced. True by default.
		 *
		 * @return  bool    True when Calls will be executed with enhanced Windows compatibility, false otherwise.
		 */

		public function getEnhanceWindowsCompatibility()
		{
			return $this->enhanceWindowsCompatibility;
		}

		/**
		 * Sets whether Windows compatibility is to be enhanced.
		 *
		 * @param   bool    $bool   True to enhance Windows compatibility, false otherwise.
		 * @return  $this
		 */

		public function setEnhanceWindowsCompatibility($bool)
		{
			$this->enhanceWindowsCompatibility = (bool) $bool;

			return $this;
		}

		/**
		 * Checks whether sigchild compatibility is set to be enhanced.
		 *
		 * @return  bool    True when Calls will be executed with enhanced sigchild compatibility, false otherwise.
		 */

		public function getEnhanceSigchildCompatibility()
		{
			return $this->enhanceSigchildCompatibility;
		}

		/**
		 * Sets whether sigchild compatibility is to be enhanced.
		 *
		 * Sigchild compatibility mode is required to get the exit code and determine the success of a process when
		 * PHP has been compiled with the --enable-sigchild flag.
		 *
		 * @param   bool    $bool
		 * @return  $this
		 */

		public function setEnhanceSigchildCompatibility($bool)
		{
			$this->enhanceSigchildCompatibility = (bool) $bool;

			return $this;
		}

		/**
		 * Returns a string representation of this Call with its arguments, the way it would be written in a
		 * command-line to be run.
		 *
		 * @return  string
		 */

		public function toString()
		{
			// Fetch the base command that is to be run.
			$command = $this->command;

			// Temporary holder.
			$args = [];

			// If the arguments container (Cell) is not empty store a reference to it
			// and proceed (the Cell is fully traversable so we can run a foreach() loop
			// on it without hassle.
			if($arguments = $this->getArguments())
			{
				foreach($arguments as $key => $value)
				{
					// Escape the value properly.
					$value = utils\System::escapeArgument($value);

					// If the key is an integer, we're assuming it was not defined as such
					// by the developer but by PHP instead and only the value is important.
					if(is_int($key))
					{
						$args[] = $value;
					}
					// Otherwise the key is the argument and its value becomes the argument's
					// value we need to use.
					else
					{
						$args[] = $key.'='.$value;
					}
				}

				// Append the arguments to the base command
				$command .= ' '.implode(' ', $args);
			}

			// Tailor the command to Windows platforms when asked to do so.
			if(utils\System::isWindows() and $this->enhanceWindowsCompatibility)
			{
				$command = 'cmd /V:ON /E:ON /C "'.$command.'"';

				if(!isset($this->options['bypass_shell']))
				{
					$this->options['bypass_shell'] = true;
				}
			}
			// On non-Windows platforms where PHP is built with the "--enable-sigchild" flag an additional fourth
			// write pipe will be defined by the getDescriptors() method so that we can catch the proper exit code.
			elseif($this->getEnhanceSigchildCompatibility() and utils\Php::hasFlag('enable-sigchild'))
			{
				$command = '('.$command.') 3>/dev/null; code=$?; echo $code >&3; exit $code';
			}

			// Return the final string.
			return $command;
		}

		/**
		 * {@see self::toString()}
		 */

		public function __toString()
		{
			return $this->toString();
		}

		/**
		 * Performs the actual execution of the Call, ie. decides whether to also execute piped Calls or only return
		 * the final result of the first Call.
		 *
		 * {@see self::execute()}
		 */

		protected function doExecute(call\Process $process, callable $callback = null, $timeout = 60)
		{
			// If there are no piped Calls waiting, we can return the full Result right away.
			if(!$piped = $this->getPipedCall()) return $process->wait($callback);

			// Otherwise we shall return the result of the piped call since this (after running the whole
			// chain if the pipe goes on even farther) is the final result we actually want.
			return $piped->execute($process->wait($callback)->getOutput(), $callback, $timeout);
		}

		/**
		 * Checks whether a system call has been interrupted. This check is required at certain times as stream_select()
		 * used in the Process instances returns false when the 'select' system call it performs is interrupted by
		 * an incoming signal.
		 *
		 * @return  bool    True when a system call has been interrupted, false otherwise.
		 */

		public static function hasBeenInterrupted()
		{
			$error = error_get_last();

			return isset($error['message']) and false !== stripos($error['message'], 'interrupted system call');
		}
	}

