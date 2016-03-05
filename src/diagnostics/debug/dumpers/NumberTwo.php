<?php namespace nyx\diagnostics\debug\dumpers;

	// External dependencies
	use NumberTwo\NumberTwo as Base;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * NumberTwo Dumper
	 *
	 * A bridge allowing to use NumberTwo as a Dumper within the Debug subcomponent. Check out NumberTwo itself on
	 * Github at {@see https://github.com/mnapoli/NumberTwo}.
	 *
	 * Requires:
	 * - Package: mnapoli/number-two (available as suggestion for nyx/diagnostics within Composer)
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 * @todo        Readable breaks between each variable dump.
	 * @todo        Adjust the settings locally and apply them on each call to the base dump().
	 */

	class NumberTwo extends debug\Dumper
	{
		/**
		 * {@inheritDoc}
		 */

		public function dump()
		{
			$result = '';

			// NumberTwo isn't variadic so we need to adapt.
			foreach(func_get_args() as $i => $variable)
			{
				$result .= Base::dump($variable);
			}

			return $result;
		}
	}