<?php namespace nyx\console\input;

	/**
	 * Input Argument Definition
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	class Argument extends Parameter
	{
		/**
		 * {@inheritDoc}
		 *
		 * Overridden to ensure Arguments require a value by default.
		 */

		public function __construct($name, $description = null, Value $value = null)
		{
			parent::__construct($name, $description, $value ?: new Value(Value::REQUIRED));
		}

		/**
		 * {@inheritDoc}
		 */

		public function setValue(Value $value)
		{
			// An argument must accept a value. If you don't want to accept a value, don't define it. Simple
			// as that.
			if($value->is(Value::NONE))
			{
				throw new \InvalidArgumentException("A defined argument [{$this->getName()}] must accept a value.");
			}

			parent::setValue($value);
		}
	}