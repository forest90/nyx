<?php namespace nyx\console\input;

	// External dependencies
	use nyx\core;

	/**
	 * Input Parameter Value Definition
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	class Value
	{
		/**
		 * The available types.
		 */

		const NONE      = 1;
		const REQUIRED  = 2;
		const OPTIONAL  = 4;
		const VALID     = 8;

		/**
		 * @var mixed   The default value for this definition.
		 */

		private $default;

		/**
		 * @var core\Mask   The type mask of the value.
		 */

		private $type;

		/**
		 * Constructs a new Input Parameter Value Definition instance.
		 *
		 * @param   int                         $type       The type of the Value (one of the class constants).
		 * @param   mixed                       $default    The default value.
		 * @throws  \InvalidArgumentException               When the given type of the Value is invalid (unrecognized).
		 */

		public function __construct($type = null, $default = null)
		{
			if($type === null)
			{
				$type = self::NONE;
			}
			elseif(!is_int($type) or $type > 15 or $type < 1)
			{
				throw new \InvalidArgumentException("The given type of the value [$type] is invalid.");
			}

			$this->type = new core\Mask($type);
			$this->setDefault($default);
		}

		/**
		 * Compares this Value's type to the given type and returns true if it has the given mode. Shortcut to accessing
		 * the type mask.
		 *
		 * @param   int     $type   The type to check against.
		 * @return  bool
		 */

		public function is($type)
		{
			return $this->type->is($type);
		}

		/**
		 * Checks whether this Value is not expected to be null (for instance when an option is a behavioral flag).
		 *
		 * @return  bool    True when the Value can be non-null, false otherwise.
		 */

		public function accepts()
		{
			return !$this->is(Value::NONE);
		}

		/**
		 * Returns the default value.
		 *
		 * @return  mixed
		 */

		public function getDefault()
		{
			return $this->default;
		}

		/**
		 * Sets the default value.
		 *
		 * @param   mixed               $default    The default value.
		 * @throws  \LogicException                 When an incorrect default value is given.
		 */

		public function setDefault($default = null)
		{
			if($default !== null and !$this->is(Value::OPTIONAL))
			{
				throw new \LogicException('Cannot set a default value for non-optional values.');
			}

			// If it's null, let potential child classes set the default by simply setting a property's value.
			$this->default = $default ?: $this->default;
		}
	}