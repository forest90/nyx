<?php namespace nyx\core\collections\interfaces;

	/**
	 * Map Interface
	 *
	 * @package     Nyx\Core\Collections
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/collections.html
	 */

	interface Map extends \ArrayAccess, Collection
	{
		/**
		 * Sets the given item in the Collection.
		 *
		 * @param   string|int  $key    The key the item should be set as.
		 * @param   mixed       $item   The item to set.
		 * @return  $this
		 */

		public function set($key, $item);
	}