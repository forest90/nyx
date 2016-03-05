<?php namespace nyx\console;

	/**
	 * Abstract Descriptor
	 *
	 * Implements the Descriptor Interface by checking the types of the objects {@see self::describe()) gets and calling
	 * methods fit to describe the given type.
	 *
	 * The particular methods are public for simplicity's sake but if you intend to use them directly, ensure you are
	 * checking for the type of this class, not for the interface, as the interface does not require them to be present
	 * for the contract to be fulfilled.
	 *
	 * @package     Nyx\Console\Descriptors
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/descriptors.html
	 */

	abstract class Descriptor implements interfaces\Descriptor
	{
		/**
		 * {@inheritDoc}
		 */

		public function describe($object, array $options = [])
		{
			if($object instanceof input\Argument)   return $this->describeInputArgument($object, $options);
			if($object instanceof input\Option)     return $this->describeInputOption($object, $options);
			if($object instanceof input\Definition) return $this->describeInputDefinition($object, $options);
			if($object instanceof input\Value)      return $this->describeInputValue($object, $options);
			if($object instanceof Suite)            return $this->describeSuite($object, $options);
			if($object instanceof Command)          return $this->describeCommand($object, $options);

			throw new \InvalidArgumentException('This descriptor is unable to describe an object of type ['.get_class($object).'].');
		}

		/**
		 * Describes an Input Argument.
		 *
		 * @param   input\Argument      $argument   The Input Argument to describe.
		 * @param   array               $options    Additional options to be considered by the Descriptor.
		 * @return  mixed
		 */

		abstract public function describeInputArgument(input\Argument $argument, array $options = []);

		/**
		 * Describes an Input Option.
		 *
		 * @param   input\Option        $option     The Input Option to describe.
		 * @param   array               $options    Additional options to be considered by the Descriptor.
		 * @return  mixed
		 */

		abstract public function describeInputOption(input\Option $option, array $options = []);

		/**
		 * Describes an Input Value.
		 *
		 * @param   input\Value         $value      The Input Value to describe.
		 * @param   array               $options    Additional options to be considered by the Descriptor.
		 * @return  mixed
		 */

		abstract public function describeInputValue(input\Value $value, array $options = []);

		/**
		 * Describes an Input Definition.
		 *
		 * @param   input\Definition    $definition The Input Definition to describe.
		 * @param   array               $options    Additional options to be considered by the Descriptor.
		 * @return  mixed
		 */

		abstract public function describeInputDefinition(input\Definition $definition, array $options = []);

		/**
		 * Describes a Command.
		 *
		 * @param   Command             $command    The Command to describe.
		 * @param   array               $options    Additional options to be considered by the Descriptor.
		 * @return  string
		 */

		abstract public function describeCommand(Command $command, array $options = []);

		/**
		 * Describes a Suite.
		 *
		 * @param   Suite               $suite      The Suite to describe.
		 * @param   array               $options    Additional options to be considered by the Descriptor.
		 * @return  mixed
		 */

		abstract public function describeSuite(Suite $suite, array $options = []);

		/**
		 * Returns the synopsis (ie. a string describing the usage) for the given Command.
		 *
		 * Kept in the abstract Descriptor as the generated string is pretty generic, but may require overrides for
		 * specific formats and therefore should not be kept within the Command class.
		 *
		 * @param   Command             $command    The Command to get the synopsis for.
		 * @return  string
		 */

		public function getCommandSynopsis(Command $command)
		{
			$definition = $command->getDefinition();
			$parameters = [];

			/** @var input\Argument $argument */
			foreach($definition->arguments()->all() as $argument)
			{
				$parameters[] = sprintf($argument->getValue()->is(input\Value::REQUIRED)
					? '%s'
					: '[%s]', $argument->getName().($argument->getValue() instanceof input\values\Multiple ? '1' : ''));

				if($argument->getValue() instanceof input\values\Multiple)
				{
					$parameters[] = sprintf('... [%sN]', $argument->getName());
				}
			}

			/** @var input\Option $option */
			foreach($definition->options()->all() as $option)
			{
				$shortcut = $option->getShortcut() ? sprintf('-%s|', $option->getShortcut()) : '';
				$parameters[] = sprintf('['.($option->getValue()->is(input\Value::REQUIRED)
					? '%s--%s="..."'
					: ($option->getValue()->is(input\Value::OPTIONAL) ? '%s--%s[="..."]' : '%s--%s')).']', $shortcut, $option->getName());
			}

			// Parameters are separated by a whitespace only as we want them to be contained in one line if possible.
			return implode(' ', $parameters);
		}
	}