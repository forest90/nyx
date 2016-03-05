<?php namespace nyx\framework\interfaces;

	// Internal dependencies
	use nyx\framework;

	/**
	 * Kernel Aware Interface
	 *
	 * @package     Nyx\Framework
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/index.html
	 */

	interface KernelAware
	{
		/**
		 * Returns the Application Kernel instance in use by the implementer.
		 *
		 * @return  framework\Kernel
		 */

		public function getKernel();

		/**
		 * Sets a Application Kernel instance inside the implementer.
		 *
		 * @param   framework\Kernel    $kernel     The Application Kernel to set.
		 * @return  $this
		 */

		public function setKernel(framework\Kernel $kernel);

		/**
		 * Checks whether the implementer has a set Application Kernel instance.
		 *
		 * @return  bool    True when a Application Kernel instance is set, false otherwise.
		 */

		public function hasKernel();
	}