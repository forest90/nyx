<?php namespace nyx\console\interfaces\output;

	// Internal dependencies
	use nyx\console\output;

	/**
	 * Output Formatter Interface
	 *
	 * @package     Nyx\Console\Output
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/output.html
	 */

	interface Formatter
	{
		/**
		 * Sets whether the output should be decorated.
		 *
		 * @param   bool    $decorated  Whether to decorate the output or not.
		 */

		public function setDecorated($decorated);

		/**
		 * Checks whether the Output is being decorated.
		 *
		 * @return  bool    True if the output is being decorated, false otherwise.
		 */

		public function isDecorated();

		/**
		 * Returns the style Set instance in use by this Formatter.
		 *
		 * @return  output\styles\Set
		 */

		public function getStyles();

		/**
		 * Formats a message according to the styling behaviour of the Formatter.
		 *
		 * @param   string|array    $message    The message or array of messages that should be styled.
		 * @return  string                      The resulting, styled message.
		 */

		public function format($message);
	}
