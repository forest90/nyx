<?php namespace nyx\console\interfaces\input;

	// External dependencies
	use nyx\core;

	/**
	 * Input Tokens Interface
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	interface Tokens
	{
		/**
		 * Prepends a token to the collection.
		 *
		 * @param   string  $value  The value to prepend.
		 */

		public function prepend($value);

		/**
		 * Returns all tokens which do not start with a hyphen and therefore *appear to* be arguments.
		 *
		 * @return  array
		 */

		public function arguments();

		/**
		 * Returns all tokens which start with a hyphen and therefore *appear to* be options, without differentiating
		 * between short and long options.
		 *
		 * @return  array
		 */

		public function options();
	}