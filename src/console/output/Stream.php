<?php namespace nyx\console\output;

	// External dependencies
	use nyx\connect\streams;

	// Internal dependencies
	use nyx\console;

	/**
	 * Stream Output
	 *
	 * @package     Nyx\Console\Output
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/output.html
	 */

	class Stream extends console\Output
	{
		/**
		 * @var streams\interfaces\Stream   The Stream instance in use for normal output.
		 */

		private $stream;

		/**
		 * {@inheritDoc}
		 *
		 * @param   streams\interfaces\Stream   $stream     A Stream instance.
		 */

		public function __construct(streams\interfaces\Stream $stream, $verbosity = self::NORMAL, $decorated = null, console\interfaces\output\Formatter $formatter = null)
		{
			$this->stream = $stream;

			// Should we auto-discover whether to decorate the output?
			$decorated === null and $decorated = $this->hasColorSupport();

			// Set some of the basic stuff.
			parent::__construct($verbosity, $decorated, $formatter);
		}

		/**
		 * Returns the underlying Stream instance.
		 *
		 * @return  streams\interfaces\Stream
		 */

		public function expose()
		{
			return $this->stream;
		}

		/**
		 * {@inheritDoc}
		 */

		protected function doWrite($message, $newlines = 0)
		{
			$this->stream->write($message.(str_repeat(PHP_EOL, $newlines)));

			return $this;
		}

		/**
		 * Checks whether the underlying Stram supports colored output.
		 *
		 * @return  bool
		 */

		protected function hasColorSupport()
		{
			// There's way too many unknowns in regards to Stream handling, so let's only attempt to detect support
			// when handling local streams (Stdout will be the most likely use case, but the possibilities are vast).
			if($this->stream->is(streams\interfaces\Stream::LOCAL))
			{
				// Running on Windows with ANSICON or ConEmu?
				if(DIRECTORY_SEPARATOR == '\\') return false !== getenv('ANSICON') or 'ON' === getenv('ConEmuANSI');

				// Let's try with POSIX.
				return function_exists('posix_isatty') and @posix_isatty($this->stream->expose());
			}

			return false;
		}
	}