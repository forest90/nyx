<?php namespace nyx\diagnostics\profiling;

	// External dependencies
	use nyx\core;

	/**
	 * Profiling Data Collectors Collection
	 *
	 * Represents a collection of Collectors. Extends the core Collection class but will ignore the key for ArrayAccess
	 * setters in favour of the name set in the Collector being added.
	 *
	 * @package     Nyx\Diagnostics\Profiling
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/profiling/index.html
	 */

	class Collectors extends core\Collection
	{
		/**
		 * Constructs a new Collectors collection.
		 *
		 * @param   array   $collectors     An array of Collectors the collection should be instantiated with.
		 */

		public function __construct(array $collectors = null)
		{
			if(null !== $collectors) $this->replace($collectors);
		}

		/**
		 * {@inheritDoc}
		 *
		 * Overridden to enforce the type and to grab the Collector's name from itself.
		 *
		 * @param   interfaces\Collector    $collector
		 */

		public function set(interfaces\Collector $collector)
		{
			$this->items[$collector->getName()] = $collector;
		}

		/**
		 * {@inheritDoc}
		 */

		public function replace(array $elements)
		{
			$this->items = [];

			foreach($elements as $element) $this->set($element);
		}

		/**
		 * {@inheritDoc}
		 *
		 * @param   string  $key        Ignored.
		 */

		public function offsetSet($key, $value)
		{
			$this->set($value);
		}
	}