<?php namespace nyx\diagnostics\profiling\collectors;

	// Internal dependencies
	use nyx\diagnostics\profiling;

	/**
	 * Configuration Data Collector
	 *
	 * @package     Nyx\Diagnostics\Profiling
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/profiling/index.html
	 * @todo        Extend with kernel (name, version, env, debug), app (name, version), bundle (list) etc. information.
	 */

	class Config extends profiling\Collector
	{
		/**
		 * {@inheritDoc}
		 */

		public function __construct($name = 'config')
		{
			parent::__construct($name);
		}

		/**
		 * {@inheritDoc}
		 */

		public function collect(profiling\Context $context = null)
		{
			$this->data =
			[
				'php' =>
				[
					'version'     => PHP_VERSION,
					'extensions'  => get_loaded_extensions(),
					'sapi'        => php_sapi_name(),
					'opcaches' =>
					[
						'apc'          => extension_loaded('apc') and ini_get('apc.enabled'),
						'eaccelerator' => extension_loaded('eaccelerator') and ini_get('eaccelerator.enable'),
						'wincache'     => extension_loaded('wincache') and ini_get('wincache.ocenabled'),
						'xcache'       => extension_loaded('xcache') and ini_get('xcache.cacher'),
						'zend'         => extension_loaded('Zend OPcache') and ini_get('opcache.enable'),
					]
				]
			];
		}

		/**
		 * Returns the version of PHP.
		 *
		 * @return  string
		 */

		public function getPhpVersion()
		{
			return $this->data['php']['version'];
		}

		/**
		 * Returns an array of the extensions currently loaded by PHP.
		 *
		 * @return  array
		 */

		public function getPhpExtensions()
		{
			return $this->data['php']['extensions'];
		}

		/**
		 * Returns true if the XDebug is enabled, false otherwise.
		 *
		 * @return  bool
		 */

		public function hasXDebug()
		{
			return in_array('xdebug', $this->data['php']['extensions']);
		}

		/**
		 * Returns true if the APC is enabled, false otherwise.
		 *
		 * @return  bool
		 */

		public function hasApc()
		{
			return $this->data['php']['opcaches']['apc'];
		}

		/**
		 * Returns true if the eAccelerator is enabled, false otherwise.
		 *
		 * @return  bool
		 */

		public function hasEAccelerator()
		{
			return $this->data['php']['opcaches']['eaccelerator'];
		}

		/**
		 * Returns true if the WinCache is enabled, false otherwise.
		 *
		 * @return  bool
		 */

		public function hasWinCache()
		{
			return $this->data['php']['opcaches']['wincache'];
		}

		/**
		 * Returns true if the XCache is enabled, false otherwise.
		 *
		 * @return  bool
		 */

		public function hasXCache()
		{
			return $this->data['php']['opcaches']['xcache'];
		}

		/**
		 * Returns true if the Zend OPCache is enabled, false otherwise.
		 *
		 * @return  bool
		 */

		public function hasZendOpcache()
		{
			return $this->data['php']['opcaches']['zend'];
		}

		/**
		 * Checks if any opcode cache is currently enabled.
		 *
		 * @return  bool    True if any opcode cache is enabled, false otherwise.
		 */

		public function hasOpcodeCache()
		{
			// Simply loop through all opcaches we know of and return true on the first enabled one.
			foreach($this->data['php']['opcaches'] as $enabled)
			{
				if($enabled) return true;
			}

			return false;
		}

		/**
		 * Returns the name of the opcode cache currently in use or null if none is being used.
		 *
		 * @return  string
		 */

		public function getOpcodeCacheName()
		{
			// Loop through all known opcaches and return the name of the first enabled one found. If you happen to
			// have more than one opcode cache loaded and enabled... you're doing something terribly wrong.
			foreach($this->data['php']['opcaches'] as $name => $enabled)
			{
				if($enabled) return $name;
			}

			return null;
		}

		/**
		 * Returns the name of the PHP SAPI currently in use.
		 *
		 * @return  string
		 */

		public function getSapiName()
		{
			return $this->data['php']['sapi'];
		}
	}