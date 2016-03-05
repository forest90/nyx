<?php namespace nyx\diagnostics\profiling\collectors;

	// Internal dependencies
	use nyx\diagnostics\debug;
	use nyx\diagnostics\profiling;

	/**
	 * Exception Data Collector
	 *
	 * Analyzes an Exception contained in the Profiling Context given and provides serializable information about it
	 * in form of a debug/Trace containing debug/Frame instances. The Exception itself will also be converted to
	 * a debug\Frame instance to use its serialization features.
	 *
	 * @package     Nyx\Diagnostics\Profiling
	 * @version     0.0.2
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/profiling/index.html
	 * @todo        Inject the Inspector?
	 */

	class Exception extends profiling\Collector
	{
		/**
		 * {@inheritDoc}
		 */

		public function __construct($name = 'exception')
		{
			parent::__construct($name);
		}

		/**
		 * {@inheritDoc}
		 */

		public function collect(profiling\Context $context = null)
		{
			if($context and null !== $exception = $context->getException())
			{
				$this->data =
				[
					'exception' => new debug\exceptions\Flattened($exception)
				];
			}
		}

		/**
		 * Checks if an Exception has been collected and is available to this Collector.
		 *
		 * @return  bool    True when the the Exception is available, false otherwise.
		 */

		public function hasException()
		{
			return isset($this->data['exception']);
		}

		/**
		 * Returns the collected Exception.
		 *
		 * @return  debug\exceptions\Flattened
		 */

		public function getException()
		{
			return $this->hasException() ? $this->data['exception'] : null;
		}

		/**
		 * Returns the message of the Exception.
		 *
		 * @return  string
		 */

		public function getMessage()
		{
			return $this->hasException() ? $this->data['exception']->getMessage() : null;
		}

		/**
		 * Returns the code of the Exception.
		 *
		 * @return  int
		 */

		public function getCode()
		{
			return $this->hasException() ? $this->data['exception']->getCode() : null;
		}

		/**
		 * Returns the stack trace of the Exception.
		 *
		 * @return  debug\Trace
		 */

		public function getTrace()
		{
			return $this->hasException() ? $this->data['exception']->getTrace() : null;
		}
	}