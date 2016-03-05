<?php namespace nyx\console\terminals;

	// External dependencies
	use nyx\utils;

	// Internal dependencies
	use nyx\console\interfaces;

	/**
	 * TTY Terminal
	 *
	 * Should be compatible with most if not all Unix based systems. Note: The results of the calls are not cached
	 * since the dimensions might change during runtime.
	 *
	 * @package     Nyx\Console\Terminals
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/terminals.html
	 */

	class TTY implements interfaces\Terminal
	{
		/**
		 * {@inheritDoc}
		 */

		public function getWidth($default = 80)
		{
			$width = null;

			// Stty takes priority when it's available.
			if(utils\System::hasStty()) $width = $this->fromStty()['width'];

			// When we don't have a width yet, try tput.
			if(null === $width) $width = $this->fromTput('cols');

			// Return the value if it's not falsy, otherwise the default.
			return $width ?: $default;
		}

		/**
		 * {@inheritDoc}
		 */

		public function getHeight($default = 32)
		{
			$height = null;

			// Stty takes priority when it's available.
			if(utils\System::hasStty()) $height = $this->fromStty()['height'];

			// When we don't have a height yet, try tput.
			if(null === $height) $height = $this->fromTput('lines');

			// Return the value if it's not falsy, otherwise the default.
			return $height ?: $default;
		}

		/**
		 * Executes a 'stty -a' call and parses the results in order to determine the dimensions of the terminal.
		 *
		 * @return  array|null  Either an array containing two keys - 'width' and 'height' or null if the result of the
		 *                      call couldn't be parsed to retrieve any useful data.
		 */

		protected function fromStty()
		{
			preg_match_all("/rows.([0-9]+);.columns.([0-9]+);/", strtolower(exec('stty -a | grep columns')), $output);

			return count($output) === 3 ? ['width' => (int) $output[2][0], 'height' => (int) $output[1][0]] : null;
		}

		/**
		 * Executes a 'tput' call to request the given parameter - either 'cols' or 'lines' (width and height
		 * respectively) and returns the results.
		 *
		 * @param   string  $param      One of 'cols' or 'lines'.
		 * @return  int|null            Either the value of the requested parameter or null if it couldn't be parsed.
		 */

		protected function fromTput($param)
		{
			$result = (int) exec('tput ' . $param);

			return empty($result) ? null : $result;
		}
	}