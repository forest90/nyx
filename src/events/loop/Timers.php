<?php namespace nyx\events\loop;

	// Internal dependencies
	use nyx\events\interfaces;

	/**
	 * Event Loop Timers Collection
	 *
	 * @package     Nyx\Events\Loop
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/events/loop.html
	 */

	class Timers
	{
		/**
		 * @var int     The time in microseconds of either the last performed tick or when no tick has been performed
		 *              yet, the time when the first Timer got added.
		 */

		private $time;

		/**
		 * @var \SplObjectStorage   The Timers currently contained in this collection.
		 */

		private $elements;

		/**
		 * @var \SplPriorityQueue   The priority queue for the contained Timers.
		 */

		private $queue;

		/**
		 * Constructs a new Timers collection instance.
		 */

		public function __construct()
		{
			$this->elements = new \SplObjectStorage();
			$this->queue    = new \SplPriorityQueue();
		}

		/**
		 * Adds a Timer to this collection.
		 *
		 * @param   interfaces\loop\Timer   $timer      The Timer instance to add.
		 * @return  $this
		 */

		public function add(interfaces\loop\Timer $timer)
		{
			$at = $timer->getDelay() + $this->getTime();

			$this->elements->attach($timer, $at);
			$this->queue->insert($timer, -$at);

			return $this;
		}

		/**
		 * Removes the given Timer from this collection.
		 *
		 * @param   interfaces\loop\Timer   $timer      The Timer instance to remove.
		 * @return  $this
		 */

		public function remove(interfaces\loop\Timer $timer)
		{
			$this->elements->detach($timer);

			return $this;
		}

		/**
		 * Checks whether the given Timer is contained within this collection.
		 *
		 * @param   interfaces\loop\Timer   $timer      The Timer instance to check for.
		 * @return  bool                                True when the Timer is contained within this collection, false
		 *                                              otherwise.
		 */

		public function contains(interfaces\loop\Timer $timer)
		{
			return $this->elements->contains($timer);
		}

		/**
		 * Returns the topmost Timer scheduled to be run.
		 *
		 * @return  interfaces\loop\Timer   The next scheduled Timer or null if none are scheduled.
		 */

		public function getFirst()
		{
			if($this->queue->isEmpty()) return null;

			return $this->elements[$this->queue->top()];
		}

		/**
		 * Checks whether the collection contains any Timers.
		 *
		 * @return  bool    True when the collection is empty, false otherwise.
		 */

		public function isEmpty()
		{
			return empty($this->elements);
		}

		/**
		 * Performs a tick, ie. loops through all the scheduled Timers and triggers their callbacks.
		 *
		 * @return  $this
		 */

		public function tick()
		{
			$time      = $this->updateTime();
			$timers    = $this->elements;
			$scheduler = $this->queue;

			while(!$scheduler->isEmpty())
			{
				// Grab the next scheduled Timer.
				$timer = $scheduler->top();

				// If that Timer has been meanwhile removed from the Collection, we are going to remove it altogether
				// And move on to the next scheduled Timer.
				if(!isset($timers[$timer]))
				{
					$scheduler->extract();
					$timers->detach($timer);

					continue;
				}

				// Make sure the time the next Timer is scheduled does not exceed the current time.
				if($timers[$timer] >= $time) break;

				$scheduler->extract();

				// Call the callback of the Timer.
				$timer->call();

				// If we're dealing with a periodic Timer and it's still set at this point, we need to reschedule
				// it.
				if($timer->isPeriodic() and isset($timers[$timer]))
				{
					$timers[$timer] = $at = $timer->getDelay() + $time;
					$scheduler->insert($timer, -$at);
				}
				// Otherwise the Timer was meant to be run only once so we can safely remove it now.
				else
				{
					$timers->detach($timer);
				}
			}

			return $this;
		}

		/**
		 * Returns the internal timestamp of this Timers collection. If none is available yet, the current time will
		 * be set and returned.
		 *
		 * @return  int     The current time in microseconds.
		 */

		public function getTime()
		{
			return $this->time ?: $this->updateTime();
		}

		/**
		 * Updates the internal timestamp of this Timers collection.
		 *
		 * @return  int     The current time in microseconds that was also set internally.
		 */

		protected function updateTime()
		{
			return $this->time = microtime(true);
		}
	}