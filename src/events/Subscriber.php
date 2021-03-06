<?php namespace nyx\events;

	/**
	 * Event Subscriber
	 *
	 * Provides support methods for concrete, dedicated Subscribers. It allows you to set a default Emitter (and
	 * auto-subscribe to it) upon object construction and use the self::subscribe() and self::unsubsribe() methods
	 * directly on the Subscriber.
	 *
	 * Note: Incomplete implementation of the Subscriber interface. Requires an implementation of the
	 *       getSubscribedEvents() method.
	 *
	 * @package     Nyx\Events\Emission
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/events/emission.html
	 */

	abstract class Subscriber implements interfaces\Subscriber, interfaces\EmitterAware
	{
		/**
		 * The traits of a Subscriber instance.
		 */

		use traits\EmitterAware;

		/**
		 * Constructs a new Subscriber and subscribes automatically when the $subscribe argument is set to true.
		 *
		 * @param   interfaces\Emitter  $emitter    The default Emitter for this Subscriber.
		 * @param   bool                $subscribe  Whether to automatically subscribe to the events.
		 */

		public function __construct(interfaces\Emitter $emitter = null, $subscribe = false)
		{
			$this->emitter = $emitter;

			// Should we automatically subscribe?
			if($subscribe and $emitter) $this->subscribe();
		}

		/**
		 * Registers all Events this Subscriber responds to with the given Emitter.
		 *
		 * @param   interfaces\Emitter          $emitter    The Emitter to subscribe to.
		 * @throws  \InvalidArgumentException               When no Emitter is given or set within the Subscriber.
		 */

		public function subscribe(interfaces\Emitter $emitter = null)
		{
			// Make sure we have a Emitter to use. Attempt to use the default one if none is given to
			// to method. Throw an exception if neither is present.
			if(null === $emitter and null === $emitter = $this->emitter)
			{
				throw new \InvalidArgumentException('No Emitter given and the Subscriber has no default Emitter either.');
			}

			$emitter->subscribe($this);
		}

		/**
		 * Removes all Events this Subscriber is registered for from the given Emitter.
		 *
		 * @param   interfaces\Emitter          $emitter    The Emitter to unsubscribe from.
		 * @throws  \InvalidArgumentException               When no Emitter is given or set within the Subscriber.
		 */

		public function unsubscribe(interfaces\Emitter $emitter = null)
		{
			// Make sure we have a Emitter to use. Attempt to use the default one if none is given to the method.
			// Throw an exception if neither is present.
			if(null === $emitter and null === $emitter = $this->emitter)
			{
				throw new \InvalidArgumentException('No Emitter given and the Subscriber has no default Emitter either.');
			}

			$emitter->unsubscribe($this);
		}
	}