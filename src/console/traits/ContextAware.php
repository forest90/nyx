<?php namespace nyx\console\traits;

	// Internal dependencies
	use nyx\console\interfaces;
	use nyx\console;

	/**
	 * Context Aware
	 *
	 * Allows for the implementation of the interfaces\ContextAware interface.
	 *
	 * @package     Nyx\Console
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/index.html
	 */

	trait ContextAware
	{
		/**
		 * @var console\Context   The Context instance in use by the exhibitor of this trait.
		 */

		private $context;

		/**
		 * @see interfaces\ContextAware::getContext()
		 */

		public function getContext()
		{
			return $this->context;
		}

		/**
		 * @see interfaces\ContextAware::setContext()
		 */

		public function setContext(console\Context $context)
		{
			$this->context = $context;

			return $this;
		}

		/**
		 * @see interfaces\output\Aware::hasContext()
		 */

		public function hasContext()
		{
			return null !== $this->context;
		}
	}