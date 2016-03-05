<?php namespace nyx\console\dialog\questions;

	// Internal dependencies
	use nyx\console\exceptions;

	/**
	 * Choice Question
	 *
	 * A Question that accepts one choice out of a set of choices as answer.
	 *
	 * @package     Nyx\Console\Dialog
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/dialog.html
	 */

	class Choice extends Validated
	{
		/**
		 * @var array   The defined choices.
		 */

		private $choices;

		/**
		 * @var array   The defined choice labels.
		 */

		private $labels;

		/**
		 * {@inheritDoc}
		 *
		 * @param   array   $choices    The answer choices. {@see self::addChoices()}.
		 * @param   mixed   $default    The default answer. If given, needs to be one of the available choices in order
		 *                              to pass validation. Leave at null to force the user to pick one of the available
		 *                              choices.
		 * @param   bool    $attempts   {@see Validated::setAllowedAttempts()}.
		 */

		public function __construct($text, array $choices = null, $default = null, $attempts = false, $format = '  %text% (%choices%) [%default%] ')
		{
			$choices and $this->addChoices($choices);

			parent::__construct($text, [$this, 'validateAnswer'], $default, $attempts, $format);
		}

		/**
		 * Adds an answer choice to the Question.
		 *
		 * @param   string  $label  The label for the choice (ie. what the user needs to type). Square brackets can be
		 *                          used to denote an inline alias, ie. given a label of "[s]omething", if the user
		 *                          types either 's' or 'something', both answers will point to the same $value.
		 *                          Note: Labels will be case-insensitive.
		 * @param   mixed   $value  The value to return when the choice gets picked.
		 */

		public function addChoice($label, $value)
		{
			// We'll need to work on two vars either way, so let's alias it for now, regardless whether it's truthfully
			// clean or not.
			$cleanLabel = $label;

			// Did the label include an alias in square brackets?
			if(preg_match('/\[(.+)\]/', $label, $matches))
			{
				// Let's remove the brackets from the label and actually 'clean' it.
				$cleanLabel = str_replace("[$matches[1]]", $matches[1], $label);

				// And remember the alias.
				$this->choices[strtolower($matches[1])] = $value;
			}

			// Matching will be done in a case-insensitive manner, as stated in the note.
			$ciCleanLabel = strtolower($cleanLabel);

			// Remember the full label (including square brackets etc.) separately.
			$this->labels[$ciCleanLabel] = $label;
			$this->choices[$ciCleanLabel] = $value;
		}

		/**
		 * Adds one or more answer choices to the Question.
		 *
		 * @param   array   $choices    The answer choices. If the keys are not strings, this method will use the values
		 *                              as labels instead. Therefore, if you want to provide numerical choices, just pass
		 *                              them explicitly as strings. @see self::addChoice() for information on how to
		 *                              add inline aliases for labels.
		 */

		public function addChoices(array $choices)
		{
			// Define the available choices.
			foreach($choices as $label => $value)
			{
				if(!is_string($label)) $label = $value;

				// Are we dealing with an inline alias?
				if(preg_match('/\[(.+)\]/', $value, $matches))
				{
					$value = str_replace("[$matches[1]]", $matches[1], $value);
				}

				$this->addChoice($label, $value);
			}
		}

		/**
		 * Returns the defined answer choices.
		 *
		 * @return  array
		 */

		public function getChoices()
		{
			// Retuning only the unique values to avoid returning aliases.
			return array_unique($this->choices, SORT_REGULAR);
		}

		/**
		 * Returns the labels used to access the given choices. Note: This is mainly for presentation purposes as
		 * those labels will contain the square brackets used for aliases etc.
		 *
		 * @return  array
		 */

		public function getLabels()
		{
			return $this->labels;
		}

		/**
		 * Returns the count of the defined choices.
		 *
		 * @return  int
		 */

		public function count()
		{
			return count($this->getChoices());
		}

		/**
		 * Checks if the given answer is defined as a valid choice. Returns it if it is.
		 *
		 * @param   string  $answer                     The answer to be validated.
		 * @return  string  $answer                     The validated answer. Returning the same value to conform to how
		 *                                              validators in a Validated Question should act.
		 * @throws  exceptions\dialog\InvalidChoice     When the answer does not correspond to a defined choice.
		 */

		public function validateAnswer($answer)
		{
			// We'll be doing case-insensitive matching so let's avoid some overhead.
			$ciAnswer = strtolower($answer);

			// If the value does not point to an existing choice, throw the exception right away.
			if(!isset($this->choices[$ciAnswer])) throw new exceptions\dialog\InvalidChoice($this, $answer);

			// Otherwise we can return the value.
			return $this->choices[$ciAnswer];
		}

		/**
		 * {@inheritDoc}
		 */

		protected function getPresentationValues()
		{
			$values = parent::getPresentationValues();
			$values['choices'] = implode('/', $this->getLabels());

			return $values;
		}
	}