<?php namespace nyx\framework\console\commands\db\migrate;

	// External dependencies
	use nyx\console;

	// Internal dependencies
	use nyx\framework;

	/**
	 * Migrate Install Command
	 *
	 * @package     Nyx\Framework\Console
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/index.html
	 */

	class Install extends framework\console\Command
	{
		/**
		 * {@inheritDoc}
		 */

		public function __construct($name = 'install')
		{
			parent::__construct($name);
		}

		/**
		 * {@inheritDoc}
		 */

		protected function configure()
		{
			$this->setDescription('Creates the migration repository');
			$this->setDefinition(new console\input\Definition(null,
			[
				new console\input\Option('database', null, 'The database connection to use', new console\input\Value(console\input\Value::REQUIRED)),
			]));
		}

		/**
		 * {@inheritDoc}
		 */

		protected function execute(console\Context $context)
		{
			$repository = $this->getKernel()->make('migration.repository');

			$repository->setSource($context->getInput()->options()->get('database'));
			$repository->createRepository();

			$context->getOutput()->writeln("<info>Migration table created successfully.</info>");
		}
	}