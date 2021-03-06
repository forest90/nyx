<?php namespace nyx\diagnostics\debug\dumpers;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * Krumo Dumper
	 *
	 * A bridge allowing to use Krumo as a Dumper within the Debug subcomponent.
	 *
	 * Important note: This bridge relies on the krumo class being available, but it is not a Composer package. Please
	 * take a look at {@see http://krumo.sourceforge.net} on how to install and load it.
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	class Krumo extends debug\Dumper
	{
		/**
		 * {@inheritDoc}
		 */

		public function dump()
		{
			return call_user_func_array('krumo::dump', func_get_args());
		}
	}