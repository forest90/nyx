<?php namespace nyx\events\emitters;

	// Internal dependencies
	use nyx\events\interfaces;
	use nyx\events;

	/**
	 * Immutable Event Emitter
	 *
	 * An event Emitter that acts as a read-only proxy for an actual event Emitter.
	 *
	 * @package     Nyx\Events\Emission
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/events/emission.html
	 */

	class Immutable implements interfaces\Emitter
	{
		/**
		 * @var interfaces\Emitter  The actual event Emitter for which this one acts as a proxy.
		 */

		private $emitter;

		/**
		 * Constructs an Immutable Emitter as a proxy for an actual Emitter.
		 *
		 * @param   interfaces\Emitter  $emitter    The actual event Emitter for which this one shall act as a proxy.
		 */

		public function __construct(interfaces\Emitter $emitter)
		{
			$this->emitter = $emitter;
		}

		/**
		 * {@inheritDoc}
		 */

		public function emit($name, events\Event $event = null)
		{
			return $this->emitter->emit($name, $event);
		}

		/**
		 * {@inheritDoc}
		 */

		public function on($name, callable $listener, $priority = 0)
		{
			throw new \BadMethodCallException('Immutable event Emitters can not be modified.');
		}

		/**
		 * {@inheritDoc}
		 */

		public function once($name, callable $listener, $priority = 0)
		{
			throw new \BadMethodCallException('Immutable event Emitters can not be modified.');
		}

		/**
		 * {@inheritDoc}
		 */

		public function off($name, callable $listener)
		{
			throw new \BadMethodCallException('Immutable event Emitters can not be modified.');
		}

		/**
		 * {@inheritDoc}
		 */

		public function subscribe(interfaces\Subscriber $subscriber)
		{
			throw new \BadMethodCallException('Immutable event Emitters can not be modified.');
		}

		/**
		 * {@inheritDoc}
		 */

		public function unsubscribe(interfaces\Subscriber $subscriber)
		{
			throw new \BadMethodCallException('Immutable event Emitters can not be modified.');
		}

		/**
		 * {@inheritDoc}
		 */

		public function getListeners($name = null)
		{
			return $this->emitter->getListeners($name);
		}

		/**
		 * {@inheritDoc}
		 */

		public function hasListeners($name = null)
		{
			return $this->emitter->hasListeners($name);
		}

		/**
		 * {@inheritDoc}
		 */

		public function countListeners($name = null)
		{
			return $this->emitter->countListeners($name);
		}

		/**
		 * {@inheritDoc}
		 */

		public function removeListeners($name = null)
		{
			throw new \BadMethodCallException('Immutable event Emitters can not be modified.');
		}
	}