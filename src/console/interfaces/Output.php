<?php namespace nyx\console\interfaces;

	/**
	 * Output Interface
	 *
	 * @package     Nyx\Console\Output
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/output.html
	 */

	interface Output
	{
		/**
		 * Verbosity and format constants.
		 */

		const NORMAL  = 0;
		const QUIET   = 1;
		const VERBOSE = 2;
		const LOUD    = 3;
		const DEBUG   = 4;
		const RAW     = 1;
		const PLAIN   = 2;

		/**
		 * Returns the Output Formatter instance in use by this Output.
		 *
		 * @return  output\Formatter
		 */

		public function getFormatter();

		/**
		 * Sets the Output Formatter instance to be used by this Output.
		 *
		 * @param   output\Formatter    $formatter
		 * @return  $this
		 */

		public function setFormatter(output\Formatter $formatter);

		/**
		 * Sets whether the output should be decorated.
		 *
		 * @param   bool    $decorated  Whether to decorate the output or not.
		 * @return  $this
		 */

		public function setDecorated($decorated);

		/**
		 * Checks whether the Output is being decorated.
		 *
		 * @return  bool    True if the output is being decorated, false otherwise.
		 */

		public function isDecorated();

		/**
		 * Returns the current verbosity of the output.
		 *
		 * @return  int     The current level of verbosity.
		 */

		public function getVerbosity();

		/**
		 * Sets the verbosity of the output.
		 *
		 * @param   int     $level  The level of verbosity.
		 * @return  $this
		 */

		public function setVerbosity($level);

		/**
		 * Checks whether the verbosity of the output is set to be quiet.
		 *
		 * @return  bool    True when the verbosity is set to be quiet, false otherwise.
		 */

		public function isQuiet();

		/**
		 * Checks whether the verbosity of the output is set to be at least verbose.
		 *
		 * @return  bool    True when the verbosity is set to be at least verbose, false otherwise.
		 */

		public function isVerbose();

		/**
		 * Checks whether the verbosity of the output is set to be at least loud.
		 *
		 * @return  bool    True when the verbosity is set to be at least loud, false otherwise.
		 */

		public function isLoud();

		/**
		 * Checks whether the verbosity of the output is set to be at least on a debug-level.
		 *
		 * @return  bool    True when the verbosity is set to be at least on a debug-level, false otherwise.
		 */

		public function isDebug();

		/**
		 * Returns the width and height available for the output.
		 *
		 * @return  array   An array of two key-value pairs: [0] => width, [1] => height.
		 */

		public function getDimensions();

		/**
		 * Sets the width and height available for the output.
		 *
		 * @param   int     $width      The width. Pass null to revert back to determining the width based on the
		 *                              Terminal adapter in use.
		 * @param   int     $height     The height. Pass null to revert back to determining the height based on the
		 *                              Terminal adapter in use.
		 * @return  $this
		 */

		public function setDimensions($width, $height);

		/**
		 * Returns the width available for the output.
		 *
		 * @return  int
		 */

		public function getWidth();

		/**
		 * Sets the width available for the output.
		 *
		 * @param   int     $width      The width. Pass null to revert back to determining the width based on the
		 *                              Terminal adapter in use.
		 * @return  $this
		 */

		public function setWidth($width);

		/**
		 * Returns the height available for the output.
		 *
		 * @return  int
		 */

		public function getHeight();

		/**
		 * Sets the height available for the output.
		 *
		 * @param   int     $height     The height. Pass null to revert back to determining the height based on the
		 *                              Terminal adapter in use.
		 * @return  $this
		 */

		public function setHeight($height);

		/**
		 * Writes a message to the output.
		 *
		 * @param   string|array                $messages   The message as an array of lines or a single string.
		 * @param   int                         $newline    The number of newlines that should be appended after every
		 *                                                  single message.
		 * @param   int                         $format     The format of the output.
		 * @return  $this
		 * @throws  \InvalidArgumentException               When an unknown output format is given.
		 */

		public function write($messages, $newline = 0, $format = self::NORMAL);

		/**
		 * Writes a message to the output and appends exactly one newline at the end.
		 *
		 * @param   string|array    $messages   The message as an array of lines or a single string.
		 * @param   int             $format     The format of the output.
		 * @return  $this
		 */

		public function writeln($messages, $format = self::NORMAL);

		/**
		 * Writes a newline to the output. This is a helper method to avoid the overhead of calling the formatter
		 * for empty lines.
		 *
		 * @param   int $lines  The number of newlines that should be written to the output.
		 * @return  $this
		 */

		public function ln($lines = 1);
	}