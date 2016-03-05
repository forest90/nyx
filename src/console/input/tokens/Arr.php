<?php namespace nyx\console\input\tokens;

	// External dependencies
	use nyx\core\collections;

	// Internal dependencies
	use nyx\console\interfaces;
	use nyx\console\traits;

	/**
	 * Array Input Tokens
	 *
	 * A collection of unparsed tokens with their starting hyphens etc. still preserved.
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	class Arr extends collections\Map implements interfaces\input\Tokens
	{
		/**
		 * {@inheritDoc}
		 */

		public function has($values)
		{
			$values = (array) $values;

			foreach($values as $value)
			{
				if(in_array($value, array_keys($this->items))) return true;
			}

			return false;
		}

		/**
		 * {@inheritDoc}
		 */

		public function arguments()
		{
			$arguments = [];

			foreach($this->items as $name => $value)
			{
				if($name[0] !== '-') $arguments[$name] = $value;
			}

			return $arguments;
		}

		/**
		 * {@inheritDoc}
		 */

		public function options()
		{
			$options = [];

			foreach($this->items as $name => $value)
			{
				if($name[0] === '-') $options[$name] = $value;
			}

			return $options;
		}
	}