<?php namespace nyx\system\call;

	// External dependencies
	use nyx\core;

	// Internal dependencies
	use nyx\system;

	/**
	 * Process
	 *
	 * Note: It is strongly advised to use the system\Call class to build Process instances and execute them as said class
	 * helps with automatically adjusting the Process to the current system environment and covers a few common pitfalls.
	 *
	 * Heavily based on Symfony2's Process component.
	 *
	 * @package     Nyx\System\Calls
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2013 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/system/calls.html
	 * @todo        Allow to write input to a running async process, ie. don't write it only at the beginning.
	 * @todo        Implement events instead of callbacks.
	 */

	class Process implements core\interfaces\Process
	{
		/**
		 * The traits of a Process instance.
		 */

		use core\traits\Process;

		/**
		 * Pipe identifiers.
		 */

		const STDIN  = 0;
		const STDOUT = 1;
		const STDERR = 2;

		/**
		 * Operational flag identifiers.
		 */

		const WINDOWS  = 0;
		const SIGCHILD = 1;
		const TTY      = 2;

		/**
		 * Timeout precision in seconds.
		 */

		const TIMEOUT_PRECISION = 0.2;

		/**
		 * @var string      The command to be run.
		 */

		private $command;

		/**
		 * @var string      The current working directory for the command.
		 */

		private $directory;

		/**
		 * @var array       The environment variables.
		 */

		private $environment;

		/**
		 * @var array       Additional options for proc_open().
		 */

		private $options;

		/**
		 * @var Result      The Result of the Process.
		 */

		private $result;

		/**
		 * @var resource    The actual process after being opened by proc_open().
		 */

		private $process;

		/**
		 * @var array       An array of R/W pipes used by the underlying process.
		 */

		private $pipes;

		/**
		 * @var array       An array of operational flags to adjust the behaviour of the Process to the platform it
		 *                  runs on.
		 */

		private $flags;

		/**
		 * @var array       Windows: Temporary fallback file handles.
		 */

		private $fileHandles;

		/**
		 * @var int         Windows: The amount of bytes that have already been read from the respective files, used
		 *                  to fseek() properly to avoid duplicating data on read.
		 */

		private $readBytes;

		/**
		 * Creates a new Process instance without running it.
		 *
		 * @param   string  $command        The command to be run.
		 * @param   string  $directory      The current working directory for the command.
		 * @param   array   $environment    The environment variables.
		 * @param   array   $options        Additional options for proc_open().
		 * @param   array   $flags          The operational flags to adjust the behaviour of the Process to the
		 *                                  platform it runs on.
		 * @param   int     $timeout        The timeout in seconds.
		 */

		public function __construct($command, $directory, array $environment = null, array $options = null, array $flags = null, $timeout = 60)
		{
			$this->command     = $command;
			$this->directory   = $directory;
			$this->environment = $environment ?: [];
			$this->options     = $options ?: [];
			$this->flags       = $flags ?: [];

			// Set the timeout for this process.
			$this->setTimeout($timeout);
		}

		/**
		 * Starts the process. Blocks until all input is sent to the process, if applicable. Then it returns while
		 * the process keeps running in the background.
		 *
		 * @param   string      $input      The content to pass to the process (think STDIN).
		 * @param   callable    $callback   A callable to call whenever there is output available on STDOUT or STDERR.
		 * @throws  \RuntimeException       When the Process is already running.
		 * @throws  \RuntimeException       When the Process couldn't be opened.
		 * @return  $this
		 */

		public function start($input = null, $callback = null)
		{
			// Cannot start a process that is already running. Either restart() it or manually stop() and start()
			// it again.
			if($this->isRunning()) throw new \RuntimeException("Cannot start a Process that is already running.");

			// Set the start time of the Process.
			$this->status['times']['start'] = microtime(true);

			// Launch the actual process.
			$this->process = proc_open(
				$this->command,
				$this->getDescriptors(),
				$this->pipes,
				$this->directory,
				$this->environment,
				$this->options
			);

			// Check if we got a resource in return.
			if(!is_resource($this->process)) throw new \RuntimeException("Failed to launch the process.");

			// Update the state we're in.
			$this->status['state'] = core\interfaces\Process::STARTED;

			// Set the pipes to non-blocking mode.
			foreach($this->pipes as $pipe) stream_set_blocking($pipe, false);

			// Prepare a Result object.
			$this->result = new Result($this);

			// When running in TTY mode we'll detach here right away while leaving the process running.
			if(isset($this->flags[self::TTY]) and true === $this->flags[self::TTY])
			{
				$this->status['state'] = core\interfaces\Process::TERMINATED;

				return $this;
			}

			// When no additional input was given we won't need to write it to the process, so we can close the pipe
			// and return right away.
			if(null === $input)
			{
				fclose($this->pipes[self::STDIN]);
				unset($this->pipes[self::STDIN]);

				return $this;
			}

			// Otherwise write the input to the underlying process.
			$this->write($input, $this->createOutputWriter($callback));

			return $this;
		}

		/**
		 * Puts the Process in a blocking read loop and waits for it to terminate, then returns its Result.
		 *
		 * @param   callable    $callback           A callable to pass to the read loop. It will receive two arguments /
		 *                                          the type of the data (either self::STDOUT or self::STDERR) and the
		 *                                          data whenever said data is read from the process.
		 * @return  Result
		 * @throws  system\exceptions\call\Timeout  {@see self::read()}
		 * @throws  \RuntimeException               {@see self::abortIfSignaled()}
		 */

		public function wait(callable $callback = null)
		{
			// Enter a blocking read loop until all the pipes are empty and closed.
			$this->read($this->createOutputWriter($callback));

			$this->abortIfSignaled();

			// Give the underlying process some time to gracefully terminate (10 seconds by default).
			$this->awaitTermination();

			$code = proc_close($this->process);

			$this->abortIfSignaled();

			// Set the exit code and return the Result.
			return $this->result->setCode($this->status['process']['running'] ? $code : $this->status['process']['exitcode']);
		}

		/**
		 * Stops the Process.
		 *
		 * @param   integer|float   $timeout    The time in seconds the process will be given to gracefully shut down.
		 * @param   integer         $signal     A POSIX signal number to send to process in case it doesn't stop within
		 *                                      the given timeout. Defaults to SIGKILL.
		 * @return  Result|null                 A Result instance or null if the Process hadn't been started yet and
		 *                                      therefore no Result instance has been constructed.
		 */

		public function stop($timeout = 10, $signal = null)
		{
			// No point in this logic when we aren't even running the process, right?
			if($this->isRunning())
			{
				// Send a SIGTERM signal (the default of proc_terminate()) to the underlying process.
				proc_terminate($this->process);

				// Give the Process some time to gracefully terminate. Kill it if didn't terminate on its own.
				if(!$this->awaitTermination($timeout) and !isset($this->flags[self::SIGCHILD]) and (null !== $signal or defined('SIGKILL')))
				{
					proc_terminate($this->process, $signal ?: SIGKILL);
				}

				// Clean up all open pipes.
				foreach($this->pipes as $pipe) fclose($pipe);
				$this->pipes = [];

				// On Windows platforms we need to clean up the open temporary files we needed to circumvent some bugs.
				if(isset($this->flags[self::WINDOWS]))
				{
					foreach($this->fileHandles as $fileHandle) fclose($fileHandle);

					$this->fileHandles = [];
				}

				// Close the process.
				$code = proc_close($this->process);

				$this->result->setCode(-1 === $this->status['process']['exitcode'] ? $code : $this->status['process']['exitcode']);
			}

			return $this->result;
		}

		/**
		 * Sends a POSIX signal to the underlying process.
		 *
		 * @param   int     $no         A valid POSIX signal number {@see http://www.php.net/manual/en/pcntl.constants.php}.
		 * @return  $this
		 * @throws  \LogicException     When the Process is not running.
		 * @throws  \RuntimeException   When PHP has been compiled with the "--enable-sigchild" flag.
		 * @throws  \RuntimeException   When signaling failed.
		 */

		public function signal($no)
		{
			if(!$this->isRunning())
			{
				throw new \LogicException("Cannot signal a non-running process.");
			}

			if(isset($this->flags[self::SIGCHILD]))
			{
				throw new \RuntimeException("PHP has been compiled with the [--enable-sigchild] flag. The process can not be signaled.");
			}

			if(true !== @proc_terminate($this->process, $no))
			{
				throw new \RuntimeException("An error occurred while sending the signal [$no].");
			}

			return $this;
		}

		/**
		 * Reads output from the process. Blocks until the pipes are empty and closes then automatically when that
		 * happens. All read data is passed to the callback.
		 *
		 * @param   callback|null   $callback       A callable to call when data gets read from the underlying process.
		 * @throws  \LogicException                 When the process isn't running.
		 * @throws  system\exceptions\call\Timeout  When the Process times out while reading.
		 */

		public function read(callable $callback)
		{
			if(!$this->isRunning())	throw new \LogicException("Cannot read from a process that is not running.");

			while($this->pipes or (isset($this->flags[self::WINDOWS]) and $this->fileHandles))
			{
				if(isset($this->flags[self::WINDOWS]) and $this->fileHandles)
				{
					$this->processFileHandles($callback, !$this->pipes);
				}

				if($this->pipes)
				{
					$r = $this->pipes;
					$w = null;
					$e = null;

					// See if anything in stream changed.
					if(false === $n = @stream_select($r, $w, $e, 0, ceil(static::TIMEOUT_PRECISION * 1E6)))
					{
						// Unless the call has simply been interrupted we need to reset the pipes, assuming an error
						// occurred.
						if(!system\Call::hasBeenInterrupted()) $this->pipes = [];

						continue;
					}

					// If nothing has changed, continue looping until it does.
					if(0 === $n) continue;

					foreach($r as $type => $pipe)
					{
						$data = fread($pipe, 8192);

						if(strlen($data) > 0)
						{
							// The exit code is output on the 4th pipe as workaround against
							// the "--enable-sigchild" flag.
							if(3 == $type)
							{
								$this->result->setCode($data);
							}
							else
							{
								call_user_func($callback, $type, $data);
							}
						}

						if(false === $data or feof($pipe))
						{
							fclose($pipe);
							unset($this->pipes[$type]);
						}
					}
				}

				try
				{
					$this->enforceTimeout();
				}
				catch(\RuntimeException $e)
				{
					// Re-throw a timeout specific exception.
					throw new system\exceptions\call\Timeout("The Process timed out while reading the output.", $this->result);
				}
			}
		}

		/**
		 * Writes the given input to the underlying process assuming the write pipe is still open. Blocks until all
		 * the input has been written to the process.
		 *
		 * @param   string              $input              The content to pass to the process (think STDIN).
		 * @param   callback|null       $callback           A callable to call when data gets output by the process
		 *                                                  in response to the input given.
		 * @throws  \LogicException                         When the process isn't running.
		 * @throws  \RuntimeException                       When the write pipe is closed.
		 * @throws  system\exceptions\call\timeouts\Write   When the Process times out while writing the input.
		 */

		public function write($input, callable $callback)
		{
			if(!$this->isRunning())
			{
				throw new \LogicException("Cannot write input to a process that is not running.");
			}

			if(!isset($this->pipes[self::STDIN]))
			{
				throw new \RuntimeException("Cannot write the input to the process as the input pipe is not open.");
			}

			$writePipes = [$this->pipes[0]];
			unset($this->pipes[0]);

			// We'll need those two to keep track of what has already been written and what still needs to be passed
			// to the process.
			$input       = (string) $input;
			$inputLength = strlen($input);
			$inputAt     = 0;

			while($writePipes)
			{
				if(isset($this->flags[self::WINDOWS])) $this->processFileHandles($callback);

				$read   = $this->pipes;
				$write  = $writePipes;
				$except = null;

				if(false === $n = @stream_select($read, $write, $except, 0, ceil(static::TIMEOUT_PRECISION * 1E6)))
				{
					// We're going to ignore interrupted system calls and try again.
					if(system\Call::hasBeenInterrupted()) continue;

					break;
				}

				// nothing has changed, let's wait until the process is ready
				if(0 === $n) continue;

				if($write)
				{
					$written = fwrite($writePipes[0], (binary) substr($input, $inputAt), 8192);

					// When any number of bytes has been written to the process, keep track of what's still left to write.
					if(false !== $written) $inputAt += $written;

					// If we've written all the input we had to the process, close the write pipe.
					if($inputAt >= $inputLength)
					{
						fclose($writePipes[0]);
						$writePipes = null;
					}
				}

				foreach($read as $type => $pipe)
				{
					$data = fread($pipe, 8192);

					if(strlen($data) > 0)
					{
						call_user_func($callback, $type, $data);
					}

					if(false === $data or feof($pipe))
					{
						fclose($pipe);
						unset($this->pipes[$type]);
					}
				}

				try
				{
					$this->enforceTimeout();
				}
				catch(\RuntimeException $except)
				{
					// Re-throw a write-specific timeout exception with the insofar written input available.
					throw new system\exceptions\call\timeouts\Write("The Process timed out while writing the input.", $this->result, substr($input, 0, $inputAt));
				}
			}
		}

		/**
		 * Returns the command used to start this Process.
		 *
		 * @return  string
		 */

		public function getCommand()
		{
			return $this->command;
		}

		/**
		 * Returns the current working directory of this Process.
		 *
		 * @return  string
		 */

		public function getDirectory()
		{
			return $this->directory;
		}

		/**
		 * Returns the environmental variables of this Process.
		 *
		 * @return  array
		 */

		public function getEnvironment()
		{
			return $this->environment;
		}

		/**
		 * Returns the Result instance created when the Process gets started. Please note that the Result is available
		 * throughout the whole of a Process' lifetime but is considered "complete" only after it receives an exit code
		 * from the Process.
		 *
		 * @return  Result  The Result instance or null if this Process has not been started yet.
		 */

		public function getResult()
		{
			return $this->result;
		}

		/**
		 * Updates the current error output of the process (STDERR).
		 */

		public function updateErrors()
		{
			if(isset($this->pipes[self::STDERR]) and is_resource($this->pipes[self::STDERR]))
			{
				$this->result->write(self::STDERR, stream_get_contents($this->pipes[self::STDERR]));
			}
		}

		/**
		 * Updates the current output of the process (STDOUT).
		 */

		public function updateOutput()
		{
			if(isset($this->flags[self::WINDOWS]) and isset($this->fileHandles[self::STDOUT]) and is_resource($this->fileHandles[self::STDOUT]))
			{
				fseek($this->fileHandles[self::STDOUT], $this->readBytes[self::STDOUT]);
				$this->result->write(self::STDOUT, stream_get_contents($this->fileHandles[self::STDOUT]));
			}
			elseif(isset($this->pipes[self::STDOUT]) and is_resource($this->pipes[self::STDOUT]))
			{
				$this->result->write(self::STDOUT, stream_get_contents($this->pipes[self::STDOUT]));
			}
		}

		/**
		 * Returns the PID of the underlying process if it is running.
		 *
		 * @return  int|null            The process ID if the process is running, null otherwise.
		 * @throws  \RuntimeException   When PHP has been compiled with the --enable-sigchild flag.
		 */

		public function getPid()
		{
			if(isset($this->flags[self::SIGCHILD]))
			{
				throw new \RuntimeException('PHP has been compiled with the [--enable-sigchild] flag. The process identifier can not be retrieved.');
			}

			return $this->isRunning() ? $this->status['process']['pid'] : null;
		}

		/**
		 * {@inheritDoc}
		 *
		 * Overridden since we rely on PHP internals to keep track of the status of the underlying process and therefore
		 * have to fetch said data from PHP whenever the state gets requested.
		 */

		public function getState()
		{
			$this->updateStatus();

			return $this->status['state'];
		}

		/**
		 * Checks whether the underlying process has been terminated with an uncaught signal.
		 *
		 * @return  bool                True when the process has been terminated with an uncaught signal, false otherwise
		 *                              or when running on a Windows platform.
		 * @throws  \RuntimeException   When PHP has been compiled with the --enable-sigchild flag.
		 */

		public function hasBeenSignaled()
		{
			if(isset($this->flags[self::SIGCHILD]))
			{
				throw new \RuntimeException("PHP has been compiled with the [--enable-sigchild] flag. Cannot check whether the process has been signaled.");
			}

			$this->updateStatus();

			return $this->status['process']['signaled'];
		}

		/**
		 * Returns the number of the signal that caused the child process to terminate its execution. Only meaningful
		 * if hasBeenSignaled() returns true.
		 *
		 * @return  int                 The signal number.
		 * @throws  \RuntimeException   When PHP has been compiled with the --enable-sigchild flag.
		 */

		public function getTermSignal()
		{
			if(isset($this->flags[self::SIGCHILD]))
			{
				throw new \RuntimeException("PHP has been compiled with the [--enable-sigchild] flag. The term signal can not be retrieved.");
			}

			$this->updateStatus();

			return $this->status['process']['termsig'];
		}

		/**
		 * Checks whether the child process has been stopped by a signal.
		 *
		 * @return  bool    True when the child process has been stopped by a signal, false otherwise or when running
		 *                  on a Windows platform.
		 */

		public function hasBeenStopped()
		{
			$this->updateStatus();

			return $this->status['process']['stopped'];
		}

		/**
		 * Returns the number of the signal which caused the child process to stop its execution. Only meaningful if
		 * hasBeenStopped() returns true.
		 *
		 * @return  int
		 */

		public function getStopSignal()
		{
			$this->updateStatus();

			return $this->status['process']['stopsig'];
		}

		/**
		 * Checks if the underlying process has been signaled and throws an exception if that is the case.
		 *
		 * @throws  \RuntimeException   When the process has been signaled.
		 */

		protected function abortIfSignaled()
		{
			$this->updateStatus();

			if($this->status['process']['signaled'])
			{
				if(isset($this->flags[self::SIGCHILD]))
				{
					throw new \RuntimeException("The process has been terminated with an unknown signal.");
				}

				throw new \RuntimeException("The process has been terminated with signal [{$this->status['process']['termsig']}].");
			}
		}

		/**
		 * Starts a blocking loop which runs as long as the Process is still running but no longer than the given
		 * timeout.
		 *
		 * @param   int     $timeout    The timeout in seconds the Process should be given to terminate.
		 * @return  bool                True when the Process terminated within the given timeout, false if it is still
		 *                              running.
		 */

		protected function awaitTermination($timeout = 10)
		{
			// Convert the timeout into miliseconds and prepare a timer.
			$timeout = (int) $timeout * 1E6;
			$timer = 0;

			// One increment per second.
			while($this->isRunning() and $timer < $timeout)
			{
				$timer += 1000;
				usleep(1000);
			}

			return !$this->isRunning();
		}

		/**
		 * Creates and returns the descriptors needed by proc_open().
		 *
		 * @return  array               An array with descriptor definitions in a format understood by proc_open().
		 * @throws  \RuntimeException   When running on a Windows platform and a temporary write file could not be opened.
		 */

		protected function getDescriptors()
		{
			// Workaround for PHP bug #51800 (@see https://bugs.php.net/bug.php?id=51800) - using temporary files
			// instead of pipes on Windows platforms.
			if(isset($this->flags[self::WINDOWS]))
			{
				$this->fileHandles = [self::STDOUT => tmpfile()];

				if(false === $this->fileHandles[self::STDOUT])
				{
					throw new \RuntimeException("A temporary file could not be opened to write the process output to. Verify that the directory specified in your TEMP environment variable is writable.");
				}

				$this->readBytes = [self::STDOUT => 0];

				return [
					['pipe', 'r'],
					$this->fileHandles[self::STDOUT],
					['pipe', 'w']
				];
			}

			if(isset($this->flags[self::TTY]) and true === $this->flags[self::TTY])
			{
				$descriptors = [
					['file', '/dev/tty', 'r'],
					['file', '/dev/tty', 'w'],
					['file', '/dev/tty', 'w'],
				];
			}
			else
			{
				$descriptors = [
					['pipe', 'r'], // 0: STDIN
					['pipe', 'w'], // 1: STDOUT
					['pipe', 'w'], // 2: STDERR
				];
			}

			// On non-Windows platforms where PHP is built with the "--enable-sigchild" flag an additional fourth
			// write pipe will be defined so that we can catch the proper exit code. The Call's toString() method
			// also accordingly modifies the command to be used by proc_open() to account for this.
			if(isset($this->flags[self::SIGCHILD]) and true === $this->flags[self::SIGCHILD])
			{
				$descriptors = array_merge($descriptors, [['pipe', 'w']]);
			}

			return $descriptors;
		}

		/**
		 * Builds up the closure responsible for writing to the Result's buffers and calling the user provided
		 * callable.
		 *
		 * @param   callable    $callback   The user provided callable.
		 * @return  callable
		 */

		protected function createOutputWriter(callable $callback = null)
		{
			return function($type, $data) use ($callback)
			{
				$this->result->write($type, $data);

				if(null !== $callback) call_user_func($callback, $type, $data);
			};
		}

		/**
		 * Updates the status of the process.
		 *
		 * @return  bool    True when the status got updated, false otherwise.
		 */

		protected function updateStatus()
		{
			// No point in trying to update the status when the process hasn't actually been started.
			if(core\interfaces\Process::STARTED !== $this->status['state']) return false;

			// Grab the process status.
			$this->status['process'] = proc_get_status($this->process);

			if(!$this->status['process']['running'])
			{
				$this->status['state'] = core\interfaces\Process::TERMINATED;

				// The proper exit code is only available at the first call to proc_get_status(). On each subsequent
				// call it will return -1 so we need to remember it when the time is right.
				if(-1 !== $this->status['process']['exitcode'])
				{
					$this->result->setCode($this->status['process']['exitcode']);
				}
			}

			return true;
		}

		/**
		 * Processes the file handles opened as fallback on Windows platforms.
		 *
		 * @param   callable    $callback           A callable to call when there is new data available in any of the
		 *                                          open file handles.
		 * @param   bool        $closeEmptyHandles  Whether handles that are empty will be automatically closed.
		 */

		protected function processFileHandles(callable $callback, $closeEmptyHandles = false)
		{
			foreach($this->fileHandles as $type => $handle)
			{
				fseek($handle, $this->readBytes[$type]);

				$data = fread($handle, 8192);

				if(strlen($data) > 0)
				{
					$this->readBytes[$type] += strlen($data);
					call_user_func($callback, $type, $data);
				}

				if(false === $data or ($closeEmptyHandles and '' === $data and feof($handle)))
				{
					fclose($handle);
					unset($this->fileHandles[$type]);
				}
			}
		}

		/**
		 * Ensures the cloned Process instance will be in a 'ready' state with no references to the underlying running
		 * process if applicable.
		 */

		public function __clone()
		{
			$this->result       = null;
			$this->pipes        = null;
			$this->process      = null;
			$this->fileHandles  = null;
			$this->readBytes    = null;

			$this->status =
			[
				'state'   => core\interfaces\Process::READY,
				'process' => [],
				'times'   =>
				[
					'start' => null,
					'stop'  => null
				]
			];
		}

		/**
		 * Ensures the Process gets stopped if it still running during object destruction.
		 */

		public function __destruct()
		{
			$this->stop();
		}
	}