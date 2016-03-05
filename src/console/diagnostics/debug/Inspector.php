<?php namespace nyx\console\diagnostics\debug;

	// External dependencies
	use nyx\diagnostics\debug;

	// Internal dependencies
	use nyx\console;

	/**
	 * Console Exception Inspector
	 *
	 * Extends the base Exception Inspector in order to become aware of the Execution Context, which is optional but
	 * much desired for Delegates to remain consistent with the I/O that is already being used. Plus, it apparently
	 * provides more insight into what was going on when the Exception occurred.
	 *
	 * @package     Nyx\Console\Diagnostics\Debug
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/diagnostics.html
	 */

	class Inspector extends debug\Inspector
	{
		/**
		 * @var console\Context     The Execution Context in which the Exception was thrown.
		 */

		private $context;

		/**
		 * {@inheritDoc}
		 *
		 * @param   console\Context     $context    The Execution Context in which the Exception was thrown.
		 */

		public function __construct(\Exception $exception, console\Context $context = null, handlers\Exception $handler = null)
		{
			$this->context = $context;

			parent::__construct($exception, $handler);
		}

		/**
		 * Returns the Execution Context in which the Exception was thrown.
		 *
		 * @return  console\Context
		 */

		public function getContext()
		{
			return $this->context;
		}

		/**
		 * Checks whether the Execution Context in which the Exception was thrown is set.
		 *
		 * @return  bool    True when the Execution Context is set, false otherwise.
		 */

		public function hasContext()
		{
			return null !== $this->context;
		}
	}