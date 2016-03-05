<?php namespace nyx\console\events;

	// External dependencies
	use nyx\events;

	// Internal dependencies
	use nyx\console;

	/**
	 * Console Event
	 *
	 * A generic console Event which gives full read and write access to the Execution Context.
	 *
	 * Please see {@see console\Context} for more information on what is possible once given a Context instance since
	 * the class is highly flexible to the point where it may completely turn the execution procedure upside down,
	 * especially once combined with events.
	 *
	 * @package     Nyx\Console\Events
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/events.html
	 */

	class Event extends events\Event
	{
		/**
		 * @var console\Context     The execution Context which gets passed along.
		 */

		private $context;

		/**
		 * Constructs a new console Event.
		 *
		 * @param   console\Context     $context    The execution Context which shall be passed along.
		 * @param   string              $name       An optional trigger name for this event {@see events\Event::__construct()}
		 */

		public function __construct(console\Context $context, $name = null)
		{
			$this->context = $context;

			parent::__construct($name);
		}

		/**
		 * Returns the execution Context which gets passed along.
		 *
		 * @return  console\Context
		 */

		public function getContext()
		{
			return $this->context;
		}
	}