<?php namespace nyx\diagnostics\profiling\interfaces;

	// Internal dependencies
	use nyx\diagnostics\profiling;

	/**
	 * Profiling Data Collector Interface
	 *
	 * A Profiling Data Collector is responsible for extracting meaningful data out of a Profiling Context and the
	 * current environmental/global variables it has access to, in order to provide access to them in a structured
	 * manner.
	 *
	 * @package     Nyx\Diagnostics\Profiling
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/profiling/index.html
	 */

	interface Collector extends \Serializable
	{
		/**
		 * Collects data for the given Profiling Context.
		 *
		 * @param   profiling\Context   $context    The Context for which data shall be collected.
		 */

		public function collect(profiling\Context $context = null);

		/**
		 * Returns the name of the Collector.
		 *
		 * @return string
		 */

		public function getName();

		/**
		 * Flattens the Collector, ie. forces it to loose its object dependencies and returns the result.
		 *
		 * @return  Collector   A new instance of a flattened Collector based on the current instance.
		 */

		public function snapshot();
	}