<?php namespace nyx\core\promises\interfaces;

	/**
	 * Promise Resolver Interface
	 *
	 * @package     Nyx\Core\Promises
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/promises.html
	 * @todo        Decide: Inject the Promise to resolve/reject/update directly in the methods?
	 */

	interface Resolver
	{
		/**
		 * Resolves the Promise with the given result.
		 *
		 * @param   mixed       $result     The result to resolve.
		 * @return  Promise                 The resolved Promise.
		 */

		public function resolve($result = null);

		/**
		 * Rejects the Promise with the given reason.
		 *
		 * @param   mixed       $reason     The reason why the Promise is getting rejected.
		 * @return  Promise                 The rejected Promise.
		 */

		public function reject($reason = null);

		/**
		 * Progresses the Promise with the given value.
		 *
		 * @param   mixed       $update     The value to update the state with.
		 * @return  $this                   The Resolver.
		 */

		public function progress($update = null);
	}