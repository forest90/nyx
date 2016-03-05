<?php namespace nyx\events\interfaces\loop;

	// Internal dependencies
	use nyx\events\interfaces;

	/**
	 * Event Loop Timer Interface
	 *
	 * @package     Nyx\Events\Loop
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/events/loop.html
	 */

	interface Timer
	{
		/**
		 * Interval constant.
		 */

		const QUANTUM_INTERVAL = 1000000;

		/**
		 * Minimal time resolution constant.
		 */

		const MIN_RESOLUTION = 0.001;

		/**
		 * Returns the Event Loop instance the Timer belongs to.
		 *
		 * @return  interfaces\Loop
		 */

	    public function getLoop();

		/**
		 * Returns the scheduling delay/interval of this Timer in seconds.
		 *
		 * @return  float
		 */

	    public function getDelay();

		/**
		 * Returns the arbitrary data set within the Timer.
		 *
		 * @return  mixed
		 */

		public function getData();

		/**
		 * Sets arbitrary data within the Timer.
		 *
		 * @param   mixed   $data   The data to set.
		 * @return  $this
		 */

		public function setData($data);

		/**
		 * Checks whether the delay of this Timer should be treated as an interval instead.
		 *
		 * @return  bool    True when the delay should be treated as an interval, false otherwise.
		 */

	    public function isPeriodic();

		/**
		 * Checks whether the Timer is still active, ie. whether it is known to the Event Loop it belongs to and
		 * still scheduled to be run.
		 *
		 * @return  bool    True when the Timer is active, false otherwise.
		 */

	    public function isActive();

		/**
		 * Cancels the Timer, ie. removes it from the Event Loop it belongs to.
		 *
		 * @return  $this
		 */

	    public function cancel();

		/**
		 * Executes the callback of the Timer.
		 */

		public function call();
	}
