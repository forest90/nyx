<?php namespace nyx\core\interfaces;

	/**
	 * Process Interface
	 *
	 * A Process is an object that can be run and stopped and provides information about the state it is currently in.
	 *
	 * @package     Nyx\Core\Interfaces
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/interfaces.html
	 */

	interface Process
	{
		/**
		 * Process status constants.
		 */

		const READY      = 0;
		const STARTED    = 1;
		const PAUSED     = 2;
		const TERMINATED = 3;

		/**
		 * Starts the Process.
		 *
		 * @return  $this
		 */

		public function start();

		/**
		 * Stops the Process, ie. terminates it.
		 *
		 * @param   int     $timeout    The time in seconds the Process should be given to stop gracefully.
		 * @return  $this
		 */

		public function stop($timeout = 10);

		/**
		 * Pauses the Process, allowing it to be resumed later.
		 *
		 * @return  $this
		 */

		public function pause();

		/**
		 * Resumes the Process, effectively putting it back to work where it left off.
		 *
		 * @return  $this
		 */

		public function resume();

		/**
		 * Restarts the Process. If the Process is already running, the implementer of this interface instead of throwing
		 * an Exception must stop the Process and start it again, akin to how most Linux distros handle system services.
		 *
		 * @return  self
		 */

		public function restart();

		/**
		 * Checks whether the Process is currently running.
		 *
		 * @return  bool    True when the Process is running, false otherwise.
		 */

		public function isRunning();

		/**
		 * Checks whether the Process has been started with no regard to whether it is still running or not.
		 *
		 * @return  bool    True when the Process has been started, false otherwise.
		 */

		public function isStarted();

		/**
		 * Checks whether the Process has been paused.
		 *
		 * @return  bool    True when the Process has been paused, false otherwise.
		 */

		public function isPaused();

		/**
		 * Checks whether the Process has been terminated.
		 *
		 * @return  bool    True when the Process is stopped, false otherwise.
		 */

		public function isTerminated();

		/**
		 * Returns the state the Process is in. The value returned is one of the state constants defined in the
		 * interface.
		 *
		 * @return  int     The current state of the Process.
		 */

		public function getState();

		/**
		 * Sets the state the Process is in.
		 *
		 * @param   int     $state  The current state of the Process.
		 * @return  $this
		 */

		public function setState($state);

		/**
		 * Returns the timeout time of the Process.
		 *
		 * @return  int|null    The timeout in seconds when set, null otherwise.
		 */

		public function getTimeout();

		/**
		 * Sets the timeout time of the Process.
		 *
		 * @param   float|null  $seconds        The timeout time in seconds or null to disable it.
		 * @return  $this
		 * @throws  \InvalidArgumentException   When the given timeout is negative.
		 */

		public function setTimeout($seconds = null);

		/**
		 * Checks whether the time the Process has been running exceeds the set timeout.
		 *
		 * @return  bool    True when the Process timed out, false otherwise.
		 */

		public function checkTimeout();

		/**
		 * Checks whether the Process has exceeded the timeout it has been given and if has - stops it immediately and
		 * throws an exception.
		 *
		 * @throws  \RuntimeException   When the timeout was reached.
		 */

		public function enforceTimeout();
	}