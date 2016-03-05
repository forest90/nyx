<?php namespace nyx\console\interfaces\input;

	// Internal dependencies
	use nyx\console\interfaces;

	/**
	 * Input Aware Interface
	 *
	 * An Input Aware object is one that may contain an Input instance which can be injected and retrieved using the
	 * respective getters/setters.
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	interface Aware
	{
		/**
		 * Returns the Input instance in use by the implementer.
		 *
		 * @return  interfaces\Input
		 */

		public function getInput();

		/**
		 * Sets an Input instance inside the implementer.
		 *
		 * @param   interfaces\Input    $input      The Input to set.
		 * @return  $this
		 */

		public function setInput(interfaces\Input $input);

		/**
		 * Checks whether the implementer has a set Input instance.
		 *
		 * @return  bool    True when an Input instance is set, false otherwise.
		 */

		public function hasInput();
	}