<?php namespace nyx\system\traits;

	// Internal dependencies
	use nyx\system;

	/**
	 * Dependent
	 *
	 * A Dependent object is one that has dependencies which need to be met under any and all circumstances
	 * for it to work properly. Note that this is meant for use with system dependencies, like php extensions
	 * or specific functions. It is merely a tool for ensuring the system dependencies *can* be met but has nothing
	 * to do with dependency injections.
	 *
	 * For performance reasons this should not be used during HTTP request handling or other situations where
	 * performance is desired. It is mostly useful for auxiliary utilities (like the console apps) to check
	 * whether objects which expose their dependencies can work properly.
	 *
	 * @package     Nyx\System\Dependencies
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/system/dependencies.html
	 */

	trait Dependent
	{
		/**
		 * @var system\Dependency[]     An array of Dependency objects.
		 */

		private $dependencies;

		/**
		 * @var bool    Simple boolean flag which gets set to true by the depend() method once *any* dependency is met.
		 *              Unmet dependencies will throw exception when checked using said method so whenever this static
		 *              variable is set to true, checks can be skipped for a single request unless you catch the
		 *              exceptions manually and don't set the flag to false manually as well.
		 */

		private static $dependenciesMet;

		/**
		 * Add a dependency and check if it's met.
		 *
		 * @param   int|system\Dependency               $type       The type of the dependency or a Dependency object.
		 * @param   string                              $what       What the actual dependency is.
		 * @param   string                              $help       Optional help text.
		 * @param   bool                                $install    Whether to attempt to install the dependency.
		 * @return  bool                                            True when the check succeeds, an exception otherwise.
		 * @throws  system\exceptions\DependencyUnmet               When the dependency requirement was not met.
		 */

		public function dependsOn($type, $what, $help = null, $install = false)
		{
			// If a instance was passed, we're just gonna use that one. Store a reference to it right away as well.
			$this->dependencies[] = $dependency = $type instanceof system\Dependency ? $type : new system\Dependency($type, $what);

			// Set the help text even if we already had an instance, since the user might want to have it
			// overwritten.
			$help and $dependency->setHelp($help);

			// Do the actual lifting.
			return self::$dependenciesMet = $dependency->request($install);
		}

		/**
		 * Return the dependencies of the instance this trait is used in.
		 *
		 * @return  system\Dependency[]     An array of Dependency instances.
		 */

		public function getDependencies()
		{
			return $this->dependencies;
		}
	}