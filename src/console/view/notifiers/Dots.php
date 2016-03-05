<?php namespace nyx\console\view\notifiers;

	// Internal dependencies
	use nyx\console\interfaces;
	use nyx\console\view;

	/**
	 * Dots Notifier
	 *
	 * A Dots Notifier repeats the display of a given string up to the given number of times and repeats this procedure
	 * until progress reaches 100%. Despite the name of this Notifier any string may be set to be repeated.
	 *
	 * This Notifier will replace the following placeholders in the presentation format: "chars", "elapsed", "speed".
	 *
	 * @package     Nyx\Console\Output\View
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/view/notifiers.html
	 */

	class Dots extends view\Notifier
	{
		/**
		 * @var int     The number of repetitions that should be displayed before resetting into a new loop.
		 */

		private $repetitions;

		/**
		 * @var string  The string that shall be repeated up to the given number of times.
		 */

		private $character;

		/**
		 * @var int     Internal (meat)spin counter.
		 */

		private $iteration;

		/**
		 * {@inheritDoc}
		 *
		 * @param   int     $repetitions    The number of repetitions that should be displayed before resetting into
		 *                                  a new loop.
		 * @param   string  $character      The string to be repeated by the Notifier.
		 */

		public function __construct(interfaces\Output $output, $repetitions = 3, $character = '.', $format = '  %chars%  [%elapsed%, %speed%/s]', $frequency = 100)
		{
			$this->iteration = 0;

			$this->setCharacter($character);
			$this->setRepetitions($repetitions);

			parent::__construct($output, $format, $frequency);
		}

		/**
		 * Sets the number of repetitions that should be displayed before this Dots Notifiers resets.
		 *
		 * @param   int     $repetitions    The number of repetitions.
		 * @return  $this
		 * @throws  \OutOfRangeException    When the given number is smaller than 1.
		 */

		public function setRepetitions($repetitions)
		{
			if(1 > $this->repetitions = (int) $repetitions)
			{
				throw new \OutOfRangeException("Number of repetitions [$this->repetitions] out of range, must be greater or equal to 1.");
			}

			return $this;
		}

		/**
		 * Sets the string to be repeated by the Notifier up to the set number of repetitions.
		 *
		 * @param   string  $char   The string to be repeated.
		 * @return  $this
		 */

		public function setCharacter($char)
		{
			$this->character = (string) $char;

			return $this;
		}

		/**
		 * {@inheritDoc}
		 */

		protected function getPresentationValues()
		{
			$return = parent::getPresentationValues();
			$return['chars'] = str_pad(str_repeat($this->character, $this->iteration++ % $this->repetitions), $this->repetitions);

			return $return;
		}
	}
