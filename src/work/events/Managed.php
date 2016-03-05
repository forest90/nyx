<?php namespace nyx\work\events;

	// External dependencies
	use nyx\events;

	// Internal dependencies
	use nyx\work\interfaces;

	/**
	 * Managed Event
	 *
	 * Base event for all work events that are controlled by a Manager instance.
	 *
	 * @package     Nyx\Work\Events
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/work/events.html
	 */

	class Managed extends events\Event
	{
		/**
		 * @var interfaces\Manager  The Manager instance responsible for emitting this Event.
		 */

		private $manager;

		/**
		 * {@inheritDoc}
		 *
		 * @param   interfaces\Manager  $manager    The Manager instance responsible for emitting this Event.
		 */

		public function __construct(interfaces\Manager $manager, $name = null, events\interfaces\Emitter $emitter = null)
		{
			$this->manager = $manager;

			// Proceed to create the base Event.
			parent::__construct($name, $emitter);
		}

		/**
		 * Returns the Manager instance responsible for emitting this Event.
		 *
		 * @return  interfaces\Manager
		 */

		public function getManager()
		{
			return $this->manager;
		}
	}