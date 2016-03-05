<?php namespace nyx\core\promises\resolvers;

	// Internal dependencies
	use nyx\core\promises\interfaces;
	use nyx\core\promises;

	/**
	 * Deferred Resolver
	 *
	 * @package     Nyx\Core\Promises
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/promises.html
	 */

	class Deferred implements interfaces\Resolver
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

		public function resolve($result = null)
		{
			return $this->deferred->resolve($result);
		}

		/**
		 * {@inheritDoc}
		 */

		public function reject($reason = null)
		{
			return $this->deferred->reject($reason);
		}

		/**
		 * {@inheritDoc}
		 */

		public function progress($update = null)
		{
			return $this->deferred->progress($update);
		}
	}