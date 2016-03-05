<?php namespace nyx\diagnostics\debug\types;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * Structure
	 *
	 * Base class for structures (arrays and objects),
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	abstract class Structure extends debug\Type
	{
		/**
		 * @var int     The nesting level limit.
		 */

		private $nestingLimit;

		/**
		 * @var bool    Whether the Structure is terminal, ie. at the nesting limit.
		 */

		private $terminal;

		/**
		 * {@inheritDoc}
		 *
		 * @param   int $nestingLimit   The nesting level limit.
		 */

		public function __construct($value, $level = 0, $nestingLimit = 10)
		{
			parent::__construct($value, $level);

			$this->nestingLimit = $nestingLimit;
			$this->terminal     = $this->level >= $this->nestingLimit;
		}

		/**
		 * Checks whether the array is terminal, ie. at the nesting limit.
		 *
		 * @return  bool
		 */

		public function isTerminal()
		{
			return $this->terminal;
		}
	}