<?php namespace nyx\core\traits;

	// Internal dependencies
	use nyx\core\interfaces;

	/**
	 * Process
	 *
	 * A Process is an object that can be run and stopped and provides information about the state it is currently in.
	 * This trait allows for the implementation of the core\interfaces\Process interface.
	 *
	 * @package     Nyx\Core\Traits
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/traits.html
	 */

	trait Process
	{
		/**
		 * @var array   An array containing status information about the Process.
		 */

		private $status =
		[
			'state'   => interfaces\Process::READY,
			'process' => [],
			'times'   =>
			[
				'start' => null,
				'stop'  => null
			]
		];

		/**
		 * @var float   The timeout time of this Process in seconds.
		 */

		private $timeout;

		/**
		 * @see nyx\core\interfaces\Process::start()
		 */

		public function start()
		{
			$this->status['state'] = interfaces\Process::STARTED;
			$this->status['times']['start'] = microtime(true);

			return $this;
		}

		/**
		 * @see nyx\core\interfaces\Process::stop()
		 */

		public function stop($timeout = 10)
		{
			$this->status['state'] = interfaces\Process::TERMINATED;
			$this->status['times']['stop'] = microtime(true);

			return $this;
		}

		/**
		 * @see nyx\core\interfaces\Process::pause()
		 */

		public function pause()
		{
			if($this->isRunning()) $this->status['state'] = interfaces\Process::PAUSED;

			return $this;
		}

		/**
		 * @see nyx\core\interfaces\Process::pause()
		 */

		public function resume()
		{
			if($this->isPaused()) $this->status['state'] = interfaces\Process::STARTED;

			return $this;
		}

		/**
		 * @see nyx\core\interfaces\Process::restart()
		 */

		public function restart()
		{
			// Stop the Process if it's already running to conform to the interface definition.
			if($this->isRunning()) $this->stop();

			$process = clone $this;
			$process->start();

			return $process;
		}

		/**
		 * @see nyx\core\interfaces\Process::isRunning()
		 */

		public function isRunning()
		{
			return interfaces\Process::STARTED === $this->getState();
		}

		/**
		 * @see nyx\core\interfaces\Process::isStarted()
		 */

		public function isStarted()
		{
			return interfaces\Process::READY !== $this->getState();
		}

		/**
		 * @see nyx\core\interfaces\Process::isPaused()
		 */

		public function isPaused()
		{
			return interfaces\Process::PAUSED !== $this->getState();
		}

		/**
		 * @see nyx\core\interfaces\Process::isTerminated()
		 */

		public function isTerminated()
		{
			return interfaces\Process::TERMINATED === $this->getState();
		}

		/**
		 * @see nyx\core\interfaces\Process::getState()
		 */

		public function getState()
		{
			return $this->status['state'];
		}

		/**
		 * @see nyx\core\interfaces\Process::getTimeout()
		 */

		public function getTimeout()
		{
			return $this->timeout;
		}

		/**
		 * @see nyx\core\interfaces\Process::setTimeout()
		 */

		public function setTimeout($seconds = null)
		{
			if(null !== $seconds)
			{
				$seconds = (float) $seconds;

				// Ensure the timeout is non-negative.
				if($seconds < 0)
				{
					throw new \InvalidArgumentException('The timeout value must be a positive integer/float number.');
				}

				$this->timeout = $seconds;
			}
			else
			{
				$this->timeout = null;
			}

			return $this;
		}

		/**
		 * @see nyx\core\interfaces\Process::checkTimeout()
		 */

		public function checkTimeout()
		{
			return ($this->timeout and $this->timeout < microtime(true) - $this->status['times']['start']);
		}

		/**
		 * @see nyx\core\interfaces\Process::enforceTimeout()
		 */

		public function enforceTimeout()
		{
			if($this->checkTimeout())
			{
				$this->stop(0);

				throw new \RuntimeException("The process timed-out.");
			}
		}

		/**
		 * @see nyx\core\interfaces\Process::setState()
		 */

		protected function setState($state)
		{
			$this->status['state'] = (int) $state;

			return $this;
		}

		/**
		 * Resets the status information of the Process exhibiting this trait (useful for cloning etc.).
		 */

		protected function resetStatus()
		{
			$this->status =
			[
				'state'   => interfaces\Process::READY,
				'process' => [],
				'times'   =>
				[
					'start' => null,
					'stop'  => null
				]
			];
		}

		/**
		 * Ensures the Process gets closed upon object deconstruction.
		 */

		public function __destruct()
		{
			$this->stop();
		}
	}