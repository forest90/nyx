<?php namespace nyx\diagnostics\debug;

	/**
	 * Type
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	abstract class Type implements interfaces\Type
	{
		/**
		 * The actual name of the type. Extended by the concrete classes and accessed statically by self::getType().
		 */

		const NAME = '';

		/**
		 * @var mixed   The value of the underlying variable.
		 */

		protected $value;

		/**
		 * @var int The nesting level of the underlying value if it belongs to a structure.
		 */

		protected $level;

		/**
		 * @var int     The length of the underlying variable.
		 */

		protected $length;

		/**
		 * Factory method for creating concrete Types based on the value given.
		 *
		 * @param   mixed   $value  The value to create the Type for.
		 * @param   int     $level  The nesting level of the value.
		 * @return  interfaces\Type
		 * @todo    Might perform better with cloning instead of instantiating.
		 */

		public static function create($value, $level = 0)
		{
			if(null === $value)
			{
				return new types\NullType($value, $level);
			}
			elseif(is_bool($value))
			{
				return new types\BoolType($value, $level);
			}
			elseif(is_string($value))
			{
				return new types\StringType($value, $level);
			}
			elseif(is_int($value))
			{
				return new types\IntType($value, $level);
			}
			elseif(is_array($value))
			{
				return new types\ArrayType($value, $level);
			}
			elseif(is_object($value))
			{
				return new types\ObjectType($value, $level);
			}
			elseif(is_float($value))
			{
				return new types\FloatType($value, $level);
			}
			elseif(is_resource($value))
			{
				return new types\ResourceType($value, $level);
			}
		}

		/**
		 * Constructs a new Type.
		 *
		 * @param   mixed   $value  The value of the underlying variable.
		 * @param   int     $level  The nesting level of the underlying value if it belongs to a structure.
		 */

		public function __construct($value, $level = 0)
		{
			$this->value = $value;
			$this->level = (int) $level;
		}

		/**
		 * {@inheritDoc}
		 */

		public function getType()
		{
			return static::NAME;
		}

		/**
		 * {@inheritDoc}
		 */

		public function getValue()
		{
			return $this->value;
		}

		/**
		 * {@inheritDoc}
		 */

		public function getLevel()
		{
			return $this->level;
		}

		/**
		 * {@inheritDoc}
		 */

		public function setLevel($level)
		{
			$this->level = (int) $level;

			return $this;
		}

		/**
		 * {@inheritDoc}
		 */

		public function getLength()
		{
			return $this->length;
		}

		/**
		 * {@inheritDoc}
		 */

		public function toString()
		{
			return (string) $this->value;
		}

		/**
		 * {@inheritDoc}
		 */

		public function __toString()
		{
			return $this->toString();
		}
	}