<?php namespace nyx\framework\console\commands\db\migrate;

	// External dependencies
	use nyx\console;

	// Internal dependencies
	use nyx\framework;

	/**
	 * Migrate Up Command
	 *
	 * @package     Nyx\Framework\Console
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/index.html
	 */

	class Up extends framework\console\Command
	{
		/**
		 * {@inheritDoc}
		 */

		public function __construct($name = 'up')
		{
			parent::__construct($name);
		}

		/**
		 * {@inheritDoc}
		 */

		protected function configure()
		{
			$this->setDescription('Run all outstanding migrations');

			// Set a generic, application-wide input Definition.
			$this->setDefinition(new console\input\Definition(null,
			[
				new console\input\Option('database', null, 'The database connection to use', new console\input\Value(console\input\Value::REQUIRED)),
				new console\input\Option('pretend',  null, 'Dump the SQL queries that would be run'),
				new console\input\Option('path',     null, 'The path to the migration files', new console\input\Value(console\input\Value::REQUIRED))
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
			$path     = $this->getMigrationPath($context);

			// Set which connection to use and run the Migrator.
			$migrator->setConnection($options->get('database'));
			$migrator->run($path, $pretend);

			foreach($migrator->getNotes() as $note)
			{
				$output->writeln($note);
			}
		}

		/**
		 * Returns the path to the directory containing the migrations.
		 *
		 * @return string
		 */

		protected function getMigrationPath(console\Context $context)
		{
			$kernel = $this->getKernel();
			$path   = $context->getInput()->options()->get('path');

			if(null !== $path)
			{
				return $kernel->make('path.base').'/'.$path;
			}

			return $kernel->make('path.src').'/data/migrations';
		}
	}