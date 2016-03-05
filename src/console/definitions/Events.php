<?php namespace nyx\console\definitions;

	/**
	 * Console Events Definition
	 *
	 * Describes all Events which can get emitted by a default Application instance during the execution procedure
	 * when it has an Event Emitter set.
	 *
	 * All Console Event instances stemming from the base Console Event class have access to the Execution Context,
	 * which in turn gives them power to completely modify the execution flow of the application by changing the
	 * Commands or I/O instances and executing them anew and anew. Concrete Event classes will also give you access
	 * to more event specific data - please consult the respective classes for more info.
	 *
	 * @package     Nyx\Console\Definitions
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/events.html
	 */

	final class Events
	{
		/**
		 * The EXECUTION_BEFORE event is triggered just before an Application is about to execute an actual Command.
		 *
		 * Listeners registered for this event will receive a {@see \nyx\console\events\ExecutionBefore} event instance
		 * as their first parameter.
		 */

		const EXECUTION_BEFORE = 'console.execution.before';

		/**
		 * The EXECUTION_AFTER event is triggered after an Application is done executing a Command, regardless of
		 * whether an uncaught exception was thrown or not.
		 *
		 * Listeners registered for this event will receive a {@see \nyx\console\events\ExecutionAfter} event instance
		 * as their first parameter.
		 */

		const EXECUTION_AFTER = 'console.execution.after';

		/**
		 * The EXECUTION_EXCEPTION event is triggered after an Application is done executing a Command and an uncaught
		 * Exception was thrown in the process.
		 *
		 * Listeners registered for this event will receive a {@see \nyx\console\events\ExecutionException} event
		 * instance as their first parameter.
		 */

		const EXECUTION_EXCEPTION = 'console.execution.exception';
	}
