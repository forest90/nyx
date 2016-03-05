<?php namespace nyx\diagnostics\debug\types;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * Boolean Type
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	class BoolType extends debug\Type
	{
		/**
		 * The actual name of the type.
		 */

		const NAME = 'bool';

		/**
		 * {@inheritDoc}
		 */

		public function __construct($value)
		{
			if(!is_bool($value))
			{
				throw new \InvalidArgumentException('Expected boolean, '.gettype($value).' given.');
			}

			parent::__construct($value);
		}

		/**
		 * {@inheritDoc}
		 */

		public function toString()
		{
			return $this->value ? 'true' : 'false';
		}
	}
