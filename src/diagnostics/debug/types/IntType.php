<?php namespace nyx\diagnostics\debug\types;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * Integer Type
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	class IntType extends debug\Type
	{
		/**
		 * The actual name of the type.
		 */

		const NAME = 'int';

		/**
		 * {@inheritDoc}
		 */

		public function __construct($value)
		{
			if(!is_int($value))
			{
				throw new \InvalidArgumentException('Expected integer, '.gettype($value).' given.');
			}

			parent::__construct($value);
	    }
	}