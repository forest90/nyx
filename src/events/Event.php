<?php namespace nyx\events;

	// External dependencies
	use nyx\core;

	/**
	 * Event
	 *
	 * Base class for concrete classes acting as events and containing event data. Not declared abstract because the
	 * Emitter instantiates this class each time an event is triggered without passing a concrete Event instance
	 * to the emit() method of the Emitter.
	 *
	 * An Event is considered emitted when it has a set Emitter instance.
	 *
	 * @package     Nyx\Events\Emission
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/events/emission.html
	 */

	class Event implements interfaces\EmitterAware
	{
		/**
		 * Extending the core 'Named' trait to ensure trigger names are non-empty strings.
		 *
		 * Please note that event names are stored as array keys in the Emitter, so using simple integers is more
		 * likely to cause collisions in complex applications. If you want to allow them nonetheless, override the
		 * Event's constructor and the validateName() method of the Named trait.
		 */

		use core\traits\Named;

		/**
		 * Reuse some code to be able to figure out which Emitter emitted which Event.
		 */

		use traits\EmitterAware;

		/**
		 * @var bool    True if no further event listeners should be triggered.
		 */

		private $stopped = false;

		/**
		 * Constructs an Event.
		 *
		 * Allows a trigger name to be set directly in the Event during construction, therefore making it possible
		 * to pass this Event directly to the Emitter::emit() method to invoke all listeners of the given trigger
		 * name.
		 *
		 * When an Emitter is given during construction, the Event will be emitted straight away. When doing this,
		 * a valid trigger name must be provided for the Event instance at some point as otherwise the Emitter
		 * will throw an exception.
		 *
		 * @param   string              $name       The trigger name of the Event.
		 * @param   interfaces\Emitter  $emitter    The Event Emitter to use to emit the Event *straight away*.
		 */

		public function __construct($name = null, interfaces\Emitter $emitter = null)
		{
			$name    and $this->setName($name);
			$emitter and $this->emit($emitter);
		}

		/**
		 * Returns true if no further Event listeners should be invoked for this Event, false otherwise.
		 *
		 * @return  bool
		 */

		public function stopped()
		{
			return $this->stopped;
		}

		/**
		 * Stops the propagation of the Event to further listeners.
		 *
		 * @return  $this
		 */

		public function stop()
		{
			$this->stopped = true;

			return $this;
		}

		/**
		 * {@inheritDoc}
		 *
		 * Overridden to ensure the Emitter is immutable once set, ie. the Event is considered emitted.
		 *
		 * @throws  \LogicException  When attempting to set an Emitter after it is already set.
		 */

		public function setEmitter(interfaces\Emitter $emitter)
		{
			if(null !== $this->emitter)
			{
				throw new \LogicException('The Emitter of an Event can not be changed after emission had started.');
			}

			$this->emitter = $emitter;
		}

		/**
		 * Emits this Event, either using the already set Event Emitter, if any, or the given Emitter. Note: Passing
		 * an Emitter to this method will overwrite any Emitter currently set in the Event. This is considered merely
		 * an utility method and new Event instances should be created for each emission in the first place.
		 *
		 * Emitting Events using this method expects that the Event has a name already set, otherwise the Emitter
		 * will throw an exception. While it would be easy to allow for the setting of the name, this is a design
		 * decision.
		 *
		 * @param   interfaces\Emitter  $emitter    The Event Emitter which should emit this Event.
		 * @throws  \LogicException                 When no Event Emitter is available to be used.
		 * @return  $this
		 */

		public function emit(interfaces\Emitter $emitter = null)
		{
			if(null === $emitter and null === $emitter = $this->emitter)
			{
				throw new \LogicException("No Emitter given and the Event has no Emitter set.");
			}

			// No need to set the Emitter in this Event within this method as the call below will do that for us.
			return $emitter->emit($this);
		}
	}
