<?php namespace nyx\console\input\bags\definitions;

	// Internal dependencies
	use nyx\console\input;

	/**
	 * Input Arguments Definition Bag
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	class Arguments extends input\bags\Definition
	{
		/**
		 * @var int     The number of arguments required to be present in an Arguments Bag.
		 */

		private $requires;

		/**
		 * @var bool    Whether one of the arguments accepts multiple values and therefore must be the last argument
		 *              present in the definition.
		 */

		private $hasMultiparam;

		/**
		 * @var bool    Whether one of the arguments is optional, meaning no more required arguments can be defined.
		 */

		private $hasOptional;

		/**
		 * Constructor.
		 *
		 * @param   input\Argument[]    $arguments  An array of Argument instances.
		 */

		public function __construct(array $arguments = [])
		{
			// No need to validate the type here. The set() method will do that for us.
			$arguments and $this->replace($arguments);
		}

		/**
		 * {@inheritDoc}
		 */

		public function replace($items)
		{
			$this->hasMultiparam = false;
			$this->hasOptional   = false;
			$this->requires      = 0;

			parent::replace($items);
		}

		/**
		 * Adds an Argument definition.
		 *
		 * @param   input\Argument|array        $argument   The argument definition to set.
		 * @throws  \InvalidArgumentException               When the argument's type is invalid.
		 * @throws  \LogicException                         When an incorrect argument was given.
		 */

		public function set($argument)
		{
			if(is_array($argument))
			{
				foreach($argument as $item) $this->set($item); return;
			}

			if(!$argument instanceof input\Argument) throw new \InvalidArgumentException("The Argument is of the wrong type.");

			if(isset($this->items[$argument->getName()]))
			{
				throw new \LogicException("An argument with the name [$argument->name] already exists.");
			}

			if($this->hasMultiparam)
			{
				throw new \LogicException('Cannot define additional arguments after an argument accepting multiple values');
			}

			if($argument->getValue()->is(input\Value::REQUIRED))
			{
				if($this->hasOptional)
				{
					throw new \LogicException('Cannot add a required argument after an optional one.');
				}

				++$this->requires;
			}
			else
			{
				$this->hasOptional = true;
			}

			if($argument->getValue() instanceof input\values\Multiple)
			{
				$this->hasMultiparam = true;
			}

			// Finally store the Argument.
			$this->items[$argument->getName()] = $argument;
		}

		/**
		 * Returns an Argument by name or by position.
		 *
		 * @param   string|int          $name       The name of the Argument or its position.
		 * @param   bool                $verbose    Whether an exception will be thrown when the argument doesn't exist.
		 * @return  input\Argument                  The requested Argument.
		 * @throws  \InvalidArgumentException       When the given Argument could not be found.
		 */

		public function get($name, $verbose = true)
		{
			// First let's check if the Argument exists.
			if(!$this->has($name))
			{
				if($verbose) throw new \InvalidArgumentException("The [$name] argument does not exist.");

				return false;
			}

			// Are we looking for a name or a position?
			$arguments = is_int($name) ? array_values($this->items) : $this->items;

			return $arguments[$name];
		}

		/**
		 * Returns true if an Argument exists in bag, by name or position.
		 *
		 * @param   string|int          $name   The name of the Argument or its position.
		 * @return  bool
		 */

		public function has($name)
		{
			// Are we looking for a name or a position?
			$arguments = is_int($name) ? array_values($this->items) : $this->items;

			return isset($arguments[$name]);
		}

		/**
		 * Returns the number of Arguments in this Bag.
		 *
		 * @return  int
		 */

		public function count()
		{
			return $this->hasMultiparam ? PHP_INT_MAX : count($this->items);
		}

		/**
		 * Returns the number of required Arguments.
		 *
		 * @return  int
		 */

		public function requires()
		{
			return $this->requires;
		}
	}