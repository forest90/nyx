<?php namespace nyx\console\descriptors;

	// Internal dependencies
	use nyx\console\input;
	use nyx\console;

	/**
	 * Text Descriptor
	 *
	 * @package     Nyx\Console\Descriptors
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/descriptors.html
	 */

	class Text extends console\Descriptor
	{
		/**
		 * {@inheritDoc}
		 */

		public function describeInputArgument(input\Argument $argument, array $options = [])
		{
			$width = isset($options['width']) ? $options['width'] : strlen($argument->getName());

			$output = str_replace("\n", "\n".str_repeat(' ', $width + 2), $argument->getDescription());
			$output = sprintf(" <info>%-${width}s</info> %s%s", $argument->getName(), $output, $this->describeInputValue($argument->getValue()));

			return $this->postProcess($output, $options);
		}

		/**
		 * {@inheritDoc}
		 */

		public function describeInputOption(input\Option $option, array $options = [])
		{
			$value = $option->getValue();

			$width = isset($options['width']) ? $options['width'] : strlen($option->getName());
			$width = $width - strlen($option->getName()) - 2;

			$output = sprintf(" <info>%s</info> %-${width}s%s%s",
				'--'.$option->getName(),
				$option->getShortcut() ? sprintf('(-%s) ', $option->getShortcut()) : '',
				str_replace("\n", "\n".str_repeat(' ', $width + 2), $option->getDescription()),
				$this->describeInputValue($value)
			);

			return $this->postProcess($output, $options);
		}

		/**
		 * {@inheritDoc}
		 */

		public function describeInputValue(input\Value $value, array $options = [])
		{
			$default = $value->getDefault();
			$displayDefault = false;
			$output = ' <comment>';

			if($default !== null and $value->accepts() and (!is_array($default) or !empty($default)))
			{
				$displayDefault = true;
				$output .= '(default: '.json_encode($default, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

				// Close the parenthesis if we're not going to print out the multiple values allowed info.
				if(!$value instanceof input\values\Multiple) $output .= ')';
			}

			if($value instanceof input\values\Multiple)
			{
				// Either continue within the same parenthesis when the value had a usable default, or assume
				// we're just starting the description and thus open a new one.
				$output .= $displayDefault ? ', ' : '(';
				$output .= 'multiple values allowed)';
			}

			$output .= '</comment>';

			return $this->postProcess($output, $options);
		}

		/**
		 * {@inheritDoc}
		 */

		public function describeInputDefinition(input\Definition $definition, array $options = [])
		{
			// Alias the variable as we'll overwrite it in a second with the actual input options from the Definition.
			$descriptionOptions = $options;

			// Let's grab the input parameters once as we will refer to them quite a few times.
			$arguments = $definition->arguments()->all();
			$options   = $definition->options()->all();

			// First we need to determine the maximal width of the parameters so we can pad their names properly
			// before printing out their descriptions.
			$width = 0;

			/** @var input\Option $option */
			foreach($options as $option)
			{
				$nameLength = strlen($option->getName()) + 2;

				if($option->getShortcut()) $nameLength += strlen($option->getShortcut()) + 3;

				$width = max($width, $nameLength);
			}

			/** @var input\Argument $argument */
			foreach($arguments as $argument)
			{
				$width = max($width, strlen($argument->getName()));
			}

			++$width;

			// Now let's move on to actually describing the parameters from the Definition.
			$output = [];

			if($arguments)
			{
				$output[] = '<comment>Arguments:</comment>';

				foreach($arguments as $argument) $output[] = $this->describeInputArgument($argument, ['width' => $width]);

				$output[] = '';
			}

			if($options)
			{
				$output[] = '<comment>Options:</comment>';

				foreach($options as $option) $output[] = $this->describeInputOption($option, ['width' => $width]);

				$output[] = '';
			}

			return $this->postProcess($output, $descriptionOptions);
		}

		/**
		 * {@inheritDoc}
		 */

		public function describeCommand(console\Command $command, array $options = [])
		{
			$definition = $command->getDefinition();

			$output = [
				'<comment>Usage:</comment>',
				' '.trim(sprintf('%s %s', $command->chain(), $this->getCommandSynopsis($command))),
				'',
				$this->describeInputDefinition($definition),
			];

			if($help = $command->getHelp(true))
			{
				$output[] = '<comment>Help:</comment>';
				$output[] = ' '.str_replace("\n", "\n ", $help);
			}

			return $this->postProcess($output, $options);
		}

		/**
		 * {@inheritDoc}
		 */

		public function describeSuite(console\Suite $suite, array $options = [])
		{
			// Figure out which options to use.
			$recursive = isset($options['recursive']) ? $options['recursive'] : false;
			$filters = isset($options['filters']) ? $options['filters'] : [];
			$raw = (isset($options['raw']) and $options['raw']);

			// Grab the command descriptions.
			$output = $this->getCommandList($suite, $raw, $recursive, $filters);

			// Fully formatted (potentially colored) output.
			if(!$raw)
			{
				$count = count($output);

				// Prepend the following lines to the command descriptions.
				$output = array_merge([
					$suite->getHelp(true),
					'<comment>'.$count.' command'.($count == 1 ? '' : 's').' available within the "'.($suite->chain() ?: 'root').'" namespace '.($recursive ? 'and its children' : '').'</comment>',
					$filters ? 'Including: '.implode(', ', $filters) : 'No filters applied.',
					''
				], $output);
			}

			return $this->postProcess($output, $options);
		}

		/**
		 * Returns an array of lines containing command chains (and their descriptions, if this applies) in the given
		 * format.
		 *
		 * @param   console\Suite   $suite      The Suite for which the descriptions are to be provided.
		 * @param   bool            $raw        Whether the output should be raw, ie. exclude decorators etc.
		 * @param   bool            $recursive  Whether Commands of children should also be included in the results.
		 * @param   array           $filters    The filters to be applied on the results. {@see self::commands()}
		 * @param   int             $width      The maximal width of the first resulting column (ie. the command chains).
		 * @param   int             $depth      Recursion depth. Used internally to properly format nested Commands.
		 * @return  array                       The resulting descriptions in the given format.
		 */

		protected function getCommandList(console\Suite $suite, $raw = false, $recursive = false, array $filters = [], $width = null, $depth = 0)
		{
			// Grab the direct children of this namespace and make sure they are sorted alphabetically.
			$commands = $suite->commands($filters, $raw); $suite->sort($commands);

			// The maximal width needs to be available right at the beginning, therefore the whole hierarchy needs to
			// be traversed before we start building the output. This adds some overhead, but is necessary.
			$width === null and $width = $this->calculateCommandListWidth($commands, $raw, $recursive, 0, $filters);

			$messages = [];
			$repeat = 1 * $depth;

			foreach($commands as $name => $command)
			{
				$disabled = $command->is(console\Command::DISABLED);

				if($raw)
				{
					// Raw output is not formatted. The commands will be separated by line breaks. Each line will contain
					// a command chain and a description separated by whitespaces.
					$messages[] = sprintf("%-${width}s %s", $name, $command->getDescription());
				}
				elseif(!$command instanceof console\Suite)
				{
					// Full output will create a tree-like, formatted view of the command hierarchy.
					$messages[] = sprintf(" <info>%-${width}s</info> %s", str_repeat('|-', $repeat).' '.$name, ($disabled ? '<important>(Disabled)</important> ' : '').$command->getDescription());
				}
				else
				{
					$header = str_repeat('|-', $repeat > 1 ? $repeat - 1 : 0).($depth ? '|-+ ' : '+ ').$name;
					$padding = $width- strlen($header);

					// Suite names are formatted differently than casual commands in the full view.
					if($recursive and !$depth) $messages[] = '';

					if($disabled)
					{
						$messages[] = sprintf(" <error>%s</error>%-${padding}s %s", $header, ' ', '<important>(Disabled)</important> '.$command->getDescription());
					}
					else
					{
						$messages[] = sprintf(" <header>%s</header>%-${padding}s %s", $header, ' ', $command->getDescription());
					}
				}

				// Time to traverse the whole subtree if we are asked to do it recursively.
				if($recursive and $command instanceof console\Suite)
				{
					$messages = array_merge($messages, $this->getCommandList($command, $raw, $recursive, $filters, $width, $depth + 1));
				}
			}

			return $messages;
		}

		/**
		 * Determines the maximal width of the command chains in the given format. Used internally to provide padding
		 * for the command list to make it legible.
		 *
		 * @param   array   $commands   The Commands that should be used for the calculations.
		 * @param   bool    $raw        Whether the output should be raw, ie. exclude decorators etc.
		 * @param   bool    $recursive  Whether Commands of children should also be accounted for.
		 * @param   int     $total      The current maximal width. Used internally to keep track of the value during
		 *                              recursions.
		 * @param   array   $filters    The filters to be applied on the results. {@see self::commands()}
		 * @return  int                 The resulting width.
		 */

		protected function calculateCommandListWidth(array $commands, $raw, $recursive = false, $total = 0, array $filters = [])
		{
			/** @var console\Command $command */
			foreach($commands as $command)
			{
				if($recursive and $command instanceof console\Suite)
				{
					/** @var console\Suite $command */
					$current = $this->calculateCommandListWidth($command->commands($filters), $raw, $recursive, $total + 1, $filters);
				}
				else
				{
					$current = strlen($raw ? $command->chain() : $command->getName()) + 6;
				}

				$total = $total < $current ? $current : $total;
			}

			return $total;
		}

		/**
		 * Performs final string manipulations common to most (if not all) methods, based on the options passed.
		 *
		 * @param   string|array    $output     The string or an array of strings to process.
		 * @param   array           $options    Additional options to be considered by the Descriptor.
		 * @return  string
		 */

		protected function postProcess($output, array $options = [])
		{
			// Handle arrays of strings.
			$output = implode("\n", (array) $output);

			return (isset($options['raw']) and $options['raw']) ? strip_tags($output) : $output;
		}
	}