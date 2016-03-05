<?php namespace nyx\diagnostics\debug\types;

	// External dependencies
	use nyx\utils;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * Float Type
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	class FloatType extends debug\Type
	{
		/**
		 * The actual name of the type.
		 */

		const NAME = 'float';

		/**
		 * @var bool    Whether the underlying value is NaN.
		 */

		private $nan;

		/**
		 * @var bool    Whether the underlying value is infinite.
		 */

		private $infinite;

		/**
		 * @var int     The number of decimal spaces of the underlying value.
		 */

		private $decimals;

		/**
		 * @var string  The name of the mathematical constant if it's equal to underlying value, if applicable.
		 */

		private $constant;

		/**
		 * {@inheritDoc}
		 */

		public function __construct($value)
		{
			if(!is_float($value))
			{
				throw new \InvalidArgumentException('Expected float, '.gettype($value).' given.');
			}

			$this->nan      = is_nan($value);
			$this->infinite = is_infinite($value);
			$this->decimals = utils\Math::countDecimals($value);
			$this->constant = utils\Math::detectConstant($value, $this->decimals);
		}

		/**
		 * Checks whether the underlying value is NaN.
		 *
		 * @return  bool    True when the underlying value is NaN, false otherwise.
		 */

		public function isNan()
		{
			return $this->nan;
		}

		/**
		 * Checks whether the underlying value is infinite.
		 *
		 * @return  bool    True when the underlying value is infinite, false otherwise.
		 */

		public function isInfinite()
		{
			return $this->infinite;
		}

		/**
		 * Returns the number of decimal spaces of the underlying value.
		 *
		 * @return  string
		 */

		public function getDecimals()
		{
			return $this->decimals;
		}

		/**
		 * Returns the name of the mathematical constant if it's equal to underlying value, if applicable.
		 *
		 * @return  string
		 */

		public function getMathConstantName()
		{
			return $this->constant;
		}
	}