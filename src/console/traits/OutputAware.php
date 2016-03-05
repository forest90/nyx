<?php namespace nyx\console\traits;

	// Internal dependencies
	use nyx\console\interfaces;

	/**
	 * Output Aware
	 *
	 * Allows for the implementation of the interfaces\output\Aware interface.
	 *
	 * @package     Nyx\Console\Output
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/output.html
	 */

	trait OutputAware
	{
		/**
		 * @var interfaces\Output   The Output instance in use by the exhibitor of this trait.
		 */

		private $output;

		/**
		 * @see interfaces\output\Aware::getOutput()
		 */

		public function getOutput()
		{
			return $this->output;
		}

		/**
		 * @see interfaces\output\Aware::setOutput()
		 */

		public function setOutput(interfaces\Output $output)
		{
			$this->output = $output;

			return $this;
		}

		/**
		 * @see interfaces\output\Aware::hasOutput()
		 */

		public function hasOutput()
		{
			return null !== $this->output;
		}
	}