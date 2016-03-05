<?php namespace nyx\diagnostics\debug\dumpers;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * DBug Dumper
	 *
	 * A bridge allowing to use dBug as a Dumper within the Debug subcomponent.
	 *
	 * Important note: This bridge relies on the dBug class being available, but it is not a Composer package. Please
	 * take a look at {@see http://dbug.ospinto.com} on how to install and load it. If the download link thereon
	 * does not work, try http://dbug.ospinto.com/dl/dBug.zip directly.
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 * @todo        Readable breaks between each variable dump.
	 */

	class DBug extends debug\Dumper
	{
		/**
		 * {@inheritDoc}
		 */

		public function dump()
		{
			// dBug echoes directly, so we need to account for that.
			ob_start();

			// dBug isn't variadic so we need to adapt.
			foreach(func_get_args() as $i => $variable)
			{
				new \dBug($variable);
			}

			return ob_get_clean();
		}
	}