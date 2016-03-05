<?php namespace nyx\events\interfaces;

	// External dependencies
	use nyx\core;

	/**
	 * Event Loop Interface
	 *
	 * @package     Nyx\Events\Loop
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/events/loop.html
	 * @todo        Move the type constants to the Connect component when it's ready.
	 */

	interface Loop extends core\interfaces\Process
	{
		/**
		 * Type constants.
		 */

		const READ  = 1;
		const WRITE = 2;

		/**
		 * Performs a non-blocking tick.
		 */

		public function tick();

		/**
		 * Registers a stream with a listener to be called whenever the stream becomes readable.
		 *
		 * @param   int|resource    $stream     The stream identifier or the stream resource to register.
		 * @param   callable        $listener   The listener for this stream.
		 * @return  $this
		 */

	    public function onReadable($stream, callable $listener);

		/**
		 * Registers a stream with a listener to be called whenever the stream becomes writable.
		 *
		 * @param   int|resource    $stream     The stream identifier or the stream resource to register.
		 * @param   callable        $listener   The listener for this stream.
		 * @return  $this
		 */

	    public function onWritable($stream, callable $listener);

		/**
		 * Removes a stream's onReadable listener.
		 *
		 * @param   int|resource    $stream     The stream identifier or the stream resource of which the listener
		 *                                      should be removed.
		 * @return  $this
		 */

	    public function offReadable($stream);

		/**
		 * Removes a stream's onWritable listener.
		 *
		 * @param   int|resource    $stream     The stream identifier or the stream resource of which the listener
		 *                                      should be removed.
		 * @return  $this
		 */

	    public function offWritable($stream);

		/**
		 * Completely removes a stream from being tracked, ie. removes both its onReadable and its onWritable listeners.
		 *
		 * @param   int|resource    $stream     The stream identifier or the stream resource to remove.
		 * @return  $this
		 */

	    public function off($stream);

		/**
		 * Schedules a callback to be called once at the given time.
		 *
		 * @param   string      $time       A string containing a future time in a format understood by {@see strtotime()}.
		 * @param   callable    $callback   The callback to be fired.
		 * @return  loop\Timer              The resulting Timer instance.
		 */

		public function at($time, callable $callback);

		/**
		 * Schedules a callback to be called once in $delay seconds from the time it gets set.
		 *
		 * @param   float       $delay      The number of seconds before the callback should get called.
		 * @param   callable    $callback   The callback to be called.
		 * @return  loop\Timer              The resulting Timer instance.
		 */

		public function in($delay, callable $callback);

		/**
		 * Schedules a callback to be repeatedly called every $interval seconds.
		 *
		 * @param   float       $interval   The number of seconds that should pass before the first call to the callback
		 *                                  and then between each subsequent call to the callback.
		 * @param   callable    $callback   The callback to be called.
		 * @return  loop\Timer              The resulting Timer instance.
		 */

		public function repeat($interval, callable $callback);

		/**
		 * Schedules a callback to be called immediately upon the next tick.
		 *
		 * @param   callable    $callback   The callback to be called.
		 * @return  loop\Timer              The resulting Timer instance.
		 */

		public function immediately(callable $callback);

		/**
		 * Cancels the given Timer, ie. removes it from this Event Loop.
		 *
		 * @param   loop\Timer  $timer  The Timer to cancel.
		 * @return  $this
		 */

	    public function cancelTimer(loop\Timer $timer);

		/**
		 * Checks whether the given Timer is still active, ie. whether it is known to this Event Loop and still
		 * scheduled to be run.
		 *
		 * @param   loop\Timer  $timer  The Timer to check.
		 * @return  bool                True when the Timer is active, false otherwise.
		 */

	    public function isTimerActive(loop\Timer $timer);
	}