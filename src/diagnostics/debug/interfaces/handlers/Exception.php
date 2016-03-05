<?php namespace nyx\diagnostics\debug\interfaces\handlers;

	/**
	 * Exception Handler Interface
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	interface Exception
	{
		/**
		 * Handles an Exception.
		 *
		 * @param   \Exception  $exception
		 */

		public function handle(\Exception $exception);
	}