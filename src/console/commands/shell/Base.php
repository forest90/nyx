<?php namespace nyx\console\commands\shell;

	// Internal dependencies
	use nyx\console;

	/**
	 * Base Shell Command
	 *
	 * Base command to be used by concrete shell commands. It is not declared abstract because of the inherited
	 * console\Command::setCode() method which can be used normally here as well.
	 *
	 * @package     Nyx\Console\Shell\Commands
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/commands.html
	 */

	class Base extends console\Command implements console\interfaces\shell\Aware
	{
		/**
		 * The traits of a Shell Command.
		 */

		use console\traits\ShellAware;

		/**
		 * Prepares a Command with access to an instantiated Shell.
		 *
		 * @param   console\Shell   $shell  the Shell instance to use for the Command.
		 * @param   string          $name   The name of the Command.
		 */

		public function __construct(console\Shell $shell, $name = null)
		{
			$this->executingShell = $shell;

			parent::__construct($name);
		}

		/**
		 * {@inheritDoc}
		 */

		protected function configure()
		{
			$this->status()->set(console\Command::HIDDEN);
		}
	}