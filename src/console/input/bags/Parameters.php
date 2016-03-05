<?php namespace nyx\console\input\bags;

	// External dependencies
	use nyx\core\collections;

	// Internal dependencies
	use nyx\console\input;

	/**
	 * Input Parameters Bag
	 *
	 * Base class for Argument and Option bags with their common functionality abstracted away. Parameter bags are bound
	 * by their definitions and such parameters that are not defined cannot be set. They can also not escape their
	 * definition, ie. a definition may only be set during construction to avoid mismatches between the master
	 * master and the bag definitions referenced in Parameter Bags.
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	abstract class Parameters implements \IteratorAggregate, collections\interfaces\Map
	{
		/**
		 * The traits of a Parameter Bag instance.
		 */

		use collections\traits\Map;

		/**
		 * @var Definition  An Input Definition Bag.
		 */

		protected $definition;

		/**
		 * Constructs an Input Parameters Bag instance.
		 *
		 * @param   Definition  $definition     Yo dawg, we herd you like bags, so we put a bag in your bag so you can
		 *                                      define bags while using bags.
		 */

		public function __construct(Definition $definition)
		{
			$this->definition = $definition;
		}

		/**
		 * Returns the Definition for this Bag, which in itself is also a Bag.
		 *
		 * @return  Definition
		 */

		public function definition()
		{
			return $this->definition;
		}

		/**
		 * {@inheritDoc}
		 */

		public function all()
		{
			$return = $this->items;

			// Not doing a simple array merge to preserve the key sorting of the final input.
			foreach($this->definition->getDefaults() as $name => $value)
			{
				if(!isset($return[$name])) $return[$name] = $value;
			}

			return $return;
		}

		/**
		 * {@inheritDoc}
		 */

		public function get($name, $default = null)
		{
			// Instead of duplicating code, let's just try rely on the Definition Bag's exception if the parameter
			// is not defined.
			$definition = $this->definition->get($name);

			return $this->has($name) ? $this->items[$name] : (null === $default ? $definition->getValue()->getDefault() : $default);
		}

		/**
		 * {@inheritDoc}
		 */

		public function set($name, $item)
		{
			// Instead of duplicating code, let's just try rely on the Definition Bag's exception if the parameter
			// is not defined.
			$definition = $this->definition->get($name);

			// Cast the value to an array if we're dealing with a multi-type.
			if($definition->getValue() instanceof input\values\Multiple and !is_array($item))
			{
				$this->items[$name][] = $item;
			}
			else
			{
				$this->items[$name] = $item;
			}
		}
	}