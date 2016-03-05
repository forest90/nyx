<?php namespace nyx\console\interfaces\output;

	/**
	 * Output Style Interface
	 *
	 * @package     Nyx\Console\Output
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/output.html
	 */

	interface Style
	{
		/**
		 * Sets the foreground color of this Style.
		 *
		 * @param   string  $color              The name of the color.
		 * @throws  \InvalidArgumentException   When the given color is not available.
		 */

		public function setForeground($color = null);

		/**
		 * Sets the background color of this Style.
		 *
		 * @param   string  $color              The name of the color.
		 * @throws  \InvalidArgumentException   When the given color is not available.
		 */

		public function setBackground($color = null);

		/**
		 * Sets one or more additional option(s) for this Style.
		 *
		 * @param   array    $options           An array of additional option names.
		 * @throws  \InvalidArgumentException   When one of the given additional options is not available.
		 */

		public function setAdditional(array $options);

		/**
		 * Applies this Style to a given string.
		 *
		 * @param   string  $text   The string to be styled.
		 * @return  string          The stylized string.
		 */

		public function apply($text);
	}