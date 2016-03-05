<?php namespace nyx\framework\diagnostics\debug;

	// External dependencies
	use nyx\diagnostics\debug;

	// Internal dependencies
	use nyx\framework;

	/**
	 * Framework Exception Inspector
	 *
	 * @package     Nyx\Framework\Diagnostics\Debug
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/diagnostics.html
	 */

	class Inspector extends debug\Inspector
	{
		/**
		 * @var framework\Kernel    The Kernel in which the Exception was thrown.
		 */

		private $kernel;

		/**
		 * {@inheritDoc}
		 *
		 * @param   framework\Kernel    $kernel     The Kernel in which the Exception was thrown.
		 */

		public function __construct(\Exception $exception, framework\Kernel $kernel = null, handlers\Exception $handler = null)
		{
			$this->kernel = $kernel;

			parent::__construct($exception, $handler);
		}

		/**
		 * Returns the Kernel in which the Exception was thrown is set.
		 *
		 * @return  framework\Kernel
		 */

		public function getKernel()
		{
			return $this->kernel;
		}

		/**
		 * Checks whether the Kernel in which the Exception was thrown is set.
		 *
		 * @return  bool    True when the Kernel is set, false otherwise.
		 */

		public function hasKernel()
		{
			return null !== $this->kernel;
		}
	}