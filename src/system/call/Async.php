<?php namespace nyx\system\call;

	// Internal dependencies
	use nyx\system;

	/**
	 * Asynchronous System Call
	 *
	 * Acts as a Process Builder for non-blocking, ie. asynchronous Processes. Asynchronous Calls differ from
	 * standard Calls in two important aspects - they do not support piping other Calls to them and the
	 * {@see self::execute()} method will return a Process instance instead of a finished Result.
	 *
	 * @package     Nyx\System\Calls
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/system/calls.html
	 * @todo        Add in support for piped Calls? How?
	 */

	class Async extends system\Call
	{
		/**
		 * {@inheritDoc}
		 *
		 * @throws  \BadMethodCallException     Piped calls are unsupported by Asynchronous Calls.
		 */

		public function pipe(system\Call $call)
		{
			throw new \BadMethodCallException("Piped Calls are unsupported by Asynchronous System Calls.");
		}

		/**
		 * {@inheritDoc}
		 *
		 * Overridden since Asynchronous Calls return the Process directly instead of waiting for a Result and don't
		 * support piped Calls.
		 *
		 * @return  Process
		 */

		protected function doExecute(Process $process, callable $callback = null, $timeout = 60)
		{
			return $process;
		}
	}