<?php namespace nyx\core\interfaces;

	/**
	 * Arrayable Interface
	 *
	 * An Arrayable object is one that provides a method to cast it to an array.
	 *
	 * @package     Nyx\Core\Interfaces
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/interfaces.html
	 */

	interface Arrayable
	{
		/**
		 * Returns the object as an array.
		 *
		 * @return  array
		 */

		public function toArray();
	}