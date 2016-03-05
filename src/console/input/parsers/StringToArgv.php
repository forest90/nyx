<?php namespace nyx\console\input\parsers;

	// Internal dependencies
	use nyx\console\interfaces;

	/**
	 * String to Argv Parser
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.2
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	class StringToArgv
	{
		const REGEX_STRING        = '([^ ]+?)(?: |(?<!\\\\)"|(?<!\\\\)\'|$)';
		const REGEX_QUOTED_STRING = '(?:"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\')';

		/**
		 * {@inheritDoc}
		 *
		 * @throws  \InvalidArgumentException   When the data given is not a string.
		 * @throws  \RuntimeException           When unable to parse a given substring.
		 */

		public function parse($input)
		{
			// Ensure we're dealing with a string before we do anything fancy.
			if(!is_string($input)) throw new \InvalidArgumentException('A string parser can only parse strings, '.gettype($input).' given');

			// Change line breaks, tabs etc. into whitespaces.
			$input = preg_replace('/(\r\n|\r|\n|\t)/', ' ', $input);

			$argv   = [];
			$length = strlen($input);
			$cursor = 0;

			while($cursor < $length)
			{
				if(preg_match('/\s+/A', $input, $match, null, $cursor))
				{
				}
				elseif(preg_match('/([^="\' ]+?)(=?)('.self::REGEX_QUOTED_STRING.'+)/A', $input, $match, null, $cursor))
				{
					$argv[] = $match[1].$match[2].stripcslashes(str_replace(['"\'', '\'"', '\'\'', '""'], '', substr($match[3], 1, strlen($match[3]) - 2)));
				}
				elseif(preg_match('/'.self::REGEX_QUOTED_STRING.'/A', $input, $match, null, $cursor))
				{
					$argv[] = stripcslashes(substr($match[0], 1, strlen($match[0]) - 2));
				}
				elseif(preg_match('/'.self::REGEX_STRING.'/A', $input, $match, null, $cursor))
				{
					$argv[] = stripcslashes($match[1]);
				}
				else
				{
					throw new \InvalidArgumentException(sprintf('Unable to parse input near "... %s ..."', substr($input, $cursor, 10)));
				}

				$cursor += strlen($match[0]);
			}

			return $argv;
		}
	}