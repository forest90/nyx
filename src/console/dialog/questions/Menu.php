<?php namespace nyx\console\dialog\questions;

	// Internal dependencies
	use nyx\console\exceptions;

	/**
	 * Menu Question
	 *
	 * Displays an interactive menu with predefined choices.
	 *
	 * @package     Nyx\Console\Dialog
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/dialog.html
	 */

	class Menu extends Choice
	{
		/**
		 * {@inheritDoc}
		 *
		 * Overridden to change the default presentation format.
		 */

		public function __construct($text, array $choices = null, $default = null, $attempts = false, $format = '  %text% [%default%] %choices%')
		{
			parent::__construct($text, $choices, $default, $attempts, $format);
		}

		/**
		 * {@inheritDoc}
		 */

		public function addChoice($value, $description = null)
		{
			// Enable in-line aliases for the choices. Use the alias as label in this case and remove the square brackets
			// from the value.
			if(preg_match('/\[(.+)\]/', $value, $matches))
			{
				$label = $matches[1];
				$value = str_replace("[$matches[1]]", $matches[1], $value);
			}
			// Otherwise use a 1-indexed label for the choice, based on the current choice count.
			else
			{
				$label = $this->count() + 1;
			}

			// Let the parent do the rest. Passing an array as the second parameter is an ugly hackaround to avoid
			// duplicating more code than necessary. This hackaround is accounted for in self::validateAnswer().
			parent::addChoice($label, ['value' => $value, 'description' => $description]);
		}

		/**
		 * Adds one or more answer choices to the Question.
		 *
		 * @param   array   $choices    Different from Choice::addChoices(), this class assumes the array key to be
		 *                              the value of the final choice, while the array value will be used as description
		 *                              of the choice, later on displayed in the menu. By default, all choices will be
		 *                              assigned numerical labels (1-indexed) in the menu, but an inline alias may
		 *                              be used within the key, in which case the alias will be used as label instead.
		 *                              For more information on inline aliases {@see Choice::addChoice()}.
		 */

		public function addChoices(array $choices)
		{
			foreach($choices as $value => $description) $this->addChoice($value, $description);
		}

		/**
		 * {@inheritDoc}
		 *
		 * Overridden as we are using a hackaround to store the description for the choice next to the value, and the
		 * parent's validator has no knowledge of this. At the same time, it may return the default answer instead of
		 * of an array including the value and description, so we have to account for both situations.
		 */

		public function validateAnswer($answer)
		{
			return is_array($answer = parent::validateAnswer($answer)) ? $answer['value'] : $answer;
		}

		/**
		 * {@inheritDoc}
		 */

		protected function getPresentationValues()
		{
			$values = parent::getPresentationValues();

			// First we have to calculate the maximal width of the labels, in order to provide padding for them and have
			// the choices aligned properly.
			$padding = 0;

			foreach($this->getChoices() as $label => $choice)
			{
				// +2 to include the outer square brackets for the label.
				if($padding < $strlen = mb_strlen($label) + 2) $padding = $strlen;
			}

			// The presentation pattern is contained within one line, but we are going to expand the choices into
			// separate lines to make them legible.
			$values['choices'] = "\n\n";

			foreach($this->getChoices() as $label => $choice)
			{
				$values['choices'] .= sprintf("   <comment>%-${padding}s</comment> %s\n", "[$label]", $choice['description'] ?: $choice['value']);
			}

			return $values;
		}
	}