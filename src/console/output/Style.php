<?php namespace nyx\console\output;

	// Internal dependencies
	use nyx\console\interfaces;

	/**
	 * Style
	 *
	 * An output style. Used internally by the Output Formatter to apply the respective styles onto text within style
	 * tags, but it can also be used manually to stylize strings. Note: The class does not check if the given strings
	 * already contain any sort of color codes. Applying a style onto an already decorated string can therefore yield
	 * unexpected results.
	 *
	 * Based on Symfony 2's Console component. See the LICENSE file distributed with this package for detailed copyright
	 * and licensing information.
	 *
	 * @package     Nyx\Console\Output
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/output.html
	 */

	class Style implements interfaces\output\Style
	{
		/**
		 * @var array   The available foreground colors. The actual color representations may vary from the names as it
		 *              depends on the terminal used to display them.
		 */

		private static $foregrounds =
		[
			'black'         => 30,
			'red'           => 31,
			'green'         => 32,
			'yellow'        => 33,
			'blue'          => 34,
			'magenta'       => 35,
			'cyan'          => 36,
			'white'         => 37,
			'gray'          => '1;30',
			'light_red'     => '1;31',
			'light_green'   => '1;32',
			'light_yellow'  => '1;33',
			'light_blue'    => '1;34',
			'light_magenta' => '1;35',
			'light_cyan'    => '1;36',
			'silver'        => '1;37'
		];

		/**
		 * @var array   The available background colors.
		 */

		private static $backgrounds =
		[
			'black'     => 40,
			'red'       => 41,
			'green'     => 42,
			'yellow'    => 43,
			'blue'      => 44,
			'magenta'   => 45,
			'cyan'      => 46,
			'white'     => 47
		];

		/**
		 * @var array   The available additional options (Note: support for those depends on the type of the terminal
		 *              used to display the application).
		 */

		private static $additional =
		[
			'bold'          => 1,
			'underscore'    => 4,
			'blink'         => 5,
			'reverse'       => 7,
			'conceal'       => 8
		];

		/**
		 * @var int     The currently set foreground color of this Style.
		 */

		private $foreground;

		/**
		 * @var int     The currently set background color of this Style.
		 */

		private $background;

		/**
		 * @var array   The currently set additional options of this Style.
		 */

		private $options = [];

		/**
		 * Constructs an output Style.
		 *
		 * @param   string  $foreground     The foreground color to be set.
		 * @param   string  $background     The background color to be set.
		 * @param   array   $options        An array of additional options to be set.
		 */

		public function __construct($foreground = null, $background = null, array $options = [])
		{
			$foreground and $this->setForeground($foreground);
			$background and $this->setBackground($background);
			$options    and $this->setAdditional($options);
		}

		/**
		 * {@inheritDoc}
		 */

		public function setForeground($color = null)
		{
			// Ensure the given color is available.
			if($color and !isset(static::$foregrounds[$color]))
			{
				throw new \InvalidArgumentException("The foreground color [$color] is not supported");
			}

			$this->foreground = $color ? static::$foregrounds[$color] : null;
		}

		/**
		 * {@inheritDoc}
		 */

		public function setBackground($color = null)
		{
			// Ensure the given color is available.
			if($color and !isset(static::$backgrounds[$color]))
			{
				throw new \InvalidArgumentException("The background color [$color] is not supported");
			}

			$this->background = $color ? static::$backgrounds[$color] : null;
		}

		/**
		 * {@inheritDoc}
		 */

		public function setAdditional(array $options)
		{
			foreach($options as $option)
			{
				// Ensure the given option is a viable choice.
				if(!isset(static::$additional[$option]))
				{
					throw new \InvalidArgumentException("The additional option [$option] is not supported");
				}

				// Only add the option when it not already set.
				if(array_search(static::$additional[$option], $this->options) === false)
				{
					$this->options[] = static::$additional[$option];
				}
			}
		}

		/**
		 * {@inheritDoc}
		 */

		public function apply($text)
		{
			$codes = [];

			// Do we have a foreground and/or background?
			$this->foreground !== null and $codes[] = $this->foreground;
			$this->background !== null and $codes[] = $this->background;

			// Handle any additional options.
			if(count($this->options)) $codes = array_merge($codes, $this->options);

			// No point in applying fancy styling when there's no styling at all to be done.
			if(!count($codes)) return $text;

			// Note: \e handles \033 as of PHP5.4
			return sprintf("\e[%sm%s\e[0m", implode(';', $codes), $text);
		}
	}