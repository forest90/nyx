<?php namespace nyx\console\view\notifiers;

	// Internal dependencies
	use nyx\console\interfaces;
	use nyx\console\view;

	/**
	 * Alternator Notifier
	 *
	 * An Alternator Notifier alternates between the given characters and displays the subsequent character with each
	 * tick it performs. Only one and exactly one character will be displayed at any given moment.
	 *
	 * This Notifier will replace the following placeholders in the presentation format: "char", "elapsed", "speed".
	 *
	 * @package     Nyx\Console\Output\View
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/view/notifiers.html
	 */

	class Alternator extends view\Notifier
	{
		/**
		 * @var string  The characters that should be used by the Notifier to alternate between.
		 */

		private $characters;

		/**
		 * @var int     Internal (meat)spin counter.
		 */

		private $iteration;

		/**
		 * {@inheritDoc}
		 *
		 * @param   string  $characters     The string to be repeated by the Notifier.
		 */

		public function __construct(interfaces\Output $output, $characters = '-\|/', $format = '  %char%  [%elapsed%, %speed%/s]', $frequency = 100)
		{
			$this->iteration = 0;

			$this->setCharacters($characters);

			parent::__construct($output, $format, $frequency);
		}

		/**
		 * Sets the string of characters this Notifier should alternate between.
		 *
		 * @param   string  $characters     The string of characters to alternate between.
		 * @return  $this
		 */

		public function setCharacters($characters)
		{
			$this->characters = (string) $characters;

			return $this;
		}

		/**
		 * {@inheritDoc}
		 */

		protected function getPresentationValues()
		{
			$return = parent::getPresentationValues();
			$return['char'] = $this->characters[$this->iteration++ % strlen($this->characters)];

			return $return;
		}
	}
