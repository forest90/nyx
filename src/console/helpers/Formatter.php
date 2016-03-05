<?php namespace nyx\console\helpers;

	// External dependencies
	use nyx\utils;

	// Internal dependencies
	use nyx\console;

	/**
	 * Formatter Helper
	 *
	 * Provides helpful methods when formatting text before it is sent to the output.
	 *
	 * @package     Nyx\Console\Helpers
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/helpers.html
	 */

	class Formatter extends console\Helper
	{
		/**
		 * {@inheritDoc}
		 */

		public function __construct($name = null)
		{
			parent::__construct($name ?: 'formatter');
		}

		/**
		 * Formats a message as a block of text.
		 *
		 * @param   string|array    $messages   The message to write in the block.
		 * @param   string          $style      The style to apply to the whole block.
		 * @param   bool            $large      Whether to return a large block.
		 * @param   int             $width      The maximal width of one line in the block.
		 * @return  string                      The formatted message.
		 */

		public function block($messages, $style, $large = false, $width = 100)
		{
			$messages = (array) $messages;

			$length = 0;
			$lines  = [];

			foreach($messages as $message)
			{
				$message = console\output\Formatter::escape($message);

				foreach(preg_split("{\r?\n}", $message) as $line)
				{
					foreach(str_split($line, $width - ($large ? 4 : 2)) as $line)
					{
						$lines[] = sprintf($large ? '  %s  ' : ' %s ', $line);
						$length = max(utils\Str::length($line) + ($large ? 4 : 2), $length);
					}
				}
			}

			$messages = $large ? [str_repeat(' ', $length)] : [];

			foreach($lines as $line)
			{
				$messages[] = $line.str_repeat(' ', $length - utils\Str::length($line));
			}

			$large and $messages[] = str_repeat(' ', $length);

			foreach($messages as &$message)
			{
				$message = sprintf('<%s>%s</%s>', $style, $message, $style);
			}

			return implode("\n", $messages);
		}
	}