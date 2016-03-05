<?php namespace nyx\console\terminals;

	// Internal dependencies
	use nyx\console\interfaces;

	/**
	 * Windows Terminal
	 *
	 * Covers the basic command line and PowerShell. Note: The results of the calls are not cached since the dimensions
	 * might change during runtime.
	 *
	 * @package     Nyx\Console\Terminals
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/terminals.html
	 */

	class Windows implements interfaces\Terminal
	{
		/**
		 * {@inheritDoc}
		 */

		public function getWidth($default = 80)
		{
			// Ansicon takes priority when it's available.
			$width = $this->fromAnsicon()['width'];

			// Otherwise...
			if(null === $width)
			{
				exec('mode CON', $output);

				$width = preg_match('{columns:\s*(\d+)}i', $output[4], $matches) ? $matches[1] : null;
			}

			// Return the value if it's not falsy, otherwise the default.
			return $width ?: $default;
		}

		/**
		 * {@inheritDoc}
		 */

		public function getHeight($default = 32)
		{
			// Ansicon takes priority when it's available.
			$height = $this->fromAnsicon()['height'];

			// Otherwise...
			if(null === $height)
			{
				exec('mode CON', $output);

				$height = preg_match('{lines:\s*(\d+)}i', $output[3], $matches) ? $matches[1] : null;
			}

			// Return the value if it's not falsy, otherwise the default.
			return $height ?: $default;
		}

		/**
		 * Attempts to determine the terminal dimensions based on the 'ANSICON' environmental variables.
		 *
		 * @return  array|null  Either an array containing two keys - 'width' and 'height' or null if the data couldn't
		 *                      be parsed to retrieve anything useful.
		 */

		protected function fromAnsicon()
		{
			if(preg_match('/^(\d+)x\d+ \(\d+x(\d+)\)$/', trim(getenv('ANSICON')), $matches))
			{
				return ['width' => (int) $matches[1], 'height' => (int) $matches[2]];
			}

			return null;
		}
	}