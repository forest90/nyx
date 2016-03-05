<?php namespace nyx\diagnostics\debug\dumpers;

	// External dependencies
	use Ladybug\Dumper;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * Ladybug Dumper
	 *
	 * A bridge allowing to use Ladybug as a Dumper within the Debug subcomponent. The class also gives you access
	 * to the underlying Ladybug\Dumper instance if you want to customize its settings. Check out Ladybug itself on
	 * Github at {@see https://github.com/raulfraile/Ladybug}.
	 *
	 * Requires:
	 * - Package: raulfraile/ladybug (available as suggestion for nyx/diagnostics within Composer)
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	class Ladybug extends debug\Dumper
	{
		/**
		 * @var Dumper  The underlying instance of the Ladybug\Dumper.
		 */

		private $dumper;

		/**
		 * Constructs a new Dumper bridge for an Ladybug\Dumper instance.
		 *
		 * @param   Dumper  $dumper     An already instantiated Ladybug\Dumper instance or null to construct a new one.
		 */

		public function __construct(Dumper $dumper = null)
		{
			$this->dumper = $dumper ?: new Dumper;
		}

		/**
		 * {@inheritDoc}
		 */

		public function dump()
		{
			return call_user_func_array([$this->dumper, "dump"], func_get_args());
		}

		/**
		 * Returns the underlying Ladybug\Dumper instance.
		 *
		 * @return  Dumper
		 * @todo    Rename to getLadybug or...?
		 */

		public function expose()
		{
			return $this->dumper;
		}
	}