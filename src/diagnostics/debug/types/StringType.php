<?php namespace nyx\diagnostics\debug\types;

	// External dependencies
	use nyx\utils;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * String Type
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	class StringType extends debug\Type
	{
		/**
		 * The actual name of the type.
		 */

		const NAME = 'string';

		/**
		 * @var string  The encoding of the underlying value.
		 */

		private $encoding;

		/**
		 * {@inheritDoc}
		 */

		public function __construct($value)
		{
			if(!is_string($value))
			{
				throw new \InvalidArgumentException('Expected string, '.gettype($value).' given.');
			}

			$this->encoding = utils\Str::encoding($value);
			$this->length   = utils\Str::length($value, $this->encoding);
		}

		/**
		 * Returns the encoding of the underlying value.
		 *
		 * @return  string
		 */

		public function getEncoding()
		{
			return $this->encoding;
		}
	}