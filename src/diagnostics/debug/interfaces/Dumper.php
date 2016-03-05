<?php namespace nyx\diagnostics\debug\interfaces;

	/**
	 * Dumper Interface
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 * @todo        Optional direct echoing of the dumps.
	 */

	interface Dumper
	{
		/**
		 * Dumps the given variable, providing information about its type, contents and others. Variadic, ie. accepts
		 * multiple variables as parameters.
		 *
		 * @param   mixed[]     ...     The variable(s) to dump info about.
		 * @return  string              The dumped information.
		 */

		public function dump(/* $var1 [, $var2...$varN] */);
	}