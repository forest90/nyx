<?php namespace nyx\console\input\bags\definitions;

	// Internal dependencies
	use nyx\console\input;

	/**
	 * Input Options Definition Bag
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	class Options extends input\bags\Definition
	{
		/**
		 * @var array   The option shortcuts.
		 */

		private $shortcuts;

		/**
		 * Constructor.
		 *
		 * @param   input\Option[]  $options    An array of Option instances.
		 */

		public function __construct(array $options = [])
		{
			// No need to validate the type here. The add() method will do that for us.
			$this->replace($options);
		}

		/**
		 * {@inheritDoc}
		 *
		 * Overridden to reset the shortcuts as well.
		 */

		public function replace($items)
		{
			$this->shortcuts = [];

			parent::replace($items);
		}

		/**
		 * Returns an Option by its name.
		 *
		 * @param   string|int          $name       The name of the Option.
		 * @param   bool                $verbose    Whether an exception will be thrown when the Option doesn't exist.
		 * @return  input\Option                    The requested Option.
		 * @throws  \InvalidArgumentException       When the given Option could not be found.
		 */

		public function get($name, $verbose = true)
		{
			// First let's check if the Option exists.
			if(!$this->has($name))
			{
				if($verbose) throw new \InvalidArgumentException("The [$name] option is not defined.");

				return false;
			}

			return parent::get($name);
		}

		/**
		 * Adds an Option definition.
		 *
		 * @param   input\Option|array          $option
		 * @throws  \InvalidArgumentException               When the option's type is invalid.
		 */

		public function set($option)
		{
			if(is_array($option))
			{
				foreach($option as $item) $this->set($item); return;
			}

			if(!$option instanceof input\Option) throw new \InvalidArgumentException("The Option is of the wrong type.");

			$this->items[$name = $option->getName()] = $option;

			// If the Option has a shortcut...
			$shortcut = $option->getShortcut() and $this->shortcuts[$shortcut] = $name;
		}

		/**
		 * Returns the shortcuts.
		 *
		 * @return  array
		 */

		public function shortcuts()
		{
			return $this->shortcuts;
		}

		/**
		 * Returns the Option matching a given shortcut name.
		 *
		 * @param   string          $shortcut   The name of the shortcut.
		 * @return  input\Option
		 * @throws  \InvalidArgumentException   When the given shortcut is not defined.
		 */

		public function ofShortcut($shortcut)
		{
			if(!isset($this->shortcuts[$shortcut])) throw new \InvalidArgumentException("The shortcut [$shortcut] is not defined.");

			return $this->get($this->shortcuts[$shortcut]);
		}
	}