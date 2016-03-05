<?php namespace nyx\core\promises\types;

	// External dependencies
	use nyx\utils;

	// Internal dependencies
	use nyx\core\promises\interfaces;

	/**
	 * Rejected Promise
	 *
	 * @package     Nyx\Core\Promises
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/promises.html
	 */

	class Rejected implements interfaces\Promise
	{
		/**
		 * @var mixed   The reason why the Promise got rejected.
		 */

		private $reason;

		/**
		 * Constructs a new Rejected Promise.
		 *
		 * @param   mixed   $reason     The reason why the Promise got rejected.
		 */

		public function __construct($reason = null)
		{
			$this->reason = $reason;
		}

		/**
		 * {@inheritDoc}
		 */

		public function then(callable $fulfilled = null, callable $error = null, callable $progress = null)
		{
			try
			{
				// When an error handler us given we will continue to reject with the current Rejected Promise.
				// Otherwise we will resolve the return value of the error handler and return it.
				return null !== $error ? utils\When::resolve(call_user_func($error, $this->reason)) : $this;
			}
			// Reject the Promise with the Exception instead of throwing it.
			catch(\Exception $exception)
			{
				return new static($exception);
			}
		}

		/**
		 * Returns the reason why the Promise got rejected.
		 *
		 * @return  mixed
		 */

		public function getReason()
		{
			return $this->reason;
		}
	}