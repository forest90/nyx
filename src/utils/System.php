<?php namespace nyx\utils;

	// External dependencies
	use nyx\core;

	/**
	 * System
	 *
	 * Utilities related to the operation system PHP is running on, retrieving information about and executing
	 * system processes etc.
	 *
	 * Requires:
	 * - Function: shell_exec() (getting the shells available on this system)
	 * - Function: exec() (checking whether TTY is available on this system)
	 *
	 * @package     Nyx\Utils\System
	 * @version     0.0.3
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/utils/system.html
	 */

	class System
	{
		/**
		 * The traits of the System class.
		 */

		use core\traits\StaticallyExtendable;

		/**
		 * Platform constants.
		 */

		const TYPE_UNIX    = 1;
		const TYPE_WINDOWS = 2;
		const TYPE_BSD     = 3;
		const TYPE_CYGWIN  = 4;
		const TYPE_DARWIN  = 5;

		/**
		 * @var int     The platform PHP is running on.
		 */

		private static $platform;

		/**
		 * @var array   An array of shell names and the paths to their binaries once populated or false when PHP is
		 *              running on a system that does not support them (Windows).
		 */

		private static $shells;

		/**
		 * @var bool    Whether this platform has the 'stty' binary (always false on Windows).
		 */

		private static $hasStty;

		/**
		 * Guesses and returns the platform PHP is running on. If it can't be determined, the default of Unix will
		 * be returned.
		 *
		 * @return  int     One of the platform TYPE_ constants defined in this class.
		 */

		public static function getPlatform()
		{
			// Return the cached result if it's already available.
			if(null !== static::$platform) return static::$platform;

			$os = strtolower(php_uname("s"));

			// Check in order of likeliness.
			if(false !== strpos($os, 'unix'))   return static::$platform = self::TYPE_UNIX;
			if(0 === strpos($os, 'win'))        return static::$platform = self::TYPE_WINDOWS;
			if(false !== strpos($os, 'bsd'))    return static::$platform = self::TYPE_BSD;
			if(false !== strpos($os, 'cygwin')) return static::$platform = self::TYPE_CYGWIN;
			if(false !== strpos($os, 'darwin')) return static::$platform = self::TYPE_DARWIN;

			// Use the default otherwise.
			return static::$platform = self::TYPE_UNIX;
		}

		/**
		 * Checks whether PHP is running on a Unix platform
		 *
		 * @return  bool    True when PHP is running on a Unix platform, false otherwise.
		 */

		public static function isUnix()
		{
			// Return the cached result if it's already available.
			if(null !== static::$platform) return static::$platform === self::TYPE_UNIX;

			return static::getPlatform() === self::TYPE_UNIX;
		}

		/**
		 * Checks whether PHP is running on a Windows platform
		 *
		 * @return  bool    True when PHP is running on a Windows platform, false otherwise.
		 */

		public static function isWindows()
		{
			// Return the cached result if it's already available.
			if(null !== static::$platform) return static::$platform === self::TYPE_WINDOWS;

			return static::getPlatform() === self::TYPE_WINDOWS;
		}

		/**
		 * Checks whether PHP is running on a BSD platform
		 *
		 * @return  bool    True when PHP is running on a BSD platform, false otherwise.
		 */

		public static function isBsd()
		{
			// Return the cached result if it's already available.
			if(null !== static::$platform) return static::$platform === self::TYPE_BSD;

			return static::getPlatform() === self::TYPE_BSD;
		}

		/**
		 * Checks whether PHP is running on a Cygwin platform
		 *
		 * @return  bool    True when PHP is running on a Cygwin platform, false otherwise.
		 */

		public static function isCygwin()
		{
			// Return the cached result if it's already available.
			if(null !== static::$platform) return static::$platform === self::TYPE_CYGWIN;

			return static::getPlatform() === self::TYPE_CYGWIN;
		}

		/**
		 * Checks whether PHP is running on a Darwin platform
		 *
		 * @return  bool    True when PHP is running on a Darwin platform, false otherwise.
		 */

		public static function isDarwin()
		{
			// Return the cached result if it's already available.
			if(null !== static::$platform) return static::$platform === self::TYPE_DARWIN;

			return static::getPlatform() === self::TYPE_DARWIN;
		}

		/**
		 * Checks whether a shell of the given name is available in the system.
		 *
		 * @param   string  $name
		 * @return  bool
		 */

		public static function hasShell($name)
		{
			return isset(static::getShells()[$name]);
		}

		/**
		 * Returns the path to the given shell's binary or false when it is not available.
		 *
		 * @param   string          $name
		 * @return  string|bool
		 */

		public static function getShell($name)
		{
			$shells = static::getShells();

			return isset($shells[$name]) ? $shells[$name] : false;
		}

		/**
		 * Returns an array of shell names and the paths to their binaries once populated or false when PHP is
		 * running on a system that does not support them (Windows).
		 *
		 * @return  array|bool
		 */

		public static function getShells()
		{
			// Return the cached result if it's already available.
			if(static::$shells !== null) return static::$shells;

			// Definitely no shells on Windows.
			if(static::isWindows()) return static::$shells = false;

			// Ensure this method will be ran once at most.
			static::$shells = [];

			if(file_exists($file = '/etc/shells'))
			{
				$cat = trim(shell_exec('cat '.$file.' 2> /dev/null'));

				foreach(explode(PHP_EOL, $cat) as $path)
				{
					// Ignore this line if it doesn't begin with a filepath.
					if($path[0] != '/') continue;

					$name = substr($path, strrpos($path, '/') + 1);

					static::$shells[$name] = $path;
				}
			}

			return static::$shells;
		}

		/**
		 * Checks whether this platform has the 'stty' binary.
		 *
		 * @return  bool    True when 'stty' is available on this platform, false otherwise (always false on Windows).
		 */

		public static function hasStty()
		{
			// Return the cached result if it's already available.
			if(static::$hasStty !== null) return static::$hasStty;

			// Definitely no Stty on Windows.
			if(static::isWindows()) return static::$hasStty = false;

			// Run a simple exec() call and check whether it returned with an error code.
			exec('/usr/bin/env stty', $output, $exitCode);

			return static::$hasStty = $exitCode === 0;
		}

		/**
		 * Escapes a string in a way to make it usable as a command line argument.
		 *
		 * @param   string  $argument   The argument to escape.
		 * @return  string              The escaped argument,
		 */

		public static function escapeArgument($argument)
		{
			// Fix for PHP bug #49446 {@see https://bugs.php.net/bug.php?id=49446ć
			// Fix for PHP bug #43784 {@see https://bugs.php.net/bug.php?id=43784}
			if(static::isWindows())
			{
				$escapedArgument = '';

				foreach(preg_split('/([%"])/i', $argument, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE) as $part)
				{
					if('"' == $part)
					{
						$escapedArgument .= '\\"';
					}
					elseif('%' == $part)
					{
						$escapedArgument .= '^%';
					}
					else
					{
						$escapedArgument .= escapeshellarg($part);
					}
				}

				return $escapedArgument;
			}

			return escapeshellarg($argument);
		}
	}