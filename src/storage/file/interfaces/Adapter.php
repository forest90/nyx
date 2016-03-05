<?php namespace nyx\storage\file\interfaces;

	/**
	 * Filesystem Adapter Interface
	 *
	 * Generic interface for all filesystem adapters which should provide facilities for basic R/W operations on the
	 * filesystem and allow the retrieval of basic information about the files located thereon.
	 *
	 * @package     Nyx\Storage\File
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/storage/file/adapters.html
	 */

	interface Adapter
	{
		/**
		 * Reads and returns the contents of a file.
		 *
		 * @param   string  $key    The key of the file to read.
		 * @return  string          The contents of the file or false it the file couldn't be read.
		 */

		public function read($key);

		/**
		 * Writes the given contents into a file.
		 *
		 * @param   string  $key        The key of the file to write to.
		 * @param   string  $contents   The contents to write.
		 * @param   array   $metadata   The metadata about the file to insert, assuming the Adapter supports this.
		 * @return  int                 The number of bytes that have been written.
		 * @return  bool                True when the operation succeeded, false otherwise.
		 */

		public function write($key, $contents, array $metadata = null);

		/**
		 * Checks whether the given file exists.
		 *
		 * @param   string  $key    The key of the file to check.
		 * @return  bool            True when the given file exists, false otherwise.
		 */

		public function exists($key);

		/**
		 * Deletes the given file.
		 *
		 * @param   string  $key        The key of the file to delete.
		 * @return  bool                True when the operation succeeded, false otherwise.
		 */

		public function delete($key);

		/**
		 * Renames the given file, effectively moving it to the destination location.
		 *
		 * @param   string  $key            The key of the file to move.
		 * @param   string  $destination    The destination key of the file being moved.
		 * @param   int     $options        The option flags to use for moving (only supported by some of the adapters).
		 * @return  bool                    True when the operation succeeded, false otherwise.
		 */

		public function move($key, $destination, $options = null);

		/**
		 * Creates a copy of the given file at the destination location.
		 *
		 * @param   string  $key            The key of the file to copy.
		 * @param   string  $destination    The key where a copy of the file should be created at.
		 * @param   int     $options        The option flags to use for copying (only supported by some of the adapters).
		 * @return  bool                    True when the operation succeeded, false otherwise.
		 */

		public function copy($key, $destination, $options = null);

		/**
		 * Returns the time in seconds the given file was last modified at.
		 *
		 * @param   string      $key    The key of the file to check.
		 * @return  int|bool            The time in seconds the file was last modified at or false if the time could
		 *                              not be fetched.
		 */

		public function mtime($key);

		/**
		 * Returns the size of the given file in bytes.
		 *
		 * @param   string      $key    The key of the file to check.
		 * @return  int|bool            The size of the given file in bytes or false if the size could not be fetched.
		 */

		public function size($key);
	}