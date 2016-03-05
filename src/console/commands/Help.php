<?php namespace nyx\console\commands;

	// Internal dependencies
	use nyx\console\input;
	use nyx\console;

	/**
	 * Help
	 *
	 * @package     Nyx\Console\Commands
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/commands.html
	 */

	class Help extends console\Command
	{
		/**
		 * {@inheritDoc}
		 */

		protected function configure()
		{
			$this
				->setName('help')
				->setDescription('Displays help for a command')
				->setHelp(<<<EOF
The <info>%command.name%</info> command displays help for a given command.

To display help for the <info>ls</info> command, you may use it as follows:

  <info>%command.fullName% ls</info>
EOF
				)
				->setDefinition(
				[
					new input\Argument('name', 'The name of the command to provide help for.', new input\Value(input\Value::OPTIONAL))

				],
				[
					new input\Option('format',   'f', 'The output format of the help.', new input\Value(input\Value::OPTIONAL, 'txt')),
					new input\Option('raw',     null, 'Output raw command help (without decorations like colors).')
				]);
		}

		/**
		 * {@inheritDoc}
		 */

		protected function execute(console\Context $context)
		{
			// Do not use the executing application. Instead use the parent suite to grab the given command to make sure
			// we grab one within our own namespace.
			$parent = $this->parent();

			// Grab the I/O from the Context.
			$input  = $context->getInput();
			$output = $context->getOutput();

			// Parameters.
			$options   = $input->options();
			$arguments = $input->arguments();

			// Grab the Command for which we are to provide help.
			$command = $parent->get($arguments->get('name') ?: 'help');

			// We need to remember the initial input Definition of the Command. See below.
			$definition = $command->getDefinition();

			// Temporarily merge the Definitions without merging arguments, though. We are going to restore the previous
			// Definition right away the Command gets described properly.
			$command->setDefinition($definition->merge(false, $parent->getDefinition()));

			// Write the description to the Output.
			$parent->helpers('descriptor')->describe($command, $options->get('format'), $options->get('raw'), [], $output);

			// Need to restore the Definition for shell apps.
			$command->setDefinition($definition);
		}
	}