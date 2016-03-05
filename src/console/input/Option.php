<?php namespace nyx\console\input;

	/**
	 * Input Option Definition
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	class Option extends Parameter
	{
		/**
		 * @var string  The shortcut to this option.
		 */

		private $shortcut;

		/**
		 * @var callable    A callback to execute when the Option gets set with a non-null value.
		 */

		private $callback;

		/**
		 * Constructor.
		 *
		 * @param   string      $name               The option's name.
		 * @param   string      $shortcut           The shortcut. A hyphen at the beginning will be removed and only the
		 *                                          first character will be used, ie. a string of "-ve" will result in the
		 *                                          shortcut "v".
		 * @param   string      $description        A description text.
		 * @param   Value       $value              The value definition.
		 * @param   callable    $callback           A callback to execute when the Option gets set with a non-null value.
		 * @throws  \InvalidArgumentException       If the shortcut is set but empty.
		 */

		public function __construct($name, $shortcut = null, $description = null, Value $value = null, callable $callback = null)
		{
			if(!empty($shortcut))
			{
				// Standardize the shortcut's name.
				if('-' === $shortcut[0]) $shortcut = substr($shortcut, 1, 1);

				// We might have ended with an empty string if it only contained a dash
				if(empty($shortcut)) throw new \InvalidArgumentException("An option's shortcut cannot be empty when set.");
			}

			$this->shortcut = $shortcut;
			$this->callback = $callback;

			// Let the Parameter class handle the basics.
			parent::__construct($name, $description, $value ?: new Value(Value::NONE));
		}

		/**
		 * {@inheritDoc}
		 *
		 * Overriding the Named trait to remove unnecessary dashes from the beginning of the string. Gets called
		 * automatically in the parent's constructor.
		 */

		public function setName($name)
		{
			// Standardize the name by removing any double dashes at the beginning. They will be added later on when
			// the option actually gets displayed.
			if(1 === $pos = strpos($name, '-') or 0 === $pos = strpos($name, '-')) $name = substr($name, $pos + 1);

			parent::setName($name);
		}

		/**
		 * Returns the shortcut to this Option.
		 *
		 * @return  string
		 */

		public function getShortcut()
		{
			return $this->shortcut;
		}

		/**
		 * Returns the callback to execute when the Option gets set with a non-null value.
		 *
		 * @return  callable
		 */

		public function getCallback()
		{
			return $this->callback;
		}

		/**
		 * Sets a callback to execute when the Option gets set with a non-null value.
		 *
		 * @param   callable    $callback
		 */

		public function setCallback(callable $callback)
		{
			$this->callback = $callback;
		}
	}