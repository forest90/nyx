<?php namespace nyx\diagnostics\profiling\interfaces;

	// Internal dependencies
	use nyx\diagnostics\profiling;

	/**
	 * Profiling Storage Interface
	 *
	 * @package     Nyx\Diagnostics\Profiling
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/profiling/index.html
	 */

	interface Storage
	{
		/**
		 * Reads the data associated with the given token from the storage and returns the Profile if it could
		 * be found or false otherwise.
		 *
		 * @param   string  $token              The Profile's token (identifier).
		 * @return  profiling\Profile|bool      The Profile associated with the given token or false it the Profile
		 *                                      could not be read.
		 */

		public function read($token);

		/**
		 * Writes the given Profile to the storage.
		 *
		 * @param   profiling\Profile   $profile    The Profile that should be saved.
		 * @return  bool                            True when writing the Profile succeeds, false otherwise.
		 */

		public function write(profiling\Profile $profile);

		/**
		 * Purges all data from the Storage.
		 */

		public function purge();
	}
