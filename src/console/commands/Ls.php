<?php namespace nyx\console\commands;

	// Internal dependencies
	use nyx\console\input;
	use nyx\console;

	/**
	 * Ls
	 *
	 * Displays a list of available commands.
	 *
	 * @package     Nyx\Console\Commands
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/commands.html
	 */

	class Ls extends console\Command
	{
		/**
		 * {@inheritDoc}
		 */

		protected function configure()
		{
			$this
				->setName('ls')
				->setDescription('Lists commands')
				->setHelp(<<<EOF
The <info>%command.name%</info> command lists all commands visible within its namespace. You can also display the commands for a specific child namespace:

  <info>%command.fullName% test</info>

You can also output the information in other formats by using the <comment>--format</comment> option:

  <info>%command.fullName% --format=json</info>
EOF
				);


			$this->setDefinition(
			[
				new input\Argument('namespace', 'The namespace for which to provide a list of commands.', new input\Value(input\Value::OPTIONAL))
			],
			[
				new input\Option('all',         'a', 'Display hidden commands. Not taken into account if any filters get applied.'),
				new input\Option('recursive',   'r', 'Show all children within the namespace.'),
				new input\Option('inc',         'i', 'The inclusion filter(s) that should be used on the results.', new input\values\Multiple(input\Value::REQUIRED)),
				new input\Option('format',      'f', 'The output format of the list.', new input\Value(input\Value::OPTIONAL, 'txt')),
				new input\Option('raw',        null, 'Output a raw list (without decorations like colors).')
			]);
		}

		/**
		 * {@inheritdoc}
		 */

		protected function execute(console\Context $context)
		{
			// Grab the I/O from the Context.
			$input  = $context->getInput();
			$output = $context->getOutput();

			// We'll need to work with a few parameters.
			$options   = $input->options();
			$arguments = $input->arguments();

			// Figure out for which Suite we should provide the list of Commands.
			$namespace = $arguments->get('namespace');
			$target    = $namespace ? $this->parent()->get($namespace) : $this->parent();

			// Ensure the target is a Suite.
			if(!$target instanceof console\Suite)
			{
				throw new \InvalidArgumentException("Cannot provide a list of commands for [{$target->chain()}] as it is not a Suite of Commands.");
			}

			$filters = [];
			$inc = $options->get('inc');

			if(empty($inc)) $inc = $options->get('all') ? ['all'] : ['visible'];

			foreach($inc as $name)
			{
				if(!$this->parent()->filters($name))
				{
					$output->writeln("The filter [<comment>$name</comment>] is not available."); continue;
				}

				$filters[] = $name;
			}

			// Write the description to the Output.
			$context->getApplication()->helpers('descriptor')->describe($target, $options->get('format'), $options->get('raw'), [
				'recursive' => $options->get('recursive'),
				'filters' => $filters
			], $output);
		}
	}