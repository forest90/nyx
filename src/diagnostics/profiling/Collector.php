<?php namespace nyx\diagnostics\profiling;

	// External dependencies
	use nyx\core;

	/**
	 * Profiling Data Collector
	 *
	 * A Profiling Data Collector is responsible for extracting meaningful data out of a Profiling Context and the
	 * current environmental/global variables it has access to, in order to provide access to them in a structured
	 * manner.
	 *
	 * This abstract Collector implements the \Serializable interface and part of the interfaces\Collector interface
	 * while leaving the actual collect() implementation to concrete Data Collectors.
	 *
	 * Convention: Concrete Collectors must store the data they collect in the protected $data property in order
	 * to make proper use of this class' automation methods.
	 *
	 * @package     Nyx\Diagnostics\Profiling
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/profiling/index.html
	 * @todo        Collector extensions / hooks / onCollect events? Decide when Overlay goes into development.
	 */

	abstract class Collector implements interfaces\Collector
	{
		/**
		 * The traits of a Collector instance.
		 */

		use core\traits\Named;

		/**
		 * @var array   The collected data.
		 */

		protected $data;

		/**
		 * Constructs a Collector.
		 *
		 * @param   string  $name   The name of the Collector.
		 */

		public function __construct($name)
		{
			$this->setName($name);
		}

		/**
		 * {@inheritDoc}
		 */

		public function serialize()
		{
			return serialize([
				'name' => $this->name,
				'data' => $this->data
			]);
		}

		/**
		 * {@inheritDoc}
		 */

		public function unserialize($data)
		{
			$data = unserialize($data);

			// The actual collected data should reside in the 'data' key of the serialized Collector.
			$this->data = $data['data'];

			// Yes, we *are* going to validate the name again. Silly developers, I can see through your schemes. I know
			// you want to cause some "serious havoc" (probably not even a notice) by unserializing an invalid name to
			// bypass the rules. Other than that - it's actually just for consistency.
			$this->setName($data['name']);
		}

		/**
		 * {@inheritDoc}
		 */

		public function snapshot()
		{
			return unserialize(serialize($this));
		}
	}