<?php namespace nyx\console\interfaces\shell;

	// Internal dependencies
	use nyx\console;

	/**
	 * Shell Aware Application Interface
	 *
	 * @package     Nyx\Console\Shell
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/shell.html
	 * @todo        getExecutingShell?
	 * @todo        releaseFromExecutingShell?
	 */

	interface Aware
	{
		/**
		 * Returns the Shell instance which is responsible for executing the implementer of this interface.
		 *
		 * @return  console\Shell
		 */

		public function getExecutingShell();

		/**
		 * Sets the Shell instance which is responsible for executing the implementer of this interface.
		 *
		 * @param   console\Shell   $shell
		 */

		public function setExecutingShell(console\Shell $shell);

		/**
		 * Checks whether the Application is currently being executed from a Shell or not.
		 *
		 * @return  bool
		 */

		public function isRunningInShell();
	}