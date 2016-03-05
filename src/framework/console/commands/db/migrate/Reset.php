<?php namespace nyx\framework\console\commands\db\migrate;

	// External dependencies
	use nyx\console;

	// Internal dependencies
	use nyx\framework;

	/**
	 * Migrate Reset Command
	 *
	 * @package     Nyx\Framework\Console
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/index.html
	 */

	class Reset extends framework\console\Command
	{
		/**
		 * {@inheritDoc}
		 */

		public function __construct($name = 'reset')
		{
			parent::__construct($name);
		}

		/**
		 * {@inheritDoc}
		 */

		protected function configure()
		{
			$this->setDescription('Rollback all database migrations');
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
			$migrator = $this->getKernel()->make('migrator');
			$options  = $context->getInput()->options();
			$pretend  = $options->get('pretend');

			// Set which connection to use.
			$migrator->setConnection($options->get('database'));

			while(true)
			{
				$count = $migrator->rollback($pretend);

				foreach($migrator->getNotes() as $note)
				{
					$context->getOutput()->writeln($note);
				}

				if($count === 0) break;
			}
		}
	}