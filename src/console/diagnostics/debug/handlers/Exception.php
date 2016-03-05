<?php namespace nyx\console\diagnostics\debug\handlers;

	// External dependencies
	use nyx\diagnostics\debug;

	// Internal dependencies
	use nyx\console;

	/**
	 * Console Exception Handler
	 *
	 * Extends the base Exception Handler in order to become Execution Context aware and utilize our custom Inspector
	 * {@see console\diagnostics\Inspector} which is also aware of said Context.
	 *
	 * @package     Nyx\Console\Diagnostics\Debug
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/diagnostics.html
	 */

	class Exception extends debug\handlers\Exception
	{
		/**
		 * @var console\Context     The current Execution Context.
		 */

		private $context;

		/**
		 * Sets the current Execution Context.
		 *
		 * @param   console\Context     $context
		 */

		public function setContext(console\Context $context)
		{
			$this->context = $context;
		}

		/**
		 * {@inheritDoc}
		 */

		protected function inspect(\Exception $exception)
		{
			return new console\diagnostics\debug\Inspector($exception, $this->context, $this);
		}
	}