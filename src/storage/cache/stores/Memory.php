<?php namespace nyx\storage\cache\stores;

	// Internal dependencies
	use nyx\storage\cache\interfaces;

	/**
	 * Memory Cache Store
	 *
	 * Stores all values in an array, ie. the lifetime of the cached values is limited to the current request. Mostly
	 * useful for debugging purposes.
	 *
	 * @package     Nyx\Storage\Cache
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/storage/cache/stores.html
	 */

	class Memory implements interfaces\Store
	{
		/**
		 * @var array   The cached values.
		 */

		private $values = [];

		/**
		 * {@inheritDoc}
		 */

		public function get($key)
		{
			return array_key_exists($key, $this->values) ? $this->values[$key] : null;
		}

		/**
		 * {@inheritDoc}
		 */

		public function set($key, $value, $ttl = 60)
		{
			$this->values[$key] = $value;
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
			return array_key_exists($key, $this->values);
		}

		/**
		 * {@inheritDoc}
		 */

		public function remove($key)
		{
			unset($this->values[$key]);
		}

		/**
		 * {@inheritDoc}
		 */

		public function increment($key, $by = 1)
		{
			$this->values[$key] = $this->values[$key] + $by;

			return $this->values[$key];
		}

		/**
		 * {@inheritDoc}
		 */

		public function decrement($key, $by = 1)
		{
			$this->values[$key] = $this->values[$key] - $by;

			return $this->values[$key];
		}

		/**
		 * {@inheritDoc}
		 */

		public function flush()
		{
			$this->values = [];
		}

		/**
		 * {@inheritDoc}
		 */

		public function getPrefix()
		{
			return '';
		}
	}