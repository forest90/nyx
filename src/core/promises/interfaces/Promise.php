<?php namespace nyx\core\promises\interfaces;

	/**
	 * Promise Interface
	 *
	 * @package     Nyx\Core\Promises
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/promises.html
	 */

	interface Promise
	{
		/**
		 * Creates a new Promise for the specified handlers.
		 *
		 * @param   callable    $fulfilled  A handler which will be invoked once the Promise is fulfilled and to which
		 *                                  the resolved result will get passed as the first argument.
		 * @param   callable    $error      A handler which will be invoked once the Promise is rejected and to which
		 *                                  the reason of the rejection will get passed as the first argument.
		 * @param   callable    $progress   A handler which will be invoked whenever any progress notifications get
		 *                                  triggered by the producer of the Promise and to which a single, custom
		 *                                  (specified by the producer) argument will get passed.
		 * @return  Promise                 A new Promise instance.
		 */

		public function then(callable $fulfilled = null, callable $error = null, callable $progress = null);
	}