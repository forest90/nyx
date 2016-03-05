<?php namespace nyx\framework\console\commands\db;

	// External dependencies
	use nyx\console;

	/**
	 * Database Migrate Commands Suite
	 *
	 * @package     Nyx\Framework\Console
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/index.html
	 */

	class Migrate extends console\Suite
	{
		/**
		 * {@inheritDoc}
		 *
		 * Overridden to set a default name for the Suite.
		 */

		public function __construct($name = 'migrate')
		{
			parent::__construct($name);
		}

		/**
		 * {@inheritDoc}
		 */

		protected function configure()
		{
			$this->set([
				new migrate\Install,
				new migrate\Up,
				new migrate\Down,
				new migrate\Reset,
			]);
		}
	}