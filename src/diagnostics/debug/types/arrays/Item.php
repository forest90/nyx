<?php namespace nyx\diagnostics\debug\types\arrays;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * Array Item
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	class Item
	{
		/**
		 * @var string  The key of the underlying value.
		 */

		private $key;

		/**
		 * @var debug\interfaces\Type   The underlying value.
		 */

		private $value;

		/**
		 * Constructs an Array Item.
		 *
		 * @param   string                  $key    The key of the underlying value.
		 * @param   debug\interfaces\Type   $value  The underlying value.
		 */

		public function __construct($key, debug\interfaces\Type $value)
		{
			$this->key   = $key;
			$this->value = $value;
		}

		/**
		 * Returns the key of the item.
		 *
		 * @return  string
		 */

		public function getKey()
		{
			return $this->key;
		}

		/**
		 * Returns the underlying value.
		 *
		 * @return  debug\interfaces\Type
		 */

		public function getValue()
		{
			return $this->value;
		}

		/**
		 * Returns the nesting level of the underlying value.
		 *
		 * @return int
		 */

		public function getLevel()
		{
			return $this->value->getLevel();
		}
	}