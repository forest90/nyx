<?php namespace nyx\events\loop\drivers;

	// Internal dependencies
	use nyx\events\interfaces;
	use nyx\events\loop;
	use nyx\events;

	/**
	 * LibEvent Event Loop Driver
	 *
	 * @package     Nyx\Events\Loop
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/events/loop.html
	 * @see         http://php.net/manual/en/book.libevent.php
	 */

	class LibEvent extends events\Loop
	{
		/**
		 * Streams array structure constants used to identify the parts of the streams holder.
		 */

		const LISTENERS = 0;
		const EVENTS    = 1;
		const FLAGS     = 2;

		/**
		 * @var resource    Base event resource as used by the LibEvent extension.
		 */

		private $base;

		/**
		 * {@inheritDoc}
		 */

		public function __construct()
		{
			$this->base = event_base_new();

			// Run the base construction (timers storage etc.).
			parent::__construct();
		}

		/**
		 * {@inheritDoc}
		 */

		public function start()
		{
			parent::start();

			event_base_loop($this->base);
		}

		/**
		 * {@inheritDoc}
		 */

		public function tick()
		{
			event_base_loop($this->base, EVLOOP_ONCE | EVLOOP_NONBLOCK);
		}

		/**
		 * {@inheritDoc}
		 */

		public function stop($timeout = 10)
		{
			parent::stop();

			event_base_loopexit($this->base);
		}

		/**
		 * {@inheritDoc}
		 *
		 * Overridden since we need to remove the flags and the events from LibEvent on top of clearing our own
		 * collections.
		 */

		public function off($stream)
		{
			$id = (int) $stream;

			if(isset($this->streams[$id][self::EVENTS]))
			{
				$event = $this->streams[$id][self::EVENTS];

				unset($this->streams[$id]);

				event_del($event);
				event_free($event);
			}

			return $this;
		}

		/**
		 * {@inheritDoc}
		 */

		public function cancelTimer(interfaces\loop\Timer $timer)
		{
			if(isset($this->timers[$timer]))
			{
				$event = $this->timers[$timer];

				event_del($event);
				event_free($event);

				$this->timers->detach($timer);
			}

			return $this;
		}

		/**
		 * Handles callbacks for stream R/W events.
		 *
		 * @param   resource    $stream     The stream the event was triggered for.
		 * @param   int         $flags      The flags the event was triggered with.
		 * @param   LibEvent    $loop       The Event Loop which initiated the event.
		 * @throws  \Exception              The exception which occurred when calling the user-defined callbacks,
		 *                                  if applicable.
		 */

		public function callback($stream, $flags, static $loop)
		{
			$id = (int) $stream;

			try
			{
				if(($flags & EV_READ) === EV_READ and isset($this->streams[$id][self::LISTENERS][EV_READ]))
				{
					call_user_func($this->streams[$id][self::LISTENERS][EV_READ], $stream, $loop);
				}

				if(($flags & EV_WRITE) === EV_WRITE and isset($this->streams[$id][self::LISTENERS][EV_WRITE]))
				{
					call_user_func($this->streams[$id][self::LISTENERS][EV_WRITE], $stream, $loop);
				}
			}
			// Stop the loop and re-throw the exception.
			catch(\Exception $e)
			{
				/* @var events\Loop $loop */
				$loop->stop();

				throw $e;
			}
		}

		/**
		 * {@inheritDoc}
		 */

		protected function registerStream($stream, callable $listener, $type)
		{
			$id   = (int) $stream;
			$type = $type === self::READ ? EV_READ : EV_WRITE;

			if($exists = isset($this->streams[$id]))
			{
				// If the flag is already set, return right away.
				if(($this->streams[$id][self::FLAGS] & $type) === $type) return;

				// Otherwise remove the event as we will need to add it again with the new flag.
				$event = $this->streams[$id][self::EVENTS];
				event_del($event);
			}
			else
			{
				// Need to create an empty array for the R/W listeners.
				$this->streams[$id] = [self::LISTENERS => []];

				$event = event_new();
			}

			// Update the flags bitmask if it was already set.
			$flags = isset($this->streams[$id][self::FLAGS]) ? $this->streams[$id][self::FLAGS] | $type : $type;

			event_set($event, $stream, $flags | EV_PERSIST, [$this, 'callback'], $this);

			// Set the base only if $event has been newly created or be ready for segfaults.
			if(!$exists) event_base_set($event, $this->base);

			event_add($event);

			$this->streams[$id][self::LISTENERS][$type] = $listener;
			$this->streams[$id][self::EVENTS] = $event;
			$this->streams[$id][self::FLAGS]  = $flags;
		}

		/**
		 * {@inheritDoc}
		 */

		protected function removeStream($stream, $type)
		{
			$id   = (int) $stream;
			$type = $type === self::READ ? EV_READ : EV_WRITE;

			if(isset($this->streams[$id][self::EVENTS]))
			{
				// Remove the given type from the stream's flags.
				$flags = $this->streams[$id][self::FLAGS] & ~$type;

				// Remove and return if there are no more flags set for the given stream's events.
				if($flags === 0)
				{
					$this->off($stream); return;
				}

				$event = $this->streams[$id][self::EVENTS];

				// First remove the event...
				event_del($event);
				event_free($event);

				unset($this->streams[$id][self::LISTENERS][$type]);

				// ... to create a new one with updated flags.
				$event = event_new();

				event_set($event, $stream, $flags | EV_PERSIST, [$this, 'callback'], $this);
				event_base_set($event, $this->base);
				event_add($event);

				$this->streams[$id][self::EVENTS] = $event;
				$this->streams[$id][self::FLAGS] = $flags;
			}
		}

		/**
		 * {@inheritDoc}
		 */

		protected function schedule($delay, callable $callback, $isPeriodic = false)
		{
			// Create the actual resource.
			$event = event_new();

			// Create and attach the Timer to our storage.
			$this->timers->attach($timer = new loop\Timer($this, $delay, $callback, $isPeriodic), $event);

			// Prepare an anonymous callback to pass to the Libevent extension.
			$callback = function () use ($timer, &$callback)
			{
				if(isset($this->timers[$timer]))
				{
					$timer->call();

					if($timer->isPeriodic() and isset($this->timers[$timer]))
					{
						event_add($this->timers[$timer], $timer->getDelay() * interfaces\loop\Timer::QUANTUM_INTERVAL);
					}
					else
					{
						$timer->cancel();
					}
				}
			};

			event_timer_set($event, $callback);
			event_base_set($event, $this->base);
			event_add($event, $delay * interfaces\loop\Timer::QUANTUM_INTERVAL);

			return $timer;
		}
	}