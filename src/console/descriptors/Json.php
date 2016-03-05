<?php namespace nyx\console\descriptors;

	// Internal dependencies
	use nyx\console\input;
	use nyx\console;

	/**
	 * Json Descriptor
	 *
	 * @package     Nyx\Console\Descriptors
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/descriptors.html
	 */

	class Json extends console\Descriptor
	{
		/**
		 * {@inheritDoc}
		 */

		public function describeInputArgument(input\Argument $argument, array $options = [])
		{
			return $this->postProcess(
			[
				'name'        => $argument->getName(),
				'description' => $argument->getDescription(),
				'value'       => $this->describeInputValue($argument->getValue(), ['as_array' => true])
			], $options);
		}

		/**
		 * {@inheritDoc}
		 */

		public function describeInputOption(input\Option $option, array $options = [])
		{
			return $this->postProcess(
			[
				'name'        => '--'.$option->getName(),
				'shortcut'    => $option->getShortcut() ? '-'.implode('|-', explode('|', $option->getShortcut())) : '',
				'description' => $option->getDescription(),
				'value'       => $this->describeInputValue($option->getValue(), ['as_array' => true])
			], $options);
		}

		/**
		 * {@inheritDoc}
		 */

		public function describeInputValue(input\Value $value, array $options = [])
		{
			return $this->postProcess(
			[
				'is_required'       => $value->is(input\Value::REQUIRED),
				'accepts_any'       => $value->accepts(),
				'accepts_multiple'  => $value instanceof input\values\Multiple,
				'default'           => $value->getDefault(),
			], $options);
		}

		/**
		 * {@inheritDoc}
		 */

		public function describeInputDefinition(input\Definition $definition, array $options = [])
		{
			$inputArguments = [];
			$inputOptions = [];

			foreach($definition->arguments()->all() as $name => $argument)
			{
				$inputArguments[$name] = $this->describeInputArgument($argument, ['as_array' => true]);
			}

			foreach($definition->options()->all() as $name => $option)
			{
				$inputOptions[$name] = $this->describeInputOption($option, ['as_array' => true]);
			}

			return $this->postProcess(
			[
				'arguments' => $inputArguments,
				'options' => $inputOptions
			], $options);
		}

		/**
		 * {@inheritDoc}
		 */

		public function describeCommand(console\Command $command, array $options = [])
		{
			$definition = $command->getDefinition();

			// Which type of Command are we dealing with?
			$type = ($command instanceof console\Suite) ? ($command instanceof console\Application ? 'application' : 'suite') : 'command';

			// Grab the help fpr the Command.
			return $this->postProcess(
			[
				'name'        => $command->getName(),
				'chain'       => $command->chain(),
				'type'        => $type,
				'usage'       => $this->getCommandSynopsis($command),
				'description' => $command->getDescription(),
				'help'        => (isset($options['raw']) and $options['raw']) ? strip_tags($command->getHelp(true)) : $command->getHelp(true),
				'definition'  => $this->describeInputDefinition($definition, ['as_array' => true]),
			], $options);
		}

		/**
		 * {@inheritDoc}
		 */

		public function describeSuite(console\Suite $suite, array $options = [])
		{
			// Descriptor options.
			$recursive = isset($options['recursive']) ? $options['recursive'] : false;
			$filters = isset($options['filters']) ? $options['filters'] : [];

			// Child description options.
			$childDescriptionOptions = array_merge($options,
			[
				'recursive' => !$recursive ? 0 : true,
				'as_array' => true
			]);

			// Let's reuse some code since Suites are Commands as well.
			$output = $this->describeCommand($suite, array_merge($options, $childDescriptionOptions));

			// An explicit 0 will break the loop. If the $recursive parameter is set to false instead, the loop
			// below will get executed, but only for the direct children.
			if($recursive === 0) return $this->postProcess($output, $childDescriptionOptions);

			// Loop through all direct children of the Suite.
			foreach($suite->commands($filters) as $name => $command)
			{
				$output['commands'][$name] = $this->describe($command, $childDescriptionOptions);
			}

			return $this->postProcess($output, $options);
		}

		/**
		 * Performs final string manipulations common to most (if not all) methods, based on the options passed.
		 *
		 * @param   array       $output     The array of data to process.
		 * @param   array       $options    Additional options to be considered by the Descriptor.
		 * @return  string
		 */

		protected function postProcess(array $output, array $options = [])
		{
			if(isset($options['as_array']) and $options['as_array'])
			{
				return $output;
			}

			return json_encode($output, isset($options['encoding']) ? $options['encoding'] : JSON_PRETTY_PRINT);
		}
	}