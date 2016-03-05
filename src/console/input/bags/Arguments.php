<?php namespace nyx\console\input\bags;

	// Internal dependencies
	use nyx\console\exceptions;
	use nyx\console\input;

	/**
	 * Input Arguments Bag
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	class Arguments extends Parameters
	{
		/**
		 * {@inheritDoc}
		 *
		 * Overridden to enforce a stricter type.
		 *
		 * @param   definitions\Arguments   $definition     The Definition of this Arguments Bag.
		 */

		public function __construct(definitions\Arguments $definition)
		{
			parent::__construct($definition);
		}

		/**
		 * Adds an argument's value to the stack.
		 *
		 * Automatically decides on the name of the argument based on the present definition and handles arrays of
		 * values for arguments accepting multiple values.
		 *
		 * @param   string  $value                          The argument's value to set.
		 * @throws  exceptions\input\ArgumentsTooMany       When the definition does not permit any further arguments.
		 */

		public function add($value)
		{
			// How many arguments have we got so far?
			$count = $this->count();

			// If the definition leaves room for one more argument, let's set it the casual way.
			if($defined = $this->definition->get($count, false))
			{
				$this->set($defined->getName(), $value);
			}
			// It didn't. But maybe the last argument accepts multiple values
			elseif($defined = $this->definition->get($count - 1, false) and $defined->getValue() instanceof input\values\Multiple)
			{
				$this->items[$defined->getName()][] = $value;
			}
			// Oops, a bridge too far.
			else
			{
				throw new exceptions\input\ArgumentsTooMany($this);
			}
		}

		/**
		 * Validates this instance.
		 *
		 * Checks if the Bag contains all necessary arguments. We are not validating whether there are too many arguments
		 * as this can only happen when add()'ing values directly and that method handles this case already.
		 *
		 * @throws  exceptions\input\ArgumentsNotEnough     When not enough arguments are present in this Bag.
		 */

		public function validate()
		{
			if($this->count() < $this->definition->requires())
			{
				throw new exceptions\input\ArgumentsNotEnough($this);
			}
		}

		/**
		 * Returns an array of the arguments containing the given string in their values.
		 *
		 * @param   string  $string     The string to look for.
		 * @return  array
		 */

		public function containing($string)
		{
			$return = [];

			foreach($this->items as $name => $value)
			{
				if(strpos($value, $string) !== false) $return[$name] = $value;
			}

			return $return;
		}
	}