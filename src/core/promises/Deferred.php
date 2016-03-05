<?php namespace nyx\core\promises;

	// External dependencies
	use nyx\utils;

	/**
	 * Deferred
	 *
	 * @package     Nyx\Core\Promises
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/promises.html
	 */

	class Deferred implements interfaces\Promise, interfaces\Resolver
	{
		/**
		 * @var interfaces\Promise  The completed Promise.
		 */

		private $completed;

		/**
		 * @var types\Deferred      A Deferred Promise.
		 */

		private $promise;

		/**
		 * @var resolvers\Deferred  A Deferred Resolver.
		 */

		private $resolver;

		/**
		 * @var callable[]          An array of resolver and progress handler callables for this Deferred.
		 */

		private $handlers;

		/**
		 * Constructs a new Deferred instance.
		 */

		public function __construct()
		{
			$this->handlers =
			[
				'resolvers' => [],
				'progress'  => []
			];
		}

		/**
		 * {@inheritDoc}
		 */

		public function then(callable $fulfilled = null, callable $error = null, callable $progress = null)
		{
			// If we've already resolved this Promise once, delegate the call to the resolved instance.
			if(null !== $this->completed) return $this->completed->then($fulfilled, $error, $progress);

			$instance = new self;

			if(null !== $progress)
			{
				$updater = function ($update) use ($instance, $progress)
				{
					try
					{
						$instance->progress(call_user_func($progress, $update));
					}
					// Update the Promise with the Exception instead of throwing it.
					catch(\Exception $e)
					{
						$instance->progress($e);
					}
				};
			}
			else
			{
				$updater = [$instance, 'progress'];
			}

			$this->handlers['progress'][]  = $updater;
			$this->handlers['resolvers'][] = function (interfaces\Promise $promise) use ($fulfilled, $error, $instance, $updater)
			{
				$promise
					->then($fulfilled, $error)
					->then([$instance, 'resolve'], [$instance, 'reject'], $updater);
			};

			return $instance->getPromise();
		}

		/**
		 * {@inheritDoc}
		 */

		public function resolve($result = null)
		{
			if(null !== $this->completed) return utils\When::resolve($result);

			$this->completed = utils\When::resolve($result);

			// Process all resolution handlers.
			foreach($this->handlers['resolvers'] as $handler)
			{
				call_user_func($handler, $this->completed);
			}

			// Purge all handlers since we're done and won't need them anymore.
			unset($this->handlers);

			return $this->completed;
		}

		/**
		 * {@inheritDoc}
		 */

		public function reject($reason = null)
		{
			return $this->resolve(utils\When::reject($reason));
		}

		/**
		 * {@inheritDoc}
		 */

		public function progress($update = null)
		{
			if(null !== $this->completed) return $this;

			// Process all progress handlers.
			foreach($this->handlers['progress'] as $handler)
			{
				call_user_func($handler, $update);
			}

			return $this;
		}

		/**
		 * Returns a Deferred Promise.
		 *
		 * @return types\Deferred
		 */

		public function getPromise()
		{
			return $this->promise ?: $this->promise = new types\Deferred($this);
		}

		/**
		 * Returns a Deferred Resolver.
		 *
		 * @return resolvers\Deferred
		 */

		public function getResolver()
		{
			return $this->resolver ?: $this->resolver = new resolvers\Deferred($this);
		}
	}