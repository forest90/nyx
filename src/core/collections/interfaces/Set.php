<?php namespace nyx\core\collections\interfaces;

	/**
	 * Set Interface
	 *
	 * @package     Nyx\Core\Collections
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/collections.html
	 */

	interface Set extends Collection
	{
		/**
		 * Sets the given item in the Collection.
		 *
		 * @param   mixed   $item   The item to set.
		 * @return  $this
		 */

		public function set($item);
	}