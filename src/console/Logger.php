<?php namespace nyx\console;

	// External dependencies
	use Psr\Log;

	/**
	 * Console Logger
	 *
	 * Assumes the default Output Format Styles 'error' and 'important' are defined.
	 *
	 * @package     Nyx\Console\Output
	 * @version     0.0.2
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/logger.html
	 * @todo        Ensure the styles are present and make them customizable (also add in a "merge" method on the Styles
	 *              set)
	 * @todo        Customizable patterns and styles on a per-level basis.
	 */

	class Logger extends Log\AbstractLogger implements Log\LoggerInterface, interfaces\output\Aware
	{
		/**
		 * The traits of a Logger instance.
		 */

		use traits\OutputAware;

		/**
		 * Constructs a new Console Logger instance.
		 *
		 * @param   interfaces\Output   $output         The Output instance to be used.
		 */

		public function __construct(interfaces\Output $output = null)
		{
			$this->output = $output ?: new output\Stdout;
		}

		/**
		 * {@inheritDoc}
		 */

		public function log($level, $message, array $context = [])
		{
			switch($level)
			{
				case Log\LogLevel::EMERGENCY:
				case Log\LogLevel::ALERT:
				case Log\LogLevel::CRITICAL:
					$style = 'error';
				break;

				case Log\LogLevel::ERROR:
				case Log\LogLevel::WARNING:
					$style = 'important';
				break;

				default:
					$style = null;
			}

			// Uppercase the message level, pad it to 10 characters total ("emergency" is the longest default level
			// and is 9 characters long) and prepend it to the actual message.
			$message = str_pad(strtoupper($level), 10).$message;

			// Wrap the above in a style tag when the message level was high enough for that.
			$message = $style ? sprintf("<%s>%s</%s>", $style, $message, $style) : $message;

			// Prepend the formatted date and write the message out.
			$this->output->writeln(sprintf("%s %s", $this->getFormattedDate(), $message));
		}

		/**
		 * Returns the current time as a string formatted according to the defined pattern.
		 *
		 * @return  string
		 */

		protected function getFormattedDate()
		{
			return date("H:i");
		}
	}