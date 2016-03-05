<?php namespace nyx\console\interfaces\output;

	// Internal dependencies
	use nyx\console\interfaces;

	/**
	 * Error Output Aware Interface
	 *
	 * An Error Output Aware object is one that may contain an Error Output instance which can be injected and
	 * retrieved using the respective getters/setters.
	 *
	 * @package     Nyx\Console\Output
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	interface ErrorAware
	{
		/**
		 * Returns the Output instance in use for error output by the implementer.
		 *
		 * @return  interfaces\Output
		 */

		public function getErrorOutput();

		/**
		 * Sets the Output instance to be used for error output by the implementer.
		 *
		 * @param   interfaces\Output   $output     The Error Output to set.
		 * @return  $this
		 */

		public function setErrorOutput(interfaces\Output $output);

		/**
		 * Checks whether the implementer has a set Output instance error output.
		 *
		 * @return  bool    True when an Error Output instance is set, false otherwise.
		 */

		public function hasErrorOutput();
	}