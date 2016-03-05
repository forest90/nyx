<?php namespace nyx\diagnostics\debug\interfaces;

	// External dependencies
	use nyx\core;

	/**
	 * Type Interface
	 *
	 * Represents a variable during runtime.
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/types.html
	 */

	interface Type extends core\interfaces\Stringable
	{
		/**
		 * Returns the type of underlying variable, one of: boolean, integer, float, string, array, object,
		 * resource or null.
		 *
		 * @return  string
		 */

		public function getType();

		/**
		 * Returns the value of the underlying variable.
		 *
		 * @return  mixed
		 */

		public function getValue();

		/**
		 * Returns the nesting level of the underlying value if it belongs to a structure.
		 *
		 * @return  mixed
		 */

		public function getLevel();

		/**
		 * Sets the nesting level of the underlying value if it belongs to a structure.
		 *
		 * @param   int     $level
		 * @return  $this
		 */

		public function setLevel($level);

		/**
		 * Returns the length of the underlying variable.
		 *
		 * @return  int
		 */

		public function getLength();
	}