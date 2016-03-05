<?php namespace nyx\framework\traits;

	// Internal dependencies
	use nyx\framework;

	/**
	 * Kernel Aware
	 *
	 * Allows for the implementation of the framework\interfaces\KernelAware interface.
	 *
	 * @package     Nyx\Framework
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/index.html
	 */

	trait KernelAware
	{
		/**
		 * @var framework\Kernel    The Application Kernel.
		 */

		private $kernel;

		/**
		 * @see framework\interfaces\KernelAware::getKernel()
		 */

		public function getKernel()
		{
			return $this->kernel;
		}

		/**
		 * @see framework\interfaces\KernelAware::setKernel()
		 */

		public function setKernel(framework\Kernel $kernel)
		{
			$this->kernel = $kernel;

			return $this;
		}

		/**
		 * @see framework\interfaces\KernelAware::hasKernel()
		 */

		public function hasKernel()
		{
			return null !== $this->kernel;
		}
	}