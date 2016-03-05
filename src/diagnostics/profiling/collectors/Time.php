<?php namespace nyx\diagnostics\profiling\collectors;

	// Internal dependencies
	use nyx\diagnostics\profiling;

	/**
	 * Time Data Collector
	 *
	 * @package     Nyx\Diagnostics\Profiling
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/profiling/index.html
	 * @todo        Enforce types for the Stopwatch events?
	 */

	class Time extends profiling\Collector
	{
		/**
		 * {@inheritDoc}
		 */

		public function __construct($name = 'time')
		{
			parent::__construct($name);
		}

		/**
		 * {@inheritDoc}
		 */

		public function collect(profiling\Context $context = null)
		{
			$this->data =
			[
				'start_time' => $_SERVER['REQUEST_TIME_FLOAT'] * 1000,
				'events'     => [],
				'allowed'    => ini_get('max_execution_time') * 1000
			];
		}

		/**
		 * Returns the request events.
		 *
		 * @return  array
		 */

		public function getEvents()
		{
			return $this->data['events'];
		}

		/**
		 * Sets the request events.
		 *
		 * @param   array   $events
		 */

		public function setEvents(array $events)
		{
			foreach($events as $event) $event->ensureStopped();

			$this->data['events'] = $events;
		}

		/**
		 * Gets the request elapsed time.
		 *
		 * @return  float
		 */

		public function getDuration()
		{
			$lastEvent = $this->data['events']['__section__'];

			return $lastEvent->getOrigin() + $lastEvent->getDuration() - $this->getStartTime();
		}

		/**
		 * Returns the time spent on initialization, ie. the time spent on processing
		 * before request handling kicked in.
		 *
		 * @return  float
		 */

		public function getInitTime()
		{
			return $this->data['events']['__section__']->getOrigin() - $this->getStartTime();
		}

		/**
		 * Returns the start time of the request.
		 *
		 * @return  int
		 */

		public function getStartTime()
		{
			return $this->data['start_time'];
		}
	}
