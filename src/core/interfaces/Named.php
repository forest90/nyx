<?php namespace nyx\core\interfaces;

	/**
	 * Named Interface
	 *
	 * A Named object is one that has a name which can be get and set.
	 *
	 * @package     Nyx\Core\Interfaces
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/interfaces.html
	 */

	interface Named
	{
		/**
		 * Returns the name of the implementer of this interface.
		 *
		 * @return  string
		 */

		public function getName();

		/**
		 * Sets the name of the implementer of this interface.
		 *
		 * @param   string  $name   The name to set.
		 * @return  $this
		 */

		public function setName($name);
	}