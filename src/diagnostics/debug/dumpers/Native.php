<?php namespace nyx\diagnostics\debug\dumpers;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * Native Dumper
	 *
	 * Uses var_dump() to perform the dump.
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	class Native extends debug\Dumper
	{
		/**
		 * {@inheritDoc}
		 */

		public function dump()
		{
			ob_start();

			call_user_func_array('var_dump', func_get_args());

			return trim(ob_get_clean());
		}
	}