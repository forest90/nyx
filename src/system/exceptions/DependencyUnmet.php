<?php namespace nyx\system\exceptions;

	// Internal dependencies
	use nyx\system;

	/**
	 * DependencyUnmet Exception
	 *
	 * Exception thrown when a required dependency could not be met.
	 *
	 * @package     Nyx\System\Dependencies
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/system/dependencies.html
	 */

	class DependencyUnmet extends \RuntimeException
	{
		/**
		 * @var system\Dependency   The Dependency which has not been met.
		 */

		private $dependency;

		/**
		 * {@inheritDoc}
		 *
		 * @param   system\Dependency   $dependency     The Dependency which has not been met.
		 */

		public function __construct(system\Dependency $dependency, $message = null, $code = null, \Exception $previous = null)
		{
			// Make sure the Dependency is available within this exception.
			$this->dependency = $dependency;

			// Some data to feed to the base exception.
			$message = $message !== null ? $message : $dependency->getHelp();

			// Proceed to create a casual exception
			parent::__construct($message, $code, $previous);
		}

		/**
		 * Returns the Dependency which has not been met.
		 *
		 * @return  system\Dependency   The Dependency which has not been met.
		 */

		public function getDependency()
		{
			return $this->dependency;
		}
	}