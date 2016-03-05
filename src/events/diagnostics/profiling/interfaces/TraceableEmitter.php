<?php namespace nyx\events\diagnostics\profiling\interfaces;

	/**
	 * Traceable Event Emitter Interface
	 *
	 * An event Emitter which keeps track of the events it emits and the listeners it calls.
	 *
	 * @package     Nyx\Events\Diagnostics\Profiling
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/events/emitter.html
	 */

	interface TraceableEmitter
	{
		/**
		 * Returns the listeners that have been called.
		 *
		 * @return  array   An array of listeners that have been called.
		 */

		public function getCalledListeners();

		/**
		 * Returns the listeners that have not been called.
		 *
		 * @return  array   An array of listeners that have not been called.
		 */

		public function getNotCalledListeners();

		/**
		 * Returns the events that have been emitted.
		 *
		 * @return  array   An array of events that have been emitted in the following format:
		 *                      $eventName => ['time' => microtime(true), 'context' => []]
		 *
		 *                  The context (when emitted by the included Emitter) will contain the Event instance fired
		 *                  at its first key and n other keys depending on what was passed to the Emitter.
		 */

		public function getEmittedEvents();
	}