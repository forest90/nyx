<?php namespace nyx\core\promises\types;

	// Internal dependencies
	use nyx\core\promises\interfaces;
	use nyx\core\promises;

	/**
	 * Deferred Promise
	 *
	 * @package     Nyx\Core\Promises
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/promises.html
	 */

	class Deferred implements interfaces\Promise
	{
		/**
		 * @var promises\Deferred   The underlying Deferred instance.
		 */

		private $deferred;

		/**
		 * Constructs a new Deferred Promise.
		 *
		 * @param   promises\Deferred   $deferred   The underlying Deferred instance.
		 */

		public function __construct(promises\Deferred $deferred = null)
		{
			$this->deferred = $deferred;
		}

		/**
		 * {@inheritDoc}
		 */

		public function then(callable $fulfilled = null, callable $error = null, callable $progress = null)
		{
			return $this->deferred->then($fulfilled, $error, $progress);
		}
	}