<?php namespace nyx\connect\streams\interfaces;

	// External dependencies
	use nyx\core;

	/**
	 * Stream Interface
	 *
	 * @package     Nyx\Connect\Streams
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/connect/streams.html
	 */

	interface Stream extends core\interfaces\Stringable
	{
		/**
		 * Status/mode bits of the Stream.
		 */

		const LOCAL    = 1;
		const READABLE = 2;
		const WRITABLE = 4;
		const SEEKABLE = 8;
		const BLOCKED  = 16;

		/**
		 * Returns the underlying stream resource.
		 *
		 * @return  resource
		 */

		public function expose();

		/**
		 * Reads data from the underlying stream up to the given length of bytes.
		 *
		 * @param   int             $length     The amount of bytes that should be read.
		 * @return  string|bool                 The data read from the stream, otherwise false on failure or upon
		 *                                      reaching the end of the stream (EOF).
		 */

		public function read($length);

		/**
		 * Reads a line of data from the underlying stream up to the given length of bytes.
		 *
		 * @param   int             $length     The amount of bytes that should be read.
		 * @return  string|bool                 The data read from the stream, otherwise false on failure or upon
		 *                                      reaching the end of the stream (EOF).
		 */

		public function line($length = null);

		/**
		 * Writes the given data to the underlying stream.
		 *
		 * @param   string      $data   The data to write.
		 * @return  int|bool            The number of bytes written to the stream, otherwise false on failure.
		 */

		public function write($data);

		/**
		 * Seeks to the specified position in the underlying stream, ie. moves the file pointer to the given offset.
		 *
		 * @param   int     $position   The position to seek to.
		 * @param   int     $whence     How the offset should be applied.
		 * @return  bool                True on success, false otherwise.
		 */

		public function seek($position, $whence = SEEK_SET);

		/**
		 * Moves the pointer in the stream to the beginning. Equivalent of calling self::seek(0, SEEK_SET).
		 *
		 * @return  bool    True on success, false otherwise.
		 */

		public function rewind();

		/**
		 * Closes the underlying stream.
		 *
		 * @return  $this
		 */

		public function close();

		/**
		 * Returns the current position of the file pointer.
		 *
		 * @return  int|bool    The position of the file pointer or false on error.
		 */

		public function getPosition();

		/**
		 * Checks whether the underlying stream has been consumed, ie. whether the EOF has been reached.
		 *
		 * @return  bool
		 */

		public function isConsumed();

		/**
		 * Returns the metadata of the stream resource.
		 *
		 * @param   string              $key    The specific key that should be fetched.
		 * @return  array|mixed|null
		 */

		public function getMetadata($key = null);

		/**
		 * Returns the size of the stream if able to.
		 *
		 * @return  int|bool
		 */

		public function getSize();
	}