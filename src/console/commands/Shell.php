<?php namespace nyx\console\commands;

	// Internal dependencies
	use nyx\console;

	/**
	 * Shell
	 *
	 * @package     Nyx\Console\Commands
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/commands.html
	 */

	class Shell extends console\Command
	{
		/**
		 * {@inheritDoc}
		 */

		protected function configure()
		{
			$this
				->setName('shell')
				->setDescription('Runs the current application in a shell.')
				->setHelp(<<<EOF
The <info>%command.name%</info> command wraps the current application in an interactive shell. It does not accept
any options nor arguments.
EOF
				);

			// Make sure this Command is hidden by default.
			$this->status()->set(console\Command::HIDDEN);
		}

		/**
		 * {@inheritdoc}
		 */

		protected function execute(console\Context $context)
		{
			// Instead of using the root/executing Application, we are going to use our direct parent app.
			$executive = $this->parent(true);;

			// If we are dealing with an Application which provides its own Shell - use it.
			if($executive instanceof console\interfaces\shell\Provider) return $executive->getProvidedShell()->start();

			// Otherwise wrap it inside the default Shell.
			return (new console\Shell($executive))->start();
		}
	}