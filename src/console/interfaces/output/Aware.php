<?php namespace nyx\console\interfaces\output;

	// Internal dependencies
	use nyx\console\interfaces;

	/**
	 * Output Aware Interface
	 *
	 * An Output Aware object is one that may contain an Output instance which can be injected and retrieved using the
	 * respective getters/setters.
	 *
	 * @package     Nyx\Console\Output
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	interface Aware
	{
		/**
		 * Returns the Output instance in use by the implementer.
		 *
		 * @return  interfaces\Output
		 */

		public function getOutput();

		/**
		 * Sets an Output instance inside the implementer.
		 *
		 * @param   interfaces\Output   $output     The Output to set.
		 * @return  $this
		 */

		public function setOutput(interfaces\Output $output);

		/**
		 * Checks whether the implementer has a set Output instance.
		 *
		 * @return  bool    True when an Output instance is set, false otherwise.
		 */

		public function hasOutput();
	}