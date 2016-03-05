<?php namespace nyx\storage\cache\stores;

	// Internal dependencies
	use nyx\storage\cache\interfaces;

	/**
	 * Memcached Cache Store
	 *
	 * @package     Nyx\Storage\Cache
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/storage/cache/stores.html
	 * @todo        Decide: Simplify get() and has()? Just check for null values instead of the result code?
	 */

	class Memcached implements interfaces\Store
	{
		/**
		 * @var \Memcached  The underlying Memcached connection.
		 */

		private $memcached;

		/**
		 * @var string  The cache key prefix.
		 */

		private $prefix;

		/**
		 * Constructs a new Memcached store.
		 *
		 * @param   \Memcached  $memcached  The underlying Memcached connection.
		 * @param   string      $prefix     The cache key prefix.
		 */

		public function __construct(\Memcached $memcached, $prefix = '')
		{
			$this->memcached = $memcached;

			$this->prefix = strlen($prefix) > 0 ? $prefix.':' : '';
		}

		/**
		 * {@inheritDoc}
		 */

		public function get($key)
		{
			$value = $this->memcached->get($this->prefix.$key);

			return $this->memcached->getResultCode() === \Memcached::RES_SUCCESS ? $value : null;
		}

		/**
		 * {@inheritDoc}
		 */

		public function set($key, $value, $ttl = 60)
		{
			$this->memcached->set($this->prefix.$key, $value, $ttl);
		}

		/**
		 * {@inheritDoc}
		 */

		public function keep($key, $value)
		{
			return $this->set($key, $value, 0);
		}

		/**
		 * {@inheritDoc}
		 */

		public function has($key)
		{
			$this->memcached->get($this->prefix.$key);

			return $this->memcached->getResultCode() === \Memcached::RES_SUCCESS;
		}

		/**
		 * {@inheritDoc}
		 */

		public function remove($key)
		{
			$this->memcached->delete($this->prefix.$key);
		}

		/**
		 * {@inheritDoc}
		 */

		public function increment($key, $by = 1)
		{
			return $this->memcached->increment($this->prefix.$key, $by);
		}

		/**
		 * {@inheritDoc}
		 */

		public function decrement($key, $by = 1)
		{
			return $this->memcached->decrement($this->prefix.$key, $by);
		}

		/**
		 * {@inheritDoc}
		 */

		public function flush()
		{
			$this->memcached->flush();
		}

		/**
		 * Returns the underlying Memcached connection.
		 *
		 * @return \Memcached
		 */

		public function getMemcached()
		{
			return $this->memcached;
		}

		/**
		 * {@inheritDoc}
		 */

		public function getPrefix()
		{
			return $this->prefix;
		}
	}