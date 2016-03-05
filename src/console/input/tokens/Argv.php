<?php namespace nyx\console\input\tokens;

	// External dependencies
	use nyx\core\collections;

	// Internal dependencies
	use nyx\console\interfaces;
	use nyx\console\traits;

	/**
	 * Argv Input Tokens
	 *
	 * A collection of unparsed tokens with their starting hyphens etc. still preserved.
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	class Argv extends collections\Set implements interfaces\input\Tokens
	{
		/**
		 * {@inheritDoc}
		 */

		public function get($value, $default = false)
		{
			foreach($this->items as $token)
			{
				if(0 === strpos($token, $value))
				{
					if(false !== $pos = strpos($token, '=')) return substr($token, $pos + 1);

					return $token;
				}
			}

			return $default;
		}

		/**
		 * {@inheritDoc}
		 */

		public function has($values)
		{
			$values = (array) $values;

			foreach($values as $value)
			{
				if(in_array($value, $this->items)) return true;
			}

			return false;
		}

		/**
		 * {@inheritDoc}
		 */

		public function remove($values)
		{
			$values = (array) $values;

			foreach($values as $value)
			{
				if(false !== $key = array_search($value, $this->items)) unset($this->items[$key]);
			}

			return $this;
		}

		/**
		 * {@inheritDoc}
		 */

		public function arguments()
		{
			$arguments = [];

			foreach($this->items as $token)
			{
				if($token[0] !== '-') $arguments[] = $token;
			}

			return $arguments;
		}

		/**
		 * {@inheritDoc}
		 */

		public function options()
		{
			$options = [];

			foreach($this->items as $token)
			{
				if($token[0] === '-') $options[] = $token;
			}

			return $options;
		}
	}