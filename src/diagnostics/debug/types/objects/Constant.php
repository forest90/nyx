<?php namespace nyx\diagnostics\debug\types\objects;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * Class Constant
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	class Constant
	{
		/**
		 * @var string  The name of the underlying class constant.
		 */

		private $name;

		/**
		 * @var debug\interfaces\Type   The value of the underlying class constant.
		 */

		private $value;

		/**
		 * Constructs a new Class Constant.
		 *
		 * @param   string                  $name   The name of the underlying class constant.
		 * @param   debug\interfaces\Type   $value  The value of the underlying class constant.
		 */

		public function __construct($name, debug\interfaces\Type $value)
		{
			$this->name  = $name;
			$this->value = $value;
		}

		/**
		 * Returns the name of the underlying class constant.
		 *
		 * @return  string
		 */

		public function getName()
		{
			return $this->name;
		}

		/**
		 * Returns the value of the underlying class constant.
		 *
		 * @return  debug\interfaces\Type
		 */

		public function getValue()
		{
			return $this->value;
		}

		/**
		 * Returns the nesting level of the underlying class constant.
		 *
		 * @return  int
		 */

		public function getLevel()
		{
			return $this->value->getLevel();
		}
	}