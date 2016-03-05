<?php namespace nyx\framework\console\commands\db;

	// External dependencies
	use nyx\console;

	// Internal dependencies
	use nyx\framework;

	/**
	 * Database Seed Command
	 *
	 * @package     Nyx\Framework\Console
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/index.html
	 */

	class Seed extends framework\console\Command
	{
		/**
		 * {@inheritDoc}
		 */

		public function __construct($name = 'seed')
		{
			parent::__construct($name);
		}

		/**
		 * {@inheritDoc}
		 */

		protected function configure()
		{
			$this->setDescription('Seeds the database with records');
			$this->setDefinition(new console\input\Definition(null,
			[
				new console\input\Option('class',    null, 'The class name of the root seeder', new console\input\Value(console\input\Value::REQUIRED)),
				new console\input\Option('database', null, 'The database connection to seed on', new console\input\Value(console\input\Value::REQUIRED))
			]));
		}

		/**
		 * {@inheritDoc}
		 */

		protected function execute(console\Context $context)
		{
			$this->prepareDatabase($context);
			$this->makeSeeder($context)->run();
		}

		/**
		 * Returns a seeder instance from the Kernel based on the Context we are running in.
		 *
		 * @param   console\Context $context
		 * @return  framework\database\Seeder
		 */

		protected function makeSeeder(console\Context $context)
		{
			$kernel = $this->getKernel();
			$seeder = $kernel->make($context->getInput()->options()->get('class'));

			// Set the Application Kernel on the Seeder instance if applicable.
			if($kernel and $seeder instanceof framework\interfaces\KernelAware)
			{
				$seeder->setKernel($kernel);
			}

			// Set the Console Context on the Seeder instance if applicable.
			if($seeder instanceof console\interfaces\ContextAware)
			{
				$seeder->setContext($context);
			}

			return $seeder;
		}

		/**
		 * Prepares the database connection.
		 *
		 * @param   console\Context $context
		 * @return  $this
		 */

		protected function prepareDatabase(console\Context $context)
		{
			$kernel = $this->getKernel();

			// Which database name should we use? The one passed in from the console if applicable, or the default one?
			$connectionName = $context->getInput()->options()->get('database') ?: $kernel->make('config')['database.default'];

			// Set the database connection to use by default.
			$kernel->make('db')->setDefaultConnection($connectionName);

			return $this;
		}
	}