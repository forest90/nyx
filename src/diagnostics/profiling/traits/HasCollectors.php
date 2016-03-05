<?php namespace nyx\diagnostics\profiling\traits;

	// Internal dependencies
	use nyx\diagnostics\profiling;

	/**
	 * Has Collectors
	 *
	 * A Has Collectors object is one that contains a Profiling Collectors Collection that can be get and set.
	 *
	 * @package     Nyx\Diagnostics\Profiling
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/profiling/index.html
	 */

	trait HasCollectors
	{
		/**
		 * @var profiling\Collectors    The data Collectors collection associated with the exhibitor of this trait.
		 */

		private $collectors;

		/**
		 * Returns the Collectors collection in use by the exhibitor of this trait. If none is set, a new collection
		 * will be instantiated and used.
		 *
		 * @return  profiling\Collectors
		 */

		public function getCollectors()
		{
			return $this->collectors ?: $this->collectors = new profiling\Collectors;
		}

		/**
		 * Sets the Collectors collection to be used by the exhibitor of this trait.
		 *
		 * @param   profiling\Collectors    $collectors
		 * @return  $this
		 */

		public function setCollectors(profiling\Collectors $collectors)
		{
			$this->collectors = $collectors;

			return $this;
		}
	}