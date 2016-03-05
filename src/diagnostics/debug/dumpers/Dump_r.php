<?php namespace nyx\diagnostics\debug\dumpers;

	// External dependencies
	use dump_r\Core;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * Dump_r Dumper
	 *
	 * A bridge allowing to use Dump_r as a Dumper within the Debug subcomponent. Check out Dump_r itself on
	 * Github at {@see https://github.com/leeoniya/dump_r.php}.
	 *
	 * Requires:
	 * - Package: leeoniya/dump-r (available as suggestion for nyx/diagnostics within Composer)
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 * @todo        Readable breaks between each variable dump.
	 * @todo        Adjust the settings locally and apply them on each call to dump_r().
	 */

	class Dump_r extends debug\Dumper
	{
		/**
		 * {@inheritDoc}
		 */

		public function dump()
		{
			$result = '';

			// Dump_r isn't variadic so we need to adapt.
			foreach(func_get_args() as $i => $variable)
			{
				$result .= Core::dump_r($variable);
			}

			return $result;
		}
	}