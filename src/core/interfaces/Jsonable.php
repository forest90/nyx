<?php namespace nyx\core\interfaces;

	/**
	 * Jsonable Interface
	 *
	 * A Jsonable object is one that provides a method to cast it to a JSON *string*.
	 *
	 * @package     Nyx\Core\Interfaces
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/interfaces.html
	 */

	interface Jsonable
	{
		/**
		 * Returns the JSON string representation of the object.
		 *
		 * @param   int     $options    The options bitmask for the encoder. Implementations must be compatible with
		 *                              json_encode()'s set of of options but may provide a superset.
		 * @return  string
		 */

		public function toJson($options = 0);
	}