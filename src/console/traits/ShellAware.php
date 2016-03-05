<?php namespace nyx\console\traits;

	// Internal dependencies
	use nyx\console\interfaces;
	use nyx\console;

	/**
	 * Shell Aware
	 *
	 * Allows for the implementation of the interfaces\shell\Aware interface.
	 *
	 * @package     Nyx\Console\Shell
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/output.html
	 */

	trait ShellAware
	{
		/**
		 * @var console\Shell   The Shell instance that is executing the exhibitor of this trait.
		 */

		private $executingShell;

		/**
		 * @see interfaces\shell\Aware::getExecutingShell()
		 */

		public function getExecutingShell()
		{
			return $this->executingShell;
		}

		/**
		 * @see interfaces\shell\Aware::setExecutingShell()
		 */

		public function setExecutingShell(console\Shell $shell)
		{
			$this->executingShell = $shell;

			return $this;
		}

		/**
		 * @see interfaces\shell\Aware::isRunningInShell()
		 */

		public function isRunningInShell()
		{
			return null !== $this->executingShell;
		}
	}