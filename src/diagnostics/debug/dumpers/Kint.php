<?php namespace nyx\diagnostics\debug\dumpers;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * Kint Dumper
	 *
	 * A bridge allowing to use Kint as a Dumper within the Debug subcomponent. Check out Kint itself on
	 * Github at {@see http://raveren.github.io/kint} and {@see https://github.com/raveren/kint}.
	 *
	 * Requires:
	 * - Package: raveren/kint (available as suggestion for nyx/diagnostics within Composer)
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	class Kint extends debug\Dumper
	{
		/**
		 * {@inheritDoc}
		 */

		public function dump()
		{
			// Kint uses something it calls "modifiers", but its analysis doesn't work when calling the Kint::dump()
			// method indirectly, so in order to emulate the behaviour of its "@" modifier and get a string instead
			// of echoing the results, we need to buffer them.
			ob_start();

			call_user_func_array('Kint::dump', func_get_args());

			return ob_get_clean();
		}
	}