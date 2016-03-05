<?php namespace nyx\core;

	/**
	 * Mask
	 *
	 * Base class for concrete fields/objects utilizing bitmasks (permissions, statuses etc.). This could also be used
	 * as a generic mask builder.
	 *
	 * Note: For the sake of simplicity and to avoid overhead, as per core guidelines, this class does not perform
	 * any type checks but still expects all masks to be integers. Hopefully PHP will at some point come along with
	 * (strict!) scalar typehints to make this less of a hassle.
	 *
	 * @package     Nyx\Core
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/mask.html
	 */

	class Mask
	{
		/**
		 * @var int     The current bitmask.
		 */

		private $mask;

		/**
		 * Constructor
		 *
		 * @param   int $mask   The bitmask to start with.
		 */

		public function __construct($mask = 0)
		{
			$this->mask = (int) $mask;
		}

		/**
		 * Returns the current bitmask.
		 *
		 * @return  int
		 */

		public function get()
		{
			return $this->mask;
		}

		/**
		 * Checks if the given bits are set in the mask.
		 *
		 * @param   int     $mask
		 * @return  bool
		 */

		public function is($mask)
		{
			return ($this->mask & $mask) === $mask;
		}

		/**
		 * Sets the given bits in the mask.
		 *
		 * @param   int     $mask
		 * @return  $this
		 */

		public function set($mask)
		{
			$this->mask |= $mask;

			return $this;
		}

		/**
		 * Removes the given bits from the mask.
		 *
		 * @param   int     $mask
		 * @return  $this
		 */

		public function remove($mask)
		{
			$this->mask &= ~$mask;

			return $this;
		}

		/**
		 * Resets the mask to a state with no bits set.
		 *
		 * @return  $this
		 */

		public function reset()
		{
			$this->mask = 0;

			return $this;
		}
	}