<?php namespace nyx\console\dialog\questions;

	// Internal dependencies
	use nyx\console\interfaces;
	use nyx\console\input;
	use nyx\console\dialog;

	/**
	 * Confirmed Question
	 *
	 * A simple yes/no question.
	 *
	 * @package     Nyx\Console\Dialog
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/dialog.html
	 */

	class Confirmed extends Choice
	{
		/**
		 * {@inheritDoc}
		 *
		 * Overridden to change the default answer to a boolean true. If you pass anything other than boolean true/false
		 * or yes/no as default values, null will be used instead and therefore force the user to pick an answer.
		 */

		public function __construct($text, $default = true, $attempts = false, $format = '  %text% [%choices%] ')
		{
			parent::__construct($text, ['[y]es', '[n]o'], $default, $attempts, $format);
		}

		/**
		 * {@inheritDoc}
		 *
		 * @return  bool    The boolean representation of the answer.
		 */

		public function ask(input\Stream $input, interfaces\Output $output, $force = false)
		{
			return $this->determineBool(parent::ask($input, $output, $force));
		}

		/**
		 * {@inheritDoc}
		 *
		 * Overridden to convert yes/no strings to their boolean values and ultimately any non-boolean value to null.
		 */

		public function setDefault($answer)
		{
			parent::setDefault($this->determineBool($answer));
		}

		/**
		 * Checks if the given value is boolean or a string (yes/no) that can be converted to a boolean. If said
		 * condition is true, it returns the boolean representation of the value or null otherwise.
		 *
		 * @param   mixed       $value
		 * @return  bool|null
		 */

		protected function determineBool($value)
		{
			if(is_bool($value)) return $value;

			switch($value)
			{
				case 'y':
				case 'yes':

					return true;

				case 'n':
				case 'no':

					return false;
			}

			return null;
		}

		/**
		 * {@inheritDoc}
		 */

		protected function getPresentationValues()
		{
			$values = parent::getPresentationValues();
			$values['choices'] = $values['default'] !== null ? ($values['default'] ? 'Y/n' : 'y/N') : 'y/n';

			return $values;
		}
	}