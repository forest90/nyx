<?php namespace nyx\console\view\notifiers\progress;

	// Internal dependencies
	use nyx\console\view\notifiers;
	use nyx\console\interfaces;

	/**
	 * Progress Bar Notifier
	 *
	 * This Notifier will replace the following placeholders in the presentation format: "percent", "bar", "elapsed",
	 * "estimated".
	 *
	 * @package     Nyx\Console\Output\View
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/view/notifiers.html
	 * @todo        Automate the scaling of the width.
	 */

	class Bar extends notifiers\Progress
	{
		/**
		 * The character constants used to identify the positions of a specific character in the string of characters
		 * used to build the progress bar.
		 */

		const DONE      = 0;    // Finished step.
		const CURRENT   = 1;    // Currently at.
		const REMAINING = 2;    // Unfinished step.

		/**
		 * @var int     The width of the Progress Bar measured in characters.
		 */

		private $width;

		/**
		 * @var string  The characters that should be used to construct the Progress Bar. At most 3 are used and they
		 *              are given in a specific order as outlined by the character constants defined in this class.
		 */

		private $characters;

		/**
		 * {@inheritDoc}
		 *
		 * @param   int     $width          The width of the Progress Bar measured in characters.
		 * @param   string  $characters     The characters that should be used to construct the Progress Bar. Expects
		 *                                  exactly 3 characters.
		 */

		public function __construct(interfaces\Output $output, $max, $width = 48, $characters = '=|-', $format = '  %percent%% [%bar%] [%elapsed% / %estimated%]', $frequency = 100)
		{
			$this->setWidth($width);
			$this->setCharacters($characters);

			parent::__construct($output, $max, $format, $frequency);
		}

		/**
		 * Sets the width of the Progress Bar measured in characters.
		 *
		 * @param   int     $characters         The width of the Progress Bar.
		 * @return  $this
		 */

		public function setWidth($characters)
		{
			$this->width = (int) $characters;

			return $this;
		}

		/**
		 * Sets the characters that should be used to construct the Progress Bar.
		 *
		 * @param   string  $characters         The characters to set.
		 * @return  $this
		 * @throws  \InvalidArgumentException   When a string containing not exactly 3 characters has been given.
		 */

		public function setCharacters($characters)
		{
			if(3 !== $len = strlen($characters))
			{
				throw new \InvalidArgumentException("A Progress Bar expects exactly 3 characters to build the bar from. [$len] given in string [$characters].");
			}

			$this->characters = $characters;

			return $this;
		}

		/**
		 * Sets a specific character in the string used to be build the Progress Bar.
		 *
		 * @param   int     $which              Which character should be set (one of the character constants defined
		 *                                      in this class).
		 * @param   string  $character          The character to set. When a string longer than 1 character is given,
		 *                                      only the first character will be used.
		 * @return  $this
		 * @throws  \OutOfBoundsException       When the given character position is not recognized.
		 */

		public function setCharacter($which, $character)
		{
			// Make sure the given position is supported, ie. defined in the character constants.
			if(0 > $which or 2 < $which)
			{
				throw new \OutOfBoundsException("The given character position [$which] is not supported. Must be one of: 0, 1 or 2.");
			}

			$this->characters[$which] = $character[0];

			return $this;
		}

		/**
		 * {@inheritDoc}
		 */

		protected function getPresentationValues()
		{
			$return     = parent::getPresentationValues();
			$percentage = round($this->getPercentage(), 2);
			$finished   = floor($percentage * $this->width);

			// Start building the bar.
			$bar = str_repeat($this->characters[self::DONE], $finished);

			// Add the "currently at" character and "unfinished" step characters only if "finished" steps don't yet
			// exceed the total width.
			if($finished < $this->width)
			{
				// "Currently at".
				$bar .= $this->characters[self::CURRENT];

				// Fill in the remainder with the "unfinished" chars.
				$bar .= str_repeat($this->characters[self::REMAINING], $this->width - strlen($bar));
			}

			$return['bar'] = $bar;

			return $return;
		}
	}
