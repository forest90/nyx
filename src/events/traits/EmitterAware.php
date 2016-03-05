<?php namespace nyx\events\traits;

	// Internal dependencies
	use nyx\events\interfaces;

	/**
	 * Event Emitter Aware
	 *
	 * Allows for the implementation of the interfaces\EmitterAware interface.
	 *
	 * @package     Nyx\Events\Emission
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/events/traits.html
	 */

	trait EmitterAware
	{
		/**
		 * @var interfaces\Emitter  The Event Emitter in use by the exhibitor of this trait.
		 */

		private $emitter;

		/**
		 * @see interfaces\EmitterAware::getEmitter()
		 */

		public function getEmitter()
		{
			return $this->emitter;
		}

		/**
		 * @see interfaces\EmitterAware::setEmitter()
		 */

		public function setEmitter(interfaces\Emitter $emitter)
		{
			$this->emitter = $emitter;

			return $this;
		}

		/**
		 * @see interfaces\EmitterAware::hasEmitter()
		 */

		public function hasEmitter()
		{
			return null !== $this->emitter;
		}
	}