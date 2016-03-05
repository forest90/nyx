<?php namespace nyx\framework\console\commands\db\migrate;

	// External dependencies
	use nyx\console;

	// Internal dependencies
	use nyx\framework;

	/**
	 * Migrate Down Command
	 *
	 * @package     Nyx\Framework\Console
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/index.html
	 */

	class Down extends framework\console\Command
	{
		/**
		 * {@inheritDoc}
		 */

		public function __construct($name = 'down')
		{
			parent::__construct($name);
		}

		/**
		 * {@inheritDoc}
		 */

		protected function configure()
		{
			$this->setDescription('Rollback the last migration');

			// Set a generic, application-wide input Definition.
			$this->setDefinition(new console\input\Definition(null,
			[
				new console\input\Option('database', null, 'The database connection to use', new console\input\Value(console\input\Value::REQUIRED)),
				new console\input\Option('pretend',  null, 'Dump the SQL queries that would be run'),
			]));
		}

		/**
		 * {@inheritDoc}
		 */

		protected function execute(console\Context $context)
		{
			$options  = $context->getInput()->options();
			$output   = $context->getOutput();
			$pretend  = $options->get('pretend');
			$migrator = $this->getKernel()->make('migrator');

			// Set which connection to use and run the rollback.
			$migrator->setConnection($options->get('database'));
			$migrator->rollback($pretend);

			foreach($migrator->getNotes() as $note)
			{
				$output->writeln($note);
			}
		}
	}