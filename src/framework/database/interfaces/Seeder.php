<?php namespace nyx\framework\database\interfaces;

	// External dependencies
	use nyx\console;
	use nyx\database;

	// Internal dependencies
	use nyx\framework;

	/**
	 * Seeder Interface
	 *
	 * @package     Nyx\Framework\Database
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/database/index.html
	 */

	interface Seeder extends database\interfaces\Seeder, framework\interfaces\KernelAware, console\interfaces\ContextAware
	{

	}