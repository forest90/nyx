<?php namespace nyx\events;

	// External dependencies
	use nyx\core;

	/**
	 * Abstract Event Loop
	 *
	 * Acts as a base class for concrete Event Loop drivers and contains a factory method {@see sełf::create()} for
	 * constructing the most effective driver available on the platform.
	 *
	 * @package     Nyx\Events\Loop
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/events/loop.html
	 * @todo        Loop timeouts (enforceTimeout() from the Process trait).
	 * @todo        LibEv driver.
	 */

	abstract class Loop implements interfaces\Loop
	{
		/**
		 * The traits of a Loop instance.
		 */

		use core\traits\Process;

		/**
		 * @var array   The registered R/W streams.
		 */

		protected $streams;

		/**
		 * @var \SplObjectStorage   The timers currently in use by the Loop.
		 */

		protected $timers;

		/**
		 * Factory method responsible for creating a new Event Loop with one of the drivers available within this
		 * environment.
		 *
		 * @return  static  An Event Loop driver instance.
		 */

		public static function create()
		{
			// Make use of the LibEvent PHP extension if it's available.
			if(function_exists('event_base_new')) return new loop\drivers\LibEvent;

			// Otherwise fall back to stream_select().
			return new loop\drivers\StreamSelect;
		}

		/**
		 * Constructs a new Event Loop.
		 */

		public function __construct()
		{
			$this->timers  = new \SplObjectStorage;
			$this->streams = [];
		}

		/**
		 * {@inheritDoc}
		 */

		public function onReadable($stream, callable $listener)
		{
			return $this->registerStream($stream, $listener, self::READ);
		}

		/**
		 * {@inheritDoc}
		 */

		public function onWritable($stream, callable $listener)
		{
			return $this->registerStream($stream, $listener, self::WRITE);
		}

		/**
		 * {@inheritDoc}
		 */

		public function offReadable($stream)
		{
			return $this->removeStream($stream, self::READ);
		}

		/**
		 * {@inheritDoc}
		 */

		public function offWritable($stream)
		{
			return $this->removeStream($stream, self::WRITE);
		}

		/**
		 * {@inheritDoc}
		 */

		public function off($stream)
		{
			$this->offReadable($stream);
			$this->offWritable($stream);

			return $this;
		}

		/**
		 * {@inheritDoc}
		 */

		public function at($time, callable $callback)
		{
			if(false === ($at = @strtotime($time)) or $at <= $now = time())
			{
				throw new \InvalidArgumentException("Invalid time string given. Either not in the future or not parsable by strtotime().");
			}

			return $this->in($at - $now, $callback);
		}

		/**
		 * {@inheritDoc}
		 */

		public function in($delay, callable $callback)
		{
			return $this->schedule($delay, $callback);
		}

		/**
		 * {@inheritDoc}
		 */

		public function repeat($interval, callable $callback)
		{
			return $this->schedule($interval, $callback, true);
		}

		/**
		 * {@inheritDoc}
		 */

		public function immediately(callable $callback)
		{
			return $this->schedule(0, $callback, false);
		}

		/**
		 * {@inheritDoc}
		 */

		public function isTimerActive(interfaces\loop\Timer $timer)
		{
			return $this->timers->contains($timer);
		}

		/**
		 * Performs the actual adding of a stream of a given type and its listener.
		 *
		 * @param   int|resource    $stream     The stream identifier or the stream resource to register.
		 * @param   callable        $listener   The listener for this stream.
		 * @param   int             $type       The type of the stream (one of the type interface constants).
		 * @return  $this
		 */

		abstract protected function registerStream($stream, callable $listener, $type);

		/**
		 * Performs the actual removal of a stream of a given type and its listener.
		 *
		 * @param   int         $stream     The stream identifier.
		 * @param   int         $type       The type of the stream (one of the type interface constants).
		 * @return  $this
		 */

		abstract protected function removeStream($stream, $type);

		/**
		 * Performs the actual scheduling of a callback by creating a new Timer and registering it.
		 *
		 * @param   float       $delay      The scheduling delay/interval of the Timer in seconds.
		 * @param   callable    $callback   The callback to be called.
		 * @param   bool        $isPeriodic Whether the $delay parameter should be treated as an interval.
		 * @return  loop\Timer              The resulting Timer instance.
		 */

		abstract protected function schedule($delay, callable $callback, $isPeriodic = false);
	}
