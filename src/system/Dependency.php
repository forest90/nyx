<?php namespace nyx\system;

	/**
	 * Dependency
	 *
	 * @package     Nyx\System\Dependencies
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/system/dependencies.html
	 * @todo        Obviously, finish support for other types than functions.
	 */

	class Dependency
	{
		/**
		 * Dependency type constants.
		 */

		const TYPE_FUNCTION     = 1;
		const TYPE_CLASS        = 2;
		const TYPE_EXTENSION    = 3;
		const TYPE_CUSTOM       = 10;

		/**
		 * @var int     The type of the dependency.
		 */

		private $type;

		/**
		 * @var string  What is actually being depended upon.
		 */

		private $what;

		/**
		 * @var string  Help text for when the dependency is not met.
		 */

		private $help;

		/**
		 * Creates a new Dependency.
		 *
		 * @param   int     $type   The type of the dependency.
		 * @param   string  $what   What the actual dependency is.
		 * @param   string  $help   Optional help text.
		 */

		public function __construct($type, $what, $help = null)
		{
			$this->type = $type;
			$this->what = $what;

			// Set the help text if it was provided.
			$help and $this->setHelp($help);
		}

		/**
		 * Install the dependency.
		 *
		 * Extend this for concrete dependencies and make sure it returns true when the process was completed.
		 *
		 * @return  bool
		 */

		public function install()
		{
			return false;
		}

		/**
		 * Check if the dependency is met.
		 *
		 * @return  bool
		 */

		public function check()
		{
			switch($this->type)
			{
				case self::TYPE_FUNCTION:

					return function_exists($this->what);

				break;

				case self::TYPE_EXTENSION:

					return extension_loaded($this->what);

				break;
			}

			return false;
		}

		/**
		 * Checks if the dependency is met. Attempts to install it or throw an exception if it's not.
		 *
		 * @param   bool                        $install    Whether to attempt to install it.
		 * @return  bool
		 * @throws  exceptions\DependencyUnmet              When the dependency is not met and could not be installed.
		 */

		public function request($install = false)
		{
			// Check first, attempt to install if allowed to, throw the exception otherwise.
			if(!$this->check() and !($install and $this->install()))
			{
				throw new exceptions\DependencyUnmet($this);
			}

			return true;
		}

		/**
		 * Returns the help text for this dependency.
		 *
		 * @return  string
		 */

		public function getHelp()
		{
			// If the help text was given, just return that one.
			if($this->help) return $this->help;

			// Otherwise let's construct a default.
			switch($this->type)
			{
				case self::TYPE_FUNCTION:

					return 'The ['.$this->what.'()] function is required but unavailable.';

				break;

				case self::TYPE_EXTENSION:

					return 'The ['.$this->what.'] extension is required but unavailable.';

				break;
			}

			// Fall back to this if we didn't catch the type up to this point.
			return 'A dependency requirement was not met.';
		}

		/**
		 * Sets the help text for this dependency.
		 *
		 * @param   string  $help   Optional help text.
		 * @return  $this
		 */

		public function setHelp($help)
		{
			$this->help = $help;

			return $this;
		}
	}