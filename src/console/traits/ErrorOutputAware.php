<?php namespace nyx\console\traits;

	// Internal dependencies
	use nyx\console\interfaces;

	/**
	 * Error Output Aware
	 *
	 * Allows for the implementation of the interfaces\output\ErrorAware interface.
	 *
	 * @package     Nyx\Console\Output
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/output.html
	 */

	trait ErrorOutputAware
	{
		/**
		 * @var interfaces\Output   The Output instance in use for error output by the exhibitor of this trait.
		 */

		private $errorOutput;

		/**
		 * @see interfaces\output\ErrorAware::getErrorOutput()
		 */

		public function getErrorOutput()
		{
			return $this->errorOutput;
		}

		/**
		 * @see interfaces\output\ErrorAware::setErrorOutput()
		 */

		public function setErrorOutput(interfaces\Output $output)
		{
			$this->errorOutput = $output;

			return $this;
		}

		/**
		 * @see interfaces\output\ErrorAware::hasErrorOutput()
		 */

		public function hasErrorOutput()
		{
			return null !== $this->errorOutput;
		}
	}