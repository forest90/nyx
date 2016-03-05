<?php namespace nyx\events\loop;

	// Internal dependencies
	use nyx\events\interfaces;

	/**
	 * Event Loop Timer
	 *
	 * Timers are immutable except for their $data property, which can be utilized to pass arbitrary data to the
	 * callback of a Timer as the only argument the callback will receive is the Timer instance itself.
	 *
	 * @package     Nyx\Events\Loop
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/events/loop.html
	 */

	class Timer implements interfaces\loop\Timer
	{
		/**
		 * @var interfaces\Loop     The Event Loop the Timer belongs to.
		 */

		private $loop;

		/**
		 * @var float       The scheduling delay/interval of this Timer in seconds.
		 */

		private $delay;

		/**
		 * @var callable    The callback of this Timer.
		 */

		private $callback;

		/**
		 * @var bool        Whether the $delay property of this Timer should be treated as an interval.
		 */

		private $isPeriodic;

		/**
		 * @var mixed       Arbitrary data used as a means of passing it to the callback as the only argument the
		 *                  callback will receive is the Timer instance itself.
		 */

		private $data;

		/**
		 * Constructs a Timer instance.
		 *
		 * @param   interfaces\Loop     $loop       The Event Loop the Timer shall belong to.
		 * @param   float               $delay      The scheduling delay/interval of this Timer in seconds.
		 * @param   callable            $callback   The callback to be called.
		 * @param   bool                $isPeriodic Whether the $delay parameter should be treated as an interval.
		 * @param   mixed               $data       Arbitrary data used as a means of passing it to the callback as the
		 *                                          only argument the callback will receive is the Timer instance itself.
		 * @throws  \InvalidArgumentException       When the delay/interval is smaller than a millisecond.
		 */

		public function __construct(interfaces\Loop $loop, $delay, callable $callback, $isPeriodic = false, $data = null)
		{
			if(self::MIN_RESOLUTION > $this->delay = (float) $delay)
			{
				throw new \InvalidArgumentException("Event Loop Timers do not support sub-millisecond timeouts.");
			}

			$this->loop       = $loop;
			$this->callback   = $callback;
			$this->isPeriodic = (bool) $isPeriodic;
			$this->data       = $data;
		}

		/**
		 * {@inheritDoc}
		 */

		public function getLoop()
		{
			return $this->loop;
		}

		/**
		 * {@inheritDoc}
		 */

		public function getDelay()
		{
			return $this->delay;
		}

		/**
		 * {@inheritDoc}
		 */

		public function getData()
		{
			return $this->data;
		}

		/**
		 * {@inheritDoc}
		 */

		public function setData($data)
		{
			$this->data = $data;

			return $this;
		}

		/**
		 * {@inheritDoc}
		 */

		public function isPeriodic()
		{
			return $this->isPeriodic;
		}

		/**
		 * {@inheritDoc}
		 */

		public function isActive()
		{
			return $this->loop->isTimerActive($this);
		}

		/**
		 * {@inheritDoc}
		 */

		public function cancel()
		{
			return $this->loop->cancelTimer($this);
		}

		/**
		 * {@inheritDoc}
		 */

		public function call()
		{
			call_user_func($this->callback, $this);
		}
	}