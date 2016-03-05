<?php namespace nyx\console\input\bags;

	// Internal dependencies
	use nyx\console\input;
	use nyx\console;

	/**
	 * Input Options Bag
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	class Options extends Parameters
	{
		/**
		 * {@inheritDoc}
		 *
		 * Overridden to enforce a stricter type.
		 *
		 * @param   definitions\Options $definition     The Definition of this Options Bag.
		 */

		public function __construct(definitions\Options $definition)
		{
			parent::__construct($definition);
		}

		/**
		 *
		 * Note: Values for Options that accept multiple values will be appended unless the value given is an array.
		 */

		public function set($name, $value)
		{
			// Grab the definition for the specific Option requested. Will throw an exception when the Option is not
			// defined.
			$definition = $this->definition->get($name);

			// Handle value requirements appropriately.
			if(null === $value)
			{
				if($value = $definition->getValue() and $value->is(input\Value::REQUIRED))
				{
					throw new \RuntimeException("The option [--$name] requires a value");
				}

				// Grab the default value for optional values, otherwise simply set it to true to indicate that
				// the option is present.
				$value = $value->is(input\Value::OPTIONAL) ? $value->getDefault() : true;
			}

			// Slight overhead, because the parent will grab the definition again, but at the same time it handles
			// multiple value parameters for us.
			parent::set($name, $value);
		}
	}