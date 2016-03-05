<?php namespace nyx\database\interfaces;

	/**
	 * Seeder Interface
	 *
	 * @package     Nyx\Database
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/database/index.html
	 */

	interface Seeder
	{
		/**
		 * Runs the Seeder.
		 */

		public function run();
	}