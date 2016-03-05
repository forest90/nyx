<?php namespace nyx\diagnostics\debug;

	// Internal dependencies
	use nyx\diagnostics;

	/**
	 * Exception Inspector
	 *
	 * The Exception being inspected and the (optional) Handler are immutable. It you do not set the Handler during
	 * construction of the Inspector it will not be set for the whole lifecycle of the Inspector.
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.0.3
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	class Inspector
	{
		/**
		 * @var \Exception  The Exception that is to be inspected.
		 */

		private $exception;

		/**
		 * @var Trace   A Trace instance.
		 */

		private $trace;

		/**
		 * @var handlers\Exception  The Exception Handler currently handling the inspected Exception.
		 */

		private $handler;

		/**
		 * Prepares a new Inspector by feeding him with an Exception that shall be inspected and the Handler which
		 * started the inspection.
		 *
		 * @param   \Exception          $exception  The Exception that is to be inspected.
		 * @param   handlers\Exception  $handler
		 */

		public function __construct(\Exception $exception, handlers\Exception $handler = null)
		{
			$this->exception = $exception;
			$this->handler   = $handler;
		}

		/**
		 * Returns the Exception currently being inspected.
		 *
		 * @return  \Exception  The Exception being inspected.
		 */

		public function getException()
		{
			return $this->exception;
		}

		/**
		 * Returns the Exception Handler currently handling the inspected Exception.
		 *
		 * @return  handlers\Exception
		 */

		public function getHandler()
		{
			return $this->handler;
		}

		/**
		 * Returns a Trace instance representing the stack trace of the inspected Exception.
		 *
		 * If the trace contains a handlers\Error entry (indicating the Exception is the result of an internal
		 * error -> exception conversion), that frame will be removed. If it does not, a frame for the actual Exception
		 * will be prepended to the frame stack instead to make it easier to iterate over the causality chain.
		 *
		 * @return  Trace
		 */

		public function getTrace()
		{
			// No need for further magic if we've already instantiated a Frame Iterator.
			if($this->trace !== null) return $this->trace;

			// The frames might differ from the actual trace after we are done. See below.
			$frames = $this->exception->getTrace();

			// If the Exception we are inspecting is one of our internal ones, there's a good chance it stems from
			// a casual error which got converted. If the first trace frame denotes no file line, it's a good indicator
			// that is indeed so, so let's filter out the error handler from the trace.
			if($this->exception instanceof exceptions\Error and empty($frames[0]['line']))
			{
				// Grab more info about Fatal Errors when we've got acess to XDebug.
				if($this->exception instanceof exceptions\FatalError and function_exists('xdebug_get_function_stack'))
				{
					$frames = array_slice(xdebug_get_function_stack(), 4);

					foreach($frames as $i => $frame)
					{
						// XDebug pre 2.1.1 doesn't currently set the call type key http://bugs.xdebug.org/view.php?id=695
						if(!isset($frame['type'])) $trace[$i]['type'] = '??';

						if('dynamic' === $frames[$i]['type'])
						{
							$trace[$i]['type'] = '->';
						}
						elseif('static' === $frames[$i]['type'])
						{
							$trace[$i]['type'] = '::';
						}

						// XDebug also has a different name for the args array.
						if(isset($frame['params']) and !isset($frame['args']))
						{
							$trace[$i]['args'] = $frame['params'];
							unset($frames[$i]['params']);
						}
					}
				}
				// Otherwise only remove the Error Handler from the trace.
				else
				{
					array_shift($frames);
				}
			}
			// Alright, casual Exception it is.
			elseif(empty($frames[0]['line']))
			{
				$frames[0] = array_merge(diagnostics\Debug::exceptionToArray($this->exception), $frames[0]);
			}
			else
			{
				array_unshift($frames, diagnostics\Debug::exceptionToArray($this->exception));
			}

			// Instantiate a new Frame Iterator and cache it locally.
			return $this->trace = new Trace($frames);
		}
	}