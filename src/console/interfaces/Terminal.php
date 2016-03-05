<?php namespace nyx\console\interfaces;

	/**
	 * Terminal Interface
	 *
	 * @package     Nyx\Console\Terminals
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/terminals.html
	 */

	interface Terminal
	{
		/**
		 * Attempts to figure out the width of the terminal the Application is being displayed in and return it. If
		 * unable to determine the width, the method will return the default value given.
		 *
		 * @param   mixed   $default    The default value to return when unable to determine the width.
		 * @return  int|null
		 */

		public function getWidth($default = 80);

		/**
		 * Attempts to figure out the height of the terminal the Application is being displayed in and return it. If
		 * unable to determine the height, the method will return the default value given.
		 *
		 * @param   mixed   $default    The default value to return when unable to determine the height.
		 * @return  int
		 */

		public function getHeight($default = 32);
	}