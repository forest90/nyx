<?php namespace nyx\console\input;

	// Internal dependencies
	use nyx\console\traits;

	/**
	 * Input Parameter Definition
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	abstract class Parameter
	{
		/**
		 * The traits of an input Parameter.
		 */

		use traits\Named;

		/**
		 * @var string  The description of the parameter.
		 */

		private $description;

		/**
		 * @var Value   The Value definition for this Parameter.
		 */

		private $value;

		/**
		 * Constructor.
		 *
		 * @param   string  $name           The name of this parameter.
		 * @param   string  $description    A description of this parameter.
		 * @param   Value   $value          A Value definition for this Parameter.
		 */

		public function __construct($name, $description = null, Value $value = null)
		{
			$this->description = $description;

			// Make the name conform to our generic naming rules.
			$this->setName($name);

			// Use the given Value or create a new definition with sane defaults.
			$this->setValue($value ?: new Value());
		}

		/**
		 * Returns the description of this parameter.
		 *
		 * @return  Value
		 */

		public function getDescription()
		{
			return $this->description;
		}

		/**
		 * Sets the description of this parameter.
		 *
		 * @param   string  $description
		 */

		public function setDescription($description)
		{
			$this->description = $description;
		}

		/**
		 * Returns the Value definition assigned to this Parameter.
		 *
		 * @return  Value
		 */

		public function getValue()
		{
			return $this->value;
		}

		/**
		 * Sets the Value definition assigned to this Parameter. Note the access scope, as the value definition
		 * should not be modified directly after getting assigned to a Parameter, without the Parameter enforcing
		 * its own rules upon the Value.
		 *
		 * @param   Value   $value
		 */

		public function setValue(Value $value)
		{
			$this->value = $value;
		}
	}