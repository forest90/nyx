<?php namespace nyx\diagnostics\profiling;

	// External dependencies
	use nyx\core;

	/**
	 * Profiling Context
	 *
	 * A generic profiling Context contains arbitrary data of mixed types that can be accessed using {@see self::get()}
	 * and an optional Exception since that is the only common denominator across concrete Contexts. The Context itself
	 * does not get serialized and saved during profiling - it is only passed along to the Collectors for them to base
	 * their data collection on.
	 *
	 * It is read-only be default - all data is passed to the Context during construction and for consistency across
	 * the various Collectors that can be utilized should not be modified in any way unless you are absolutely sure
	 * you know what you are doing.
	 *
	 * @package     Nyx\Diagnostics\Profiling
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/profiling/index.html
	 * @todo        Read-only ArrayAccess?
	 */

	class Context
	{
		/**
		 * @var array   An array of arbitrary context data.
		 */

		private $data;

		/**
		 * @var \Exception  An Exception.
		 */

		private $exception;

		/**
		 * Constructs a new Context.
		 *
		 * @param   array       $data       An array of arbitrary context data.
		 * @param   \Exception  $exception  An Exception.
		 */

		public function __construct(array $data = null, \Exception $exception = null)
		{
			$this->data      = $data;
			$this->exception = $exception;
		}

		/**
		 * Returns an element defined by its key.
		 *
		 * @param   string      $key            The element's key.
		 * @return  mixed
		 * @throws  \InvalidArgumentException   When the element doesn't exist.
		 */

		public function get($key)
		{
			// First let's check if the element exists.
			if(!$this->has($key)) throw new \InvalidArgumentException("The element [$key] is not defined.");

			return $this->data[$key];
		}

		/**
		 * Returns true if an item identified by the given key exists in the Context, false otherwise.
		 *
		 * @param   string  $key
		 * @return  bool
		 */

		public function has($key)
		{
			return array_key_exists($key, $this->data);
		}

		/**
		 * Returns the Exception contained in this Context, if set.
		 *
		 * @return  \Exception|null
		 */

		public function getException()
		{
			return $this->exception;
		}

		/**
		 * Checks if this Context contains an Exception.
		 *
		 * @return  bool    True if the Context contains an Exception, false otherwise.
		 */

		public function hasException()
		{
			return null !== $this->exception;
		}
	}
