<?php namespace nyx\core\collections\traits;

	// Internal dependencies
	use nyx\core\collections\interfaces;

	/**
	 * Map
	 *
	 * @package     Nyx\Core\Collections
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/collections.html
	 */

	trait Map
	{
		/**
		 * The traits of a Map trait.
		 */

		use Collection, ArrayAccess;

		/**
		 * @see interfaces\Map::set()
		 */

		public function set($key, $item)
		{
			$this->items[$key] = $item;

			return $this;
		}

		/**
		 * @see interfaces\Collection::replace()
		 */

		public function replace($items)
		{
			$this->items = [];

			foreach($this->extractItems($items) as $key => $item) $this->set($key, $item);

			return $this;
		}

		/**
		 * @see self::set()
		 */

		public function __set($key, $value)
		{
			$this->set($key, $value);
		}
	}