<?php namespace nyx\console\input\parsers;

	// Internal dependencies
	use nyx\console\input\bags;
	use nyx\console\input\tokens;

	/**
	 * Argv to Native Parser
	 *
	 * Takes a Raw input instance and populates the given Parameter bags based on it.
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.2
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	class ArgvToNative
	{
		/**
		 * Fill the given Argument and Option bags based on their definitions and the Raw input given.
		 *
		 * @param   bags\Arguments  $arguments      An Arguments Bag.
		 * @param   bags\Options    $options        An Options Bag.
		 * @param   tokens\Argv     $input          The Argv tokens to be parsed.
		 * @throws  \InvalidArgumentException       When the data given is not an array.
		 */

		public function fill(bags\Arguments $arguments, bags\Options $options, tokens\Argv $input)
		{
			$parseOptions = true;

			// Grab all tokens.
			$input = $input->all();

			// Loop through the input given.
			while(null !== $token = array_shift($input))
			{
				if($parseOptions and $token == '')
				{
					$arguments->add($token);
				}
				// When '--' is present as a single token, it forces us to treat everything afterwards as arguments.
				elseif($parseOptions and $token == '--')
				{
					$parseOptions = false;
				}
				// Full options.
				elseif($parseOptions and strpos($token, '--') === 0)
				{
					// Remove the two starting hyphens.
					$name = substr($token, 2);
					$value = null;

					// If the token does indeed contain a value for the option, we need to split the token accordingly.
					if(false !== $pos = strpos($name, '='))
					{
						$value = substr($name, $pos + 1);
						$name = substr($name, 0, $pos);
					}
					// If the option accepts values, we have to check if one was provided.
					elseif(null === $value and $options->definition()->get($name)->getValue()->accepts())
					{
						$next = array_shift($input);

						if('-' !== $next[0])
						{
							$value = $next;
						}
						else
						{
							array_unshift($input, $next);
						}
					}

					$options->set($name, $value);
				}
				// Shortcuts.
				elseif($parseOptions and $token[0] === '-')
				{
					// Remove the starting hyphen. If it's just a hyphen and nothing else, ignore it since it's most
					// likely a mistype.
					if(!$shortcut = substr($token, 1)) continue;

					// A nested loop. Oh noes.
					foreach($this->resolve($shortcut, $options->definition()) as $name => $value)
					{
						$options->set($name, $value);
					}
				}
				else
				{
					$arguments->add($token);
				}
			}
		}

		/**
		 * Takes a short option or short option set (without the starting hyphen) and resolves it to a full option
		 * and its value, depending on the Bag Definition given.
		 *
		 * @param   string                      $shortcut       The short option to resolve.
		 * @param   bags\definitions\Options    $definition     The Options Bag Definition.
		 * @return  array                                       An array of full option $names => $values.
		 */

		protected function resolve($shortcut, bags\definitions\Options $definition)
		{
			// We can return right here if the shortcut is a single character.
			if(1 === $length = strlen($shortcut)) return [$definition->ofShortcut($shortcut)->getName() => null];

			// We have more than one character. However, if the first character points to an option that accepts values,
			// we will treat all characters afterwards as a value for said option.
			if($option = $definition->ofShortcut($shortcut[0]) and $option->getValue()->accepts())
			{
				// First, remove the shortcut from the string to leave us only with the value. Also, if the actual value
				// starts with "=", we're going to remove that character (ie. the two first characters instead of just the
				// shortcut) to cover bad syntax.
				$value = substr($shortcut, strpos($shortcut, '=') === 1 ? 2 : 1);

				return [$option->getName() => $value];
			}

			// At this point consider the whole string as a set of different options.
			$return = [];

			// Loop through the string, character by character.
			for($i = 0; $i < $length; $i++)
			{
				$option = $definition->ofShortcut($shortcut[$i]);

				// The last shortcut in a set may have a value appended afterwards.
				if($option->getValue()->accepts())
				{
					$return[$option->getName()] = $i === $length - 1 ? null : substr($shortcut, $i + 1);
					break;
				}
				else
				{
					$return[$option->getName()] = true;
				}
			}

			return $return;
		}
	}