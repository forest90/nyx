<?php namespace nyx\console\output\styles;

	// Internal dependencies
	use nyx\console\interfaces;
	use nyx\console\output;

	/**
	 * Styles Stack
	 *
	 * @package     Nyx\Console\Output
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/output.html
	 */

	class Stack
	{
		/**
		 * @var interfaces\output\Style[]   The Style instances currently being processed.
		 */

		private $styles;

		/**
		 * @var interfaces\output\Style     The default Style to be used when the Stack is empty.
		 */

		private $default;

		/**
		 * Constructor.
		 *
		 * @param   interfaces\output\Style   $default
		 */

		public function __construct(interfaces\output\Style $default = null)
		{
			// Make sure we've got a default Style since we might fall back to it quite often.
			$this->default = $default ?: new output\Style;
			$this->styles  = [];
		}

		/**
		 * Pushes a style in the stack.
		 *
		 * @param   interfaces\output\Style   $style
		 */

		public function push(interfaces\output\Style $style)
		{
			$this->styles[] = $style;
		}

		/**
		 * Pops a style from the Stack, effectively ending its processing until it gets pushed anew.
		 *
		 * @param   interfaces\output\Style     $style  An optional, specific Style to pop from the Stack. If it is not
		 *                                              the last (ie. current) element in the Stack, the Stack will be
		 *                                              sliced and all Styles present after this instance will also
		 *                                              be popped.
		 * @return  interfaces\output\Style
		 * @throws  \InvalidArgumentException           When a Style was given but couldn't be found in the Stack.
		 */

		public function pop(interfaces\output\Style $style = null)
		{
			// If we've got no styles in the stack, return the default one.
			if(!$this->count()) return $this->default;

			// If no Style object was given, perform a casual array pop.
			if($style === null) return array_pop($this->styles);

			// Otherwise we need to compare the Styles.
			foreach(array_reverse($this->styles, true) as $index => $stackedStyle)
			{
				// Performing a strict match instead of comparing the results should work just fine in most use cases
				// as the assumption is that having differently named Styles which apply the same visuals is pointless,
				// but it needs some more testing.
				// if($style->apply('') === $stackedStyle->apply(''))
				if($style === $stackedStyle)
				{
					$this->styles = array_slice($this->styles, 0, $index);

					return $stackedStyle;
				}
			}

			throw new \InvalidArgumentException("Incorrectly nested style tag found.");
		}

		/**
		 * Returns the current, top-most Style in the stack, or the default if none is being processed.
		 *
		 * @return  output\Style
		 */

		public function current()
		{
			return $this->count() ? end($this->styles) : $this->default;
		}

		/**
		 * Resets the stack (ie. empties internal arrays).
		 */

		public function reset()
		{
			$this->styles = [];
		}

		/**
		 * Returns the count of the Styles in the Stack, not taking the default Style into account.
		 *
		 * @return  int
		 */

		public function count()
		{
			return count($this->styles);
		}

		/**
		 * Sets the default Style to be used when the Stack is empty.
		 *
		 * @param   interfaces\output\Style   $default
		 * @return  $this
		 */

		public function setDefault(interfaces\output\Style $default)
		{
			$this->default = $default;

			return $this;
		}

		/**
		 * Returns the default Style.
		 *
		 * @return  interfaces\output\Style
		 */

		public function getDefault()
		{
			return $this->default;
		}
	}