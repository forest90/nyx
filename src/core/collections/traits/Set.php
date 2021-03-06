<?php namespace nyx\core\collections\traits;

	// Internal dependencies
	use nyx\core\collections\interfaces;

	/**
	 * Set
	 *
	 * @package     Nyx\Console\Application
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/collections.html
	 */

	trait Set
	{
		/**
		 * The traits of a Set trait.
		 */

		use Collection;

		/**
		 * @see interfaces\Set::set()
		 */

		public function set($item)
		{
			$this->items[] = $item;

			return $this;
		}

		/**
		 * @see interfaces\Collection::replace()
		 */

		public function replace($items)
		{
			$this->items = [];

			foreach($this->extractItems($items) as $item) $this->set($item);

			return $this;
		}
	}