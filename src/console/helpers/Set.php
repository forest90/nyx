<?php namespace nyx\console\helpers;

	// External dependencies
	use nyx\core\collections;

	// Internal dependencies
	use nyx\console\interfaces;

	/**
	 * Helper Set
	 *
	 * Acts as container for helpers to make them easier to pass around and reuse in your application's logic.
	 *
	 * @package     Nyx\Console\Helpers
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/helpers.html
	 */

	class Set extends collections\Set
	{
		/**
		 * {@inheritDoc}
		 *
		 * Overridden to enforce a stricter type.
		 *
		 * @throws  \InvalidArgumentException   When the given $helper is not a valid instance of the Helper interface.
		 */

		public function set($helper)
		{
			if(!$helper instanceof interfaces\Helper)
			{
				throw new \InvalidArgumentException("Expected an instance of nyx\\console\\interfaces\\Helper, got [".gettype($helper)."] instead.");
			}

			$this->items[$helper->getName()] = $helper;

			return $this;
		}
	}