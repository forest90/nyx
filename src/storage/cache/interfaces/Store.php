<?php namespace nyx\storage\cache\interfaces;

	/**
	 * Cache Store Interface
	 *
	 * @package     Nyx\Storage\Cache
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/storage/cache/stores.html
	 */

	interface Store
	{
		/**
		 * Returns an item from the Store by its key.
		 *
		 * @param   string  $key    The key of the item to get.
		 * @return  mixed           The value of the item or null if the item could not be found.
		 */

		public function get($key);

		/**
		 * Sets an item in the Store for the given number of seconds.
		 *
		 * @param   string  $key        The key the item should be set as.
		 * @param   mixed   $value      The value of the item which should be set.
		 * @param   int     $ttl        The time in seconds the item should be kept in the Store.
		 * @return  $this
		 */

		public function set($key, $value, $ttl = 60);

		/**
		 * Sets an item in the cache for an indefinite time.
		 *
		 * @param   string  $key        The key the item should be set as.
		 * @param   mixed   $value      The value of the item which should be set.
		 * @return  $this
		 */

		public function keep($key, $value);

		/**
		 * Checks whether an item with the given key is set in the Store, ie. not null.
		 *
		 * @param   string  $key    The key of the item to check.
		 * @return  bool            True when the item is set, false otherwise.
		 */

		public function has($key);

		/**
		 * Removes an item from the Store.
		 *
		 * @param   string  $key    The key of the item to remove.
		 * @return  $this
		 */

		public function remove($key);

		/**
		 * Increments the value of an item in the Store by $by.
		 *
		 * @param   string  $key    The key of the item whose value should be incremented.
		 * @param   int     $by     The number by which the value should be incremented.
		 * @return  $this
		 */

		public function increment($key, $by = 1);

		/**
		 * Decrements the value of an item in the Store by $by.
		 *
		 * @param   string  $key    The key of the item whose value should be decremented.
		 * @param   int     $by     The number by which the value should be decremented.
		 * @return  $this
		 */

		public function decrement($key, $by = 1);

		/**
		 * Removes all items from the Store.
		 *
		 * @return  $this
		 */

		public function flush();

		/**
		 * Returns the cache key prefix.
		 *
		 * @return  string
		 */

		public function getPrefix();
	}