<?php namespace nyx\system\call;

	// Internal dependencies
	use nyx\system;

	/**
	 * System Call I/O
	 *
	 * Responsible for managing file descriptors and pipes within a Process.
	 *
	 * Heavily based on Symfony2's Process component.
	 *
	 * @package     Nyx\System\Calls
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2013 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/system/calls.html
	 */

	class IO
	{
		/**
		 * The number of bytes forming a chunk that will be read from a file handle.
		 */

		const CHUNK_SIZE = 16384;

		/**
		 * @var array       An array of R/W pipes used by this instance.
		 */

		private $pipes = [];

		/**
		 * @var array       Windows: Workaround file names for Windows platforms.
		 */

		private $files;

		/**
		 * @var array       Windows: Workaround file handles for Windows platforms.
		 */

		private $handles;

		/**
		 * @var array       Windows: The amount of bytes that have already been read from the respective files, used
		 *                  to fseek() properly to avoid duplicating data on read.
		 */

		private $offsets;

		/**
		 * @var bool        Whether this instance is running in TTY mode.
		 */

		private $tty;

		/**
		 * @var bool        Whether this instance is running in PTY mode.
		 */

		private $pty;

		/**
		 * Constructs a new system call I/O instance.
		 *
		 * @param   bool    $useFiles   Whether to use file handles instead of read pipes (mostly used as fallback
		 *                              on Windows platforms).
		 * @param   bool    $tty        Whether to run in TTY mode.
		 * @param   bool    $pty        Whether to run in PTY mode.
		 * @throws  \RuntimeException   When asked to use file handles but a STDOUT or STDERR temporary file couldn't
		 *                              be created.
		 */

		public function __construct($useFiles = false, $tty, $pty = false)
		{
			$this->tty = (bool) $tty;
			$this->pty = (bool) $pty;

			if($useFiles) $this->openTemporaryFiles();
		}

		/**
		 * Reads data from the open pipes and file handles.
		 *
		 * @param   bool    $blocking   Whether the data should be read with blocking calls or not.
		 * @param   bool    $close      Whether pipes or file handles that reach their EOF should get automatically closed.
		 * @return  array               An array of read data indexed by its type.
		 */

		public function read($blocking = true, $close = false)
		{
			return array_replace($this->readPipes($blocking, $close), $this->readHandles($close));
		}

		/**
		 * Reads from the file handles.
		 *
		 * @param   bool    $close  Whether the handles that are finished will be automatically closed.
		 * @return  array           An array of read data indexed by its type.
		 */

		protected function readHandles($close = false)
		{
			$result = [];

			foreach($this->handles as $type => $handle)
			{
				// If unable to seek to the point where we last left off (ie. when fseek does not return 0 for success)
				// proceed to the next file handle.
				if(0 !== fseek($handle, $this->offsets[$type])) continue;

				$data = '';
				$read = null;

				// Continue reading the file handle until we reach its end.
				while(!feof($handle))
				{
					// Unless fread returns false, append the data read in this iteration to the data string.
					if(false !== $read = fread($handle, self::CHUNK_SIZE)) $data .= $read;
				}

				// Update the amount of data we've already read from the respective file so that we can fseek back to
				// the same position later on to continue reading.
				if(0 < $length = strlen($data))
				{
					$this->offsets[$type] += $length;
					$result[$type] = $data;
				}

				// If we were asked to close handles that are at their ends, let's do it.
				if(false === $read or (true === $close and feof($handle) and '' === $data))
				{
					fclose($this->handles[$type]);
					unset($this->handles[$type]);
				}
			}

			return $result;
		}

		/**
		 * Reads from the pipes.
		 *
		 * @param   bool    $blocking   Whether the data should be read with blocking calls or not.
		 * @param   bool    $close      Whether the pipes that are finished will be automatically closed.
		 * @return  array               An array of read data indexed by its type.
		 */

		protected function readPipes($blocking = true, $close = false)
		{
			// Result placeholder.
			$result = [];

			// Break away if no pipes are currently open.
			if(empty($this->pipes)) return $result;

			$r = $this->pipes;
			$w = null;
			$e = null;

			// See if anything in the stream has changed.
			if(false === $n = @stream_select($r, $w, $e, 0, $blocking ? ceil(Process::TIMEOUT_PRECISION * 1E6) : 0))
			{
				// Unless the call has simply been interrupted we need to reset the pipes, assuming an error
				// occurred.
				if(!system\Call::hasBeenInterrupted()) $this->pipes = [];

				return $result;
			}

			// If none of the streams have changed, return the result right away.
			if(0 === $n) return $result;

			foreach($r as $type => $pipe)
			{
				$data = '';
				$read = null;

				// Read the pipe in chunks until we've consumed it.
				while($read = fread($pipe, self::CHUNK_SIZE)) $data .= $read;

				// Update the result if any data has been read.
				if($data) $result[$type] = $data;

				// If we were asked to close pipes that are at their ends, let's do it.
				if(false === $read or (true === $close and feof($pipe) and '' === $data))
				{
					fclose($this->pipes[$type]);
					unset($this->pipes[$type]);
				}
			}

			return $result;
		}

		/**
		 * Opens temporary files to be used instead of pipes. Workaround for PHP bug #51800
		 * (@see https://bugs.php.net/bug.php?id=51800).
		 *
		 * @return  $this
		 */

		protected function openTemporaryFiles()
		{
			// Declare the file names we are going to use for our temporary files.
			$this->files =
			[
				Process::STDOUT => tempnam(sys_get_temp_dir(), 'nyx_system_call_stdout'),
				Process::STDERR => tempnam(sys_get_temp_dir(), 'nyx_system_call_stderr'),
			];

			// Open the respective files for reading and writing.
			foreach($this->files as $stream => $file)
			{
				$this->handles[$stream] = fopen($this->files[$stream], 'rb');

				// Ensure the temporary files got opened properly.
				if(false === $this->handles[$stream])
				{
					throw new \RuntimeException("A temporary file could not be opened to write the process output to. Verify that the directory specified in your TEMP environment variable is writable.");
				}
			}

			$this->offsets =
			[
				Process::STDOUT => 0,
				Process::STDERR => 0
			];

			return $this;
		}
	}