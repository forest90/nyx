<?php namespace nyx\console\interfaces;

	// Internal dependencies
	use nyx\console;

	/**
	 * Input Interface
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	interface Input
	{
		/**
		 * Returns the Arguments Bag in use.
		 *
		 * @return  console\input\bags\Arguments
		 */

		public function arguments();

		/**
		 * Returns the Options Bag in use.
		 *
		 * @return  console\input\bags\Options
		 */

		public function options();

		/**
		 * Returns the master Definition bound to this Input instance.
		 *
		 * @return  console\input\Definition
		 */

		public function definition();

		/**
		 * Returns a Input Tokens instance containing the raw input.
		 *
		 * @return  input\Tokens
		 */

		public function raw();

		/**
	     * Binds the Input to the given Input Definition, effectively converting raw input into named parameters,
		 * validating them and executing their binding callbacks if applicable.
	     *
	     * @param   console\input\Definition    $definition     An input Definition instance.
		 * @param   console\Context             $context        The Execution Context the binding shall be performed in.
		 * @return  $this
	     */

	    public function bind(console\input\Definition $definition, console\Context $context = null);

		/**
		 * Sets whether the Input can be used interactively.
		 *
		 * @param   bool    $interactive    Whether the input should be interactive.
		 * @return  $this
		 */

		public function setInteractive($interactive);

	    /**
	     * Checks whether the Input can be used interactively.
	     *
	     * @return  bool    True when the Input can be used interactively, false otherwise.
	     */

	    public function isInteractive();
	}