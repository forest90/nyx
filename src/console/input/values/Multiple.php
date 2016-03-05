<?php namespace nyx\console\input\values;

	// Internal dependencies
	use nyx\console\input;

	/**
	 * Input Parameter Multiple Value Definition
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	class Multiple extends input\Value
	{
		/**
		 * {@inheritDoc}
		 */

		public function setDefault($default = [])
		{
			parent::setDefault($default ? (array) $default : null);
		}
	}