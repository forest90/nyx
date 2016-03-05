<?php namespace nyx\events\loop\drivers;

	// External dependencies
	use nyx\system;

	// Internal dependencies
	use nyx\events\interfaces;
	use nyx\events\loop;
	use nyx\events;

	/**
	 * StreamSelect Event Loop Driver
	 *
	 * @package     Nyx\Events\Loop
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/events/loop.html
	 */

	class StreamSelect extends events\Loop
	{
		/**
		 * @var array   The listeners registered to be called back whenever the respective stream becomes readable
		 *              or writable.
		 */

		private $listeners;

		/**
		 * Constructs a StreamSelect Event Loop instance.
		 */

		public function __construct()
		{
			$this->timers    = new loop\Timers();
			$this->streams   = [self::READ => [], self::WRITE => []];
			$this->listeners = [self::READ => [], self::WRITE => []];
		}

		/**
		 * {@inheritDoc}
		 */

		public function start()
		{
			parent::start();

			while($this->step());
		}

		/**
		 * {@inheritDoc}
		 */

		public function tick()
		{
			return $this->step(false);
		}

		/**
		 * {@inheritDoc}
		 */

		public function cancelTimer(interfaces\loop\Timer $timer)
		{
			$this->timers->remove($timer);

			return $this;
		}

		/**
		 * Returns the time in milliseconds which are left until the next Timer is scheduled to be run.
		 *
		 * @return  int     The time in milliseconds until the next Timer is scheduled to be run.
		 */

		public function getNextEventDistance()
		{
			if(null === $event = $this->timers->getFirst()) return interfaces\loop\Timer::QUANTUM_INTERVAL;

			if($event > $currentTime = microtime(true))
			{
				return ($event - $currentTime) * interfaces\loop\Timer::QUANTUM_INTERVAL;
			}

			return 0;
		}

		/**
		 * Performs an actual tick.
		 *
		 * @param   bool    $block  Whether the loop should be blocking or not.
		 * @return  bool            Whether any more timers are still scheduled to run after this tick assuming the
		 *                          loop was start()'ed. When it was not, ie. it's being used in non-blocking mode,
		 *                          this will always be false.
		 */

		protected function step($block = true)
		{
			$this->timers->tick();
			$this->run($block);

			return $this->isRunning();
		}

		/**
		 * Runs stream_select() and invokes the respective R/W listeners once data becomes available in the given
		 * streams.
		 *
		 * @param   bool    $block  Whether the method should block until all events are done when the streams are not
		 *                          set anymore.
		 */

		protected function run($block)
		{
			$read   = $this->streams[self::READ]  ?: null;
			$write  = $this->streams[self::WRITE] ?: null;
			$except = null;

			if(!$read and !$write)
			{
				if($block)
				{
					if($this->timers->isEmpty())
					{
						$this->stop();
					}
					// stream_select() fails when there are no streams registered for R/W events so timeouts need to be
					// emulated.
					else
					{
						usleep($this->getNextEventDistance());
					}
				}

				return;
			}

			// Check if stream_select() returned false on an error. See if it's an interrupted system call and try again
			// if that's the case.
			if(false === $n = @stream_select($read, $write, $except, 0, $block ? $this->getNextEventDistance() : 0))
			{
				// We're going to ignore interrupted system calls and try again.
				if(system\Call::hasBeenInterrupted()) $this->run($block);

				return;
			}

			// If none of the streams has changed, let's return right away.
			if(0 === $n) return;

			// Process all onReadable listeners if appropriate.
			if($read)
			{
				foreach($read as $stream)
				{
					if(isset($this->listeners[self::READ][(int) $stream]))
					{
						call_user_func($this->listeners[self::READ][(int) $stream], $stream, $this);
					}
				}
			}

			// Process all onWritable listeners if appropriate.
			if($write)
			{
				foreach($write as $stream)
				{
					if(isset($this->listeners[self::WRITE][(int) $stream]))
					{
						call_user_func($this->listeners[self::WRITE][(int) $stream], $stream, $this);
					}
				}
			}
		}

		/**
		 * {@inheritDoc}
		 */

		protected function registerStream($stream, callable $listener, $type)
		{
			$id = (int) $stream;

			if(!isset($this->streams[$type][$id]))
			{
				$this->streams[$type][$id]   = $stream;
				$this->listeners[$type][$id] = $listener;
			}

			return $this;
		}

		/**
		 * {@inheritDoc}
		 */

		protected function removeStream($stream, $type)
		{
			$id = (int) $stream;

			unset($this->streams[$type][$id], $this->listeners[$type][$id]);

			return $this;
		}

		/**
		 * {@inheritDoc}
		 */

		protected function schedule($delay, callable $callback, $isPeriodic = false)
		{
			$timer = new loop\Timer($this, $delay, $callback, $isPeriodic);

			$this->timers->add($timer);

			return $timer;
		}
	}