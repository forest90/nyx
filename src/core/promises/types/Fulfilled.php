<?php namespace nyx\core\promises\types;

	// External dependencies
	use nyx\utils;

	// Internal dependencies
	use nyx\core\promises\interfaces;

	/**
	 * Fulfilled Promise
	 *
	 * @package     Nyx\Core\Promises
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/promises.html
	 */

	class Fulfilled implements interfaces\Promise
	{
		/**
		 * @var mixed   The result of the Promise.
		 */

		private $result;

		/**
		 * Constructs a new Fulfilled Promise.
		 *
		 * @param   mixed   $result     The result of the Promise.
		 */

		public function __construct($result = null)
		{
			$this->result = $result;
		}

		/**
		 * {@inheritDoc}
		 */

		public function then(callable $fulfilled = null, callable $error = null, callable $progress = null)
		{
			try
			{
				// When a fulfilled handler is given, invoke it with the result and returned the resolved return value.
				// Otherwise simply resolve the currently set result and return it.
				return utils\When::resolve(null !== $fulfilled ? call_user_func($fulfilled, $this->result) : $this->result);
			}
			// Reject the Promise with the Exception instead of throwing it.
			catch(\Exception $exception)
			{
				return new Rejected($exception);
			}
		}
	}