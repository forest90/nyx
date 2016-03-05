<?php namespace nyx\events\diagnostics\profiling\collectors;

	// External dependencies
	use nyx\diagnostics\profiling;

	// Internal dependencies
	use nyx\events\diagnostics\profiling\interfaces;

	/**
	 * Events Data Collector
	 *
	 * @package     Nyx\Events\Diagnostics\Profiling
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/events/diagnostics.html
	 * @todo        Get rid of the single Emitter design in favour of setting the event data manually? Or simply have
	 *              multiple collectors running if someone wants to use multiple emitters, considering the Collector's
	 *              name is variable?
	 */

	class Events extends profiling\Collector
	{
		/**
		 * @var interfaces\TraceableEmitter     A traceable event Emitter.
		 */

		private $emitter;

		/**
		 * {@inheritDoc}
		 *
		 * @param   interfaces\TraceableEmitter $emitter    A traceable event Emitter.
		 */

		public function __construct(interfaces\TraceableEmitter $emitter, $name = 'events')
		{
			$this->emitter = $emitter;

			parent::__construct($name);
		}

		/**
		 * {@inheritDoc}
		 */

		public function collect(profiling\Context $context = null)
		{
			$this->data = [
				'called_listeners'      => $this->emitter->getCalledListeners(),
				'not_called_listeners'  => $this->emitter->getNotCalledListeners(),
				'emitted_events'        => $this->emitter->getEmittedEvents(),
			];
		}

		/**
		 * {@see interfaces\TraceableEmitter::getCalledListeners()}
		 */

		public function getCalledListeners()
		{
			return $this->data['called_listeners'];
		}

		/**
		 * {@see interfaces\TraceableEmitter::getNotCalledListeners()}
		 */

		public function getNotCalledListeners()
		{
			return $this->data['not_called_listeners'];
		}

		/**
		 * {@see interfaces\TraceableEmitter::getEmittedEvents()}
		 */

		public function getEmittedEvents()
		{
			return $this->data['emitted_events'];
		}
	}