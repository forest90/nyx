<?php namespace nyx\system\call;

	// Internal dependencies
	use nyx\core;

	/**
	 * System Call Result
	 *
	 * Represents the result of a system Call - provides access to stdout, stderr and the exit code of a Process.
	 *
	 * Please note that Results get created internally by Processes and while they are accessible even while a Process
	 * is still running, you should make use of the isComplete() and isSuccessful() methods if you rely on having the
	 * final output on hand. In case of asynchronous processes, however, the respective get*Output() and get*Errors()
	 * methods might come in handy. They will query the underlying Process for new output and give you everything it's
	 * got if you don't need the Process to fully finish.
	 *
	 * @package     Nyx\System\Calls
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/system/calls.html
	 */

	class Result implements core\interfaces\Stringable
	{
		/**
		 * @var array   Exit codes map. User defined errors must use codes in the 64-113 range. The codes 144, 156 and
		 *              158 are not defined. This map conforms to how Unix exit code are standardized and might
		 *              therefore not be relevant for other operating systems.
		 * @see         http://tldp.org/LDP/abs/html/exitcodes.html
		 * @see         http://en.wikipedia.org/wiki/Unix_signal
		 */

		private static $codes =
		[
			  0 => 'OK',
			  1 => 'General error',
			  2 => 'Misuse of shell builtins',
			126 => 'Invoked command cannot execute',
			127 => 'Command not found',
			128 => 'Invalid exit argument',
			129 => 'Hangup',
			130 => 'Interrupt',
			131 => 'Quit and dump core',
			132 => 'Illegal instruction',
			133 => 'Trace/breakpoint trap',
			134 => 'Process aborted',
			135 => 'Bus error: "access to undefined portion of memory object"',
			136 => 'Floating point exception: "erroneous arithmetic operation"',
			137 => 'Kill (terminate immediately)',
			138 => 'User-defined 1',
			139 => 'Segmentation violation',
			140 => 'User-defined 2',
			141 => 'Write to pipe with no one reading',
			142 => 'Signal raised by alarm',
			143 => 'Termination (request to terminate)',
			145 => 'Child process terminated, stopped (or continued*)',
			146 => 'Continue if stopped',
			147 => 'Stop executing temporarily',
			148 => 'Terminal stop signal',
			149 => 'Background process attempting to read from tty ("in")',
			150 => 'Background process attempting to write to tty ("out")',
			151 => 'Urgent data available on socket',
			152 => 'CPU time limit exceeded',
			153 => 'File size limit exceeded',
			154 => 'Signal raised by timer counting virtual time: "virtual timer expired"',
			155 => 'Profiling timer expired',
			157 => 'Pollable event',
			159 => 'Bad syscall',
		];

		/**
		 * @var string      The contents of the output buffer.
		 */

		private $output;

		/**
		 * @var string      The contents of the error buffer.
		 */

		private $errors;

		/**
		 * @var int         The exit code of the Process.
		 */

		private $code;

		/**
		 * @var Process     The Process which created this Result.
		 */

		private $process;

		/**
		 * @var array       The offsets used to keep track of the data when providing incremental updates.
		 */

		private $offsets;

		/**
		 * Creates a new Result container for a Call.
		 *
		 * @param   Process     $process    The Process which created this Result.
		 * @param   string      $output     The contents of the stdout stream, not the actual resource.
		 * @param   string      $errors     The contents of the stderr stream, not the actual resource.
		 * @param   int         $code       The exit code
		 */

		public function __construct(Process $process, $output = null, $errors = null, $code = null)
		{
			$this->process  = $process;
			$this->output   = null === $output ? '' : (string) $output;
			$this->errors   = null === $errors ? '' : (string) $errors;
			$this->code     = null === $code ? $code : (int) $code;

			$this->offsets =
			[
				'output' => 0,
				'error'  => 0
			];
		}

		/**
		 * Appends the given string to one of the internal buffers.
		 *
		 * @param   int     $type               The type of the output (either Process::STDOUT or Process::STDERR).
		 * @param   string  $line               The line to append.
		 * @throws  \InvalidArgumentException   When an unrecognized output type is given.
		 */

		public function write($type, $line)
		{
			switch($type)
			{
				case Process::STDOUT:
					$this->output .= (string) $line;
				break;

				case Process::STDERR:
					$this->errors .= (string) $line;
				break;

				default:
					throw new \InvalidArgumentException("The given output type [$type] is not recognized. Must be one of: ".Process::STDOUT." or ".Process::STDERR.".");
			}
		}

		/**
		 * Returns the Process which created this Result.
		 *
		 * @return  Process
		 */

		public function getProcess()
		{
			return $this->process;
		}

		/**
		 * Returns the contents of the output buffer.
		 *
		 * @return  string
		 */

		public function getOutput()
		{
			$this->process->read(false);

			return $this->output;
		}

		/**
		 * Flushes the contents of the output buffer.
		 *
		 * @return  $this
		 */

		public function flushOutput()
		{
			$this->output = '';
			$this->offsets['output'] = 0;

			return $this;
		}

		/**
		 * Returns only those contents of the output buffer which have been added since the last call to this method.
		 *
		 * @return  string  The contents of the output buffer since the last call to this method.
		 */

		public function getLatestOutput()
		{
			$latest = substr($data = $this->getOutput(), $this->offsets['output']);

			$this->offsets['output'] = strlen($data);

			return $latest;
		}

		/**
		 * Returns the contents of the error buffer.
		 *
		 * @return  string
		 */

		public function getErrors()
		{
			$this->process->read(false);

			return $this->errors;
		}

		/**
		 * Flushes the contents of the error buffer.
		 *
		 * @return  $this
		 */

		public function flushErrors()
		{
			$this->errors = '';
			$this->offsets['errors'] = 0;

			return $this;
		}

		/**
		 * Returns only those contents of the error buffer which have been added since the last call to this method.
		 *
		 * @return  string  The contents of the output buffer since the last call to this method.
		 */

		public function getLatestErrors()
		{
			$latest = substr($data = $this->getErrors(), $this->offsets['errors']);

			$this->offsets['errors'] = strlen($data);

			return $latest;
		}

		/**
		 * Returns the exit code.
		 *
		 * @return  int
		 */

		public function getCode()
		{
			return $this->code;
		}

		/**
		 * Sets the exit code. Only sets the code when it is not yet set or the already set code is equal to -1. -1 is
		 * an integer returned by various proc_* functions and denotes an error in fetching the exit code of the process,
		 * not an error in the actual process. Some fallbacks are in place in the Process class to try to retrieve
		 * the proper exit code in such a situation, so this method acts as a convenience wrapper to reduce the number
		 * of validity checks.
		 *
		 * @param   int     $code
		 * @return  $this
		 */

		public function setCode($code)
		{
			// Only set the code when it is not yet set at all or the set code is equal to -1. See the method's
			// description for details about this behaviour.
			if(null === $this->code or -1 === $this->code) $this->code = (int) $code;

			return $this;
		}

		/**
		 * Returns a string representation of the exit code.
		 *
		 * @return  string  The string representation of the exit code.
		 */

		public function getCodeText()
		{
			return isset(static::$codes[$this->code]) ? static::$codes[$this->code] : 'Unknown error';
		}

		/**
		 * Checks whether the underlying Process which created this result has finished with no regard to how it has
		 * finished if applicable.
		 *
		 * @return  bool    True when the Process has finished, false otherwise.
		 * @todo            Might have to account for paused Processes if/when this gets implemented.
		 */

		public function isComplete()
		{
			return ($this->process->isStarted() and !$this->process->isRunning());
		}

		/**
		 * Checks whether this Result is of a Process that ended successfully or with an error code.
		 *
		 * @return  bool    True if the Process ended successfully, false otherwise.
		 */

		public function isSuccessful()
		{
			return ($this->isComplete() and $this->code === 0);
		}

		/**
		 * {@inheritDoc}
		 *
		 * Alias for {@see self::getOutput()}
		 */

		public function toString()
		{
			return $this->getOutput();
		}

		/**
		 * {@inheritDoc}
		 */

		public function __toString()
		{
			return $this->toString();
		}
	}