<?php namespace nyx\diagnostics\debug\exceptions;

	// External dependencies
	use nyx\core;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * Flattened Exception
	 *
	 * Wraps another Exception in order to make the stack Trace serializable and provide a single-dimension array of
	 * all previous Exceptions. This class *does not* actually extend the base Exception class.
	 *
	 * Internally instantiates a debug\Inspector to create a debug\Trace consisting of debug\Frame instances. Please
	 * note that said Trace will contain the Exception itself as the first element in the trace, which is not
	 * consistent with how a casual getTrace() works.
	 *
	 * The class is immutable (all setters are private) except for unserializing the data which technically allows
	 * to bypass the limitation, so be aware of the quirks.
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	class Flattened implements \Serializable, \JsonSerializable, core\interfaces\Arrayable, core\interfaces\Jsonable
	{
		/**
		 * @var \Exception  The underlying Exception. Does not get serialized directly. Used internally to build
		 *                  the debug\Trace on-demand only (during serialization or when manually requested].
		 */

		private $exception;

		/**
		 * @var string  The message of the underlying Exception.
		 */

		private $message;

		/**
		 * @var int     The code of the underlying Exception.
		 */

		private $code;

		/**
		 * @var string  The path to the file the underlying Exception had been thrown in.
		 */

		private $file;

		/**
		 * @var int     The line number of the file the underlying Exception has been thrown at.
		 */

		private $line;

		/**
		 * @var string  The class name of the underlying Exception, *not* the name of the class which resulted in the
		 *              Exception.
		 */

		private $class;

		/**
		 * @var array   The additional context data.
		 */

		private $context;

		/**
		 * @var debug\Trace     The debug\Trace for the underlying Exception.
		 */

		private $trace;

		/**
		 * @var Flattened       A Flattened instance of the previous Exception if applicable.
		 */

		private $previous;

		/**
		 * Constructs a new Flattened Exception.
		 *
		 * @param   \Exception  $exception  The Exception to wrap.
		 */

		public function __construct(\Exception $exception)
		{
			$this->exception = $exception;
			$this->setMessage($exception->getMessage());
			$this->setCode($exception->getCode());
			$this->setFile($exception->getFile());
			$this->setLine($exception->getLine());

			// Since we are wrapping an Exception, we need to provide some info about the actual underlying Exception
			// without fully exposing it.
			$this->setClass(get_class($exception));

			// Empty context for non-Error Exception instances.
			$this->setContext($exception instanceof Error ? $exception->getContext() : []);

			// Flatten recursively if the Exception contains previous Exceptions.
			if($previous = $exception->getPrevious()) $this->setPrevious(new self($previous));
		}

		/**
		 * Returns the message of the underlying Exception.
		 *
		 * @return  string
		 */

		public function getMessage()
		{
			return $this->message;
		}

		/**
		 * Returns the code of the underlying Exception.
		 *
		 * @return  int
		 */

		public function getCode()
		{
			return $this->code;
		}

		/**
		 * Returns the path to the file the underlying Exception has been thrown in.
		 *
		 * @return  string
		 */

		public function getFile()
		{
			return $this->file;
		}

		/**
		 * Returns the line number of the file the underlying Exception has been thrown at.
		 *
		 * @return  int
		 */

		public function getLine()
		{
			return $this->line;
		}

		/**
		 * Returns the class name of the underlying Exception, *not* the name of the class which resulted in the
		 * Exception.
		 *
		 * @return  string
		 */

		public function getClass()
		{
			return $this->class;
		}

		/**
		 * Returns the additional context data.
		 *
		 * @return  array
		 */

		public function getContext()
		{
			return $this->context;
		}

		/**
		 * Returns the debug\Trace for the underlying Exception.
		 *
		 * @return  debug\Trace
		 */

		public function getTrace()
		{
			return $this->trace ?: $this->trace = (new debug\Inspector($this->exception))->getTrace();
		}

		/**
		 * Returns a Flattened instance of the previous Exception of the underlying Exception, if applicable.
		 *
		 * @return  Flattened   A Flattened instance of the previous Exception or null if no previous Exception had
		 *                      been set in the underlying Exception.
		 */

		public function getPrevious()
		{
			return $this->previous;
		}

		/**
		 * Returns a Flattened instance of the previous Exception of the underlying Exception, if applicable.
		 *
		 * @param   Flattened   $exception  A Flattened instance of the previous Exception.
		 * @return  $this
		 */

		protected function setPrevious(Flattened $exception)
		{
			$this->previous = $exception;

			return $this;
		}

		/**
		 * Returns an array of Flattened instances of all previous Exceptions of the underlying Exception, if applicable.
		 * The resulting array will be numerically indexed in an ascending order, from the most recent previous
		 * Exception to the oldest.
		 *
		 * @return  Flattened[]
		 */

		public function getAllPrevious()
		{
			$exceptions = [];
			$e = $this;

			while($e = $e->getPrevious()) $exceptions[] = $e;

			return $exceptions;
		}

		/**
		 * {@inheritDoc}
		 */

		public function serialize()
		{
			return serialize($this->toArray());
		}

		/**
		 * {@inheritDoc}
		 */

		public function unserialize($data)
		{
			// Allow the loop below to properly create a chain of previous Exceptions.
			$next = null;

			//  This is some overhead since we're instantiating dummy Exceptions just to bypass our own constructor,
			// but d'oh.
			foreach(unserialize($data) as $exception)
			{
				$current = new self(new \Exception($exception['message'], $exception['code']));

				// We need to manually set the data since we're not actually throwing the Exceptions here.
				$current->setFile($exception['file']);
				$current->setLine($exception['line']);
				$current->setClass($exception['class']);
				$current->setTrace($exception['trace']);

				// When in at the least the second iteration, $next will be a Flattened Exception instance.
				/* @var Flattened $next */
				if($next !== null) $next->setPrevious($current);

				$next = $current;
			}
		}

		/**
		 * {@inheritDoc}
		 */

		public function jsonSerialize()
		{
			return $this->toArray();
		}

		/**
		 * {@inheritDoc}
		 *
		 * @todo    Decide: Also include the context? If yes, flatten it?
		 */

		public function toArray()
		{
			$exceptions = [];

			/* @var Flattened $exception */
			foreach(array_merge([$this], $this->getAllPrevious()) as $exception)
			{
				$exceptions[] =
				[
					'message' => $exception->getMessage(),
					'code'    => $exception->getCode(),
					'file'    => $exception->getFile(),
					'line'    => $exception->getLine(),
					'class'   => $exception->getClass(),
					'trace'   => $exception->getTrace(),
				];
			}

			return $exceptions;
		}

		/**
		 * {@inheritDoc}
		 */

		public function toJson($options = 0)
		{
			return json_encode($this->jsonSerialize(), $options);
		}

		/**
		 * Sets the message of the underlying Exception.
		 *
		 * @param   string  $message    The message to set.
		 * @return  $this
		 */

		private function setMessage($message)
		{
			$this->message = (string) $message;

			return $this;
		}

		/**
		 * Sets the code of the underlying Exception.
		 *
		 * @param   int     $code   The code to set.
		 * @return  $this
		 */

		private function setCode($code)
		{
			$this->code = (int) $code;

			return $this;
		}

		/**
		 * Sets the path to the file the underlying Exception has been thrown in.
		 *
		 * @param   string  $path   The path to set.
		 * @return  $this
		 */

		private function setFile($path)
		{
			$this->file = (string) $path;

			return $this;
		}

		/**
		 * Sets the line number of the file the underlying Exception has been thrown at.
		 *
		 * @param   int     $line   The line to set.
		 * @return  $this
		 */

		private function setLine($line)
		{
			$this->line = (int) $line;

			return $this;
		}

		/**
		 * Sets the class name of the underlying Exception, *not* the name of the class which resulted in the
		 * Exception.
		 *
		 * @param   string  $name   The class name to set.
		 * @return  $this
		 */

		private function setClass($name)
		{
			$this->class = (string) $name;

			return $this;
		}

		/**
		 * Sets the additional context data.
		 *
		 * @param   array   $data   The additional context data to set.
		 * @return  $this
		 */

		private function setContext(array $data)
		{
			$this->context = $data;

			return $this;
		}

		/**
		 * Returns the debug\Trace for the underlying Exception.
		 *
		 * @param   debug\Trace $trace  The debug\Trace to set.
		 * @return  $this
		 */

		private function setTrace(debug\Trace $trace)
		{
			$this->trace = $trace;

			return $this;
		}
	}