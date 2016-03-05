<?php namespace nyx\database\interfaces;

	/**
	 * Migration Interface
	 *
	 * @package     Nyx\Database
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/database/index.html
	 */

	interface Migration
	{
		/**
		 * Performs the Migration.
		 */

		public function up();

		/**
		 * Rolls the Migration back.
		 */

		public function down();
	}