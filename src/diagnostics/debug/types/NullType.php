<?php namespace nyx\diagnostics\debug\types;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * Null Type
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	class NullType extends debug\Type
	{
		/**
		 * The actual name of the type.
		 */

		const NAME = 'null';

		/**
		 * {@inheritDoc}
		 */

		public function __construct($value)
		{
			if(!is_string($value))
			{
				throw new \InvalidArgumentException('Expected null, '.gettype($value).' given.');
			}

			parent::__construct($value);
		}

		/**
		 * {@inheritDoc}
		 */

		public function toString()
		{
			return 'null';
		}
	}