<?php namespace nyx\work\definitions;

	/**
	 * Work Events Definition
	 *
	 * @package     Nyx\Work\Definitions
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/work/events.html
	 */

	final class Events
	{
		/**
		 * The WORKER_REGISTERED event is triggered after a Worker gets registered with a Manager.
		 *
		 * Listeners registered for this event will receive a {@see \nyx\work\events\Managed} event instance as their
		 * first parameter.
		 */

		const WORKER_REGISTERED     = 'work.worker.registered';
		const WORKER_UNREGISTERED   = 'work.worker.unregistered';
		const WORKER_ADDED          = 'work.worker.added';
		const WORKER_REMOVED        = 'work.worker.removed';
		const WORKER_DECOMMISSIONED = 'work.worker.decommissioned';

		const POOL_DECOMMISSIONED   = 'work.pool.decommissioned';

		const SERVER_ADDED          = 'work.server.added';
		const SERVER_REMOVED        = 'work.server.removed';

		const MANAGER_RELOAD        = 'work.manager.reload';
		const MANAGER_SHUTDOWN      = 'work.manager.shutdown';
	}
