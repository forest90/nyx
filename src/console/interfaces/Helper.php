<?php namespace nyx\console\interfaces;

	// External dependencies
	use nyx\core;

	/**
	 * Helper Interface
	 *
	 * @package     Nyx\Console\Helpers
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/helpers.html
	 */

	interface Helper extends core\interfaces\Named, output\Aware
	{

	}