<?php namespace nyx\diagnostics\debug\dumpers;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * Export Dumper
	 *
	 * Uses var_export() to perform the dump.
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	class Export extends debug\Dumper
	{
		/**
		 * {@inheritDoc}
		 */

		public function dump()
		{
			$result = '';

			// var_export() isn't variadic so we need to adapt.
			foreach(func_get_args() as $i => $variable)
			{
				$result .= var_export($variable, true);
			}

			return $result;
		}
	}