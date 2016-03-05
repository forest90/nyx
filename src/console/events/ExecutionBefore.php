<?php namespace nyx\console\events;

	// Internal dependencies
	use nyx\console\definitions;
	use nyx\console;

	/**
	 * Console Before Execution Event
	 *
	 * Please see {@see \nyx\console\definitions\Events} for information on when this Event may get triggered.
	 *
	 * @package     Nyx\Console\Events
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/events.html
	 */

	class ExecutionBefore extends Event
	{
		/**
		 * {@inheritDoc}
		 */

		public function __construct(console\Context $context, $name = definitions\Events::EXECUTION_BEFORE)
		{
			parent::__construct($context, $name);
		}
	}