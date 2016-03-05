<?php namespace nyx\console;

	/**
	 * Abstract Input
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.3
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	abstract class Input implements interfaces\Input
	{
		/**
		 * @var interfaces\input\Tokens     The raw, unparsed input.
		 */

		protected $raw;

		/**
		 * @var input\Definition        The Definition this Input instance is bound to.
		 */

		private $definition;

		/**
		 * @var input\bags\Arguments    An Arguments bag.
		 */

		private $arguments;

		/**
		 * @var input\bags\Options      An Options bag.
		 */

		private $options;

		/**
		 * @var bool    Whether this Input may be interactive or not.
		 */

		private $interactive = true;

		/**
		 * {@inheritDoc}
		 */

		public function arguments()
		{
			return $this->arguments;
		}

		/**
		 * {@inheritDoc}
		 */

		public function options()
		{
			return $this->options;
		}

		/**
		 * {@inheritDoc}
		 */

		public function definition()
		{
			return $this->definition;
		}

		/**
		 * {@inheritDoc}
		 */

		public function raw()
		{
			return $this->raw;
		}

		/**
		 * {@inheritDoc}
		 */

		public function bind(input\Definition $definition, Context $context = null)
		{
			$this->definition = $definition;

			$this->reset();
			$this->parse();
			$this->validate();

			return null !== $context ? $this->executeBindingCallbacks($context) : $this;
		}

		/**
		 * {@inheritDoc}
		 */

		public function setInteractive($bool)
		{
			$this->interactive = (bool) $bool;

			return $this;
		}

		/**
		 * {@inheritDoc}
		 */

		public function isInteractive()
		{
			return $this->interactive;
		}

		/**
		 * Ensures the parameters contained in the Input are valid.
		 *
		 * @return  $this
		 * @throws  exceptions\Input    When the provided parameters do not meet the specified Definition.
		 */

		protected function validate()
		{
			$this->arguments()->validate();

			return $this;
		}

		/**
		 * Replaces the parameter bags with new instances bound to the current master Definition.
		 *
		 * @return  $this
		 */

		protected function reset()
		{
			$this->arguments = new input\bags\Arguments($this->definition->arguments());
			$this->options   = new input\bags\Options($this->definition->options());

			return $this;
		}

		/**
		 * Executes the binding callbacks of the parameters.
		 *
		 * @param   Context             $context        The Execution Context the callbacks should be invoked with.
		 * @return  $this
		 */

		protected function executeBindingCallbacks(Context $context)
		{
			$optionsDefinition = $this->definition->options();

			// Execute the callbacks for all Options, in the order they have been given by the user.
			foreach($this->options as $name => $value)
			{
				if(null !== $callback = $optionsDefinition->get($name)->getCallback())
				{
					call_user_func($callback, $context, $value);
				}
			}

			return $this;
		}

		/**
		 * Parses the raw Tokens into usable Arguments and Options bags.
		 *
		 * @return  $this
		 */

		abstract protected function parse();
	}