<?php namespace nyx\console\interfaces;

	// Internal dependencies
	use nyx\console;

	/**
	 * Context Aware Interface
	 *
	 * @package     Nyx\Console
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/index.html
	 */

	interface ContextAware
	{
		/**
		 * Returns the Context instance in use by the implementer.
		 *
		 * @return  console\Context
		 */

		public function getContext();

		/**
		 * Sets a Context instance inside the implementer.
		 *
		 * @param   console\Context   $context  The Context to set.
		 * @return  $this
		 */

		public function setContext(console\Context $context);

		/**
		 * Checks whether the implementer has a set Context instance.
		 *
		 * @return  bool    True when a Context instance is set, false otherwise.
		 */

		public function hasContext();
	}