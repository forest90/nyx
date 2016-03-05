<?php namespace nyx\events\interfaces;

	// Internal dependencies
	use nyx\events;

	/**
	 * Event Emitter Interface
	 *
	 * Event synchronization point. Registers/removes listeners for events and triggers events. Supports registering
	 * listeners by priority.
	 *
	 * @package     Nyx\Events\Emission
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/events/emission.html
	 */

	interface Emitter
	{
		/**
		 * Magic constant used by {@see self::emit()}. See the docblock for the $event argument therein.
		 */

		const CREATE_EMPTY_EVENT = '__emitter_create_empty_event';

		/**
		 * Triggers the listeners for a given event name.
		 *
		 * You may pass any additional arguments you wish to this method. They will be passed along to the listeners,
		 * with the propagated Event instance always being the first argument (the $name will not be passed directly
		 * as it will be available through the Event). However, be aware of what is passed as reference and what is not,
		 * if you wish to modify the data within listeners and have it available with those changes within further
		 * listeners.
		 *
		 * @param   string|events\Event $name       The name of the event to emit. You may also pass an Event object that
		 *                                          has its trigger name set {@see Event::setName()}.
		 * @param   events\Event|mixed  $event      The event to pass to the event handlers/listeners. If not supplied or
		 *                                          not an instance of events\Event an empty Event instance will be
		 *                                          created and all arguments after $name will be passed to the
		 *                                          listeners. If left as the CREATE_EMPTY_EVENT magic constant, this
		 *                                          argument will be completely ignored (ie. not passed to the listeners)
		 *                                          but will still trigger the creation of a base Event.
		 * @return  events\Event                    The Event after it has run through all registered listeners.
		 * @throws  \LogicException                 When only an Event object is passed but it does not have a valid
		 *                                          trigger name.
		 */

		public function emit($name, $event = self::CREATE_EMPTY_EVENT /* ...$data */);

		/**
		 * Adds an event listener that listens to the specified event.
		 *
		 * @param   string      $name           The event to listen for.
		 * @param   callable    $listener       The listener to add.
		 * @param   integer     $priority       The higher this value, the earlier an event listener will be triggered
		 *                                      in the chain.
		 * @return  $this
		 */

		public function on($name, callable $listener, $priority = 0);

		/**
		 * Adds an event listener that listens to the specified event but only gets fired once and then removed from
		 * the stack.
		 *
		 * @param   string      $name           The event to listen for.
		 * @param   callable    $listener       The listener to add.
		 * @param   integer     $priority       The higher this value, the earlier an event listener will be triggered
		 *                                      in the chain.
		 * @return  $this
		 */

		public function once($name, callable $listener, $priority = 0);

		/**
		 * Removes the specified listener from the stack of a given event.
		 *
		 * @param   string|array    $name       The event to remove a listener from.
		 * @param   callable        $listener   The listener to remove.
		 * @return  $this
		 */

		public function off($name, callable $listener);

		/**
		 * Adds an Event Subscriber. The Subscriber is queried for a list of events he is interested in and afterwards
		 * registered as a listener for them.
		 *
		 * @param   Subscriber  $subscriber
		 * @return  $this
		 */

		public function subscribe(Subscriber $subscriber);

		/**
		 * Removes an Event Subscriber.
		 *
		 * @param   Subscriber  $subscriber
		 * @return  $this
		 */

		public function unsubscribe(Subscriber $subscriber);

		/**
		 * Returns an array containing the priority-sorted listeners for the given event or for all events if no trigger
		 * name is given.
		 *
		 * @param   string  $name   The trigger name of the event.
		 * @return  array           The event listeners for the specified event.
		 */

		public function getListeners($name = null);

		/**
		 * Checks whether the given trigger name has any listeners registered or when no trigger name is given whether
		 * any listeners at all are registered.
		 *
		 * @param   string  $name   The trigger name of the event.
		 * @return  bool
		 */

		public function hasListeners($name = null);

		/**
		 * Returns the count of the listeners for the given trigger name or the count of all registered listeners if
		 * no trigger name is given.
		 *
		 * @param   string  $name   The trigger name of the event.
		 * @return  int
		 */

		public function countListeners($name = null);

		/**
		 * Removes all registered listeners or, when a trigger name is given, all listeners for the given trigger name.
		 *
		 * @param   string  $name   The trigger name of the event.
		 * @return  $this
		 */

		public function removeListeners($name = null);
	}