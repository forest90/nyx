<?php namespace nyx\console\interfaces\shell;

	// Internal dependencies
	use nyx\console;

	/**
	 * Shell Provider Interface
	 *
	 * Meant to be implemented by Applications which want to ensure that automated utilities like the 'Shell' command
	 * instantiate a specific Shell for them, instead of relying on the default and without having to override the
	 * command itself.
	 *
	 * @package     Nyx\Console\Shell
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/shell.html
	 */

	interface Provider
	{
		/**
		 * Returns a Shell instance which shall be respected as the Shell of choice for the given implementer.
		 *
		 * @return  console\Shell
		 */

		public function getProvidedShell();
	}