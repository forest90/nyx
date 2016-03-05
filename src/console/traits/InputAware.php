<?php namespace nyx\console\traits;

	// Internal dependencies
	use nyx\console\interfaces;

	/**
	 * Input Aware
	 *
	 * Allows for the implementation of the interfaces\input\Aware interface.
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	trait InputAware
	{
		/**
		 * @var interfaces\Input    The Input instance in use by the exhibitor of this trait.
		 */

		private $input;

		/**
		 * @see interfaces\input\Aware::getInput()
		 */

		public function getInput()
		{
			return $this->input;
		}

		/**
		 * @see interfaces\input\Aware::setInput()
		 */

		public function setInput(interfaces\Input $input)
		{
			$this->input = $input;

			return $this;
		}

		/**
		 * @see interfaces\input\Aware::hasInput()
		 */

		public function hasInput()
		{
			return null !== $this->input;
		}
	}