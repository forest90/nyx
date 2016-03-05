<?php namespace nyx\console\input;

	/**
	 * String Input
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	class String extends Argv
	{
		/**
		 * Constructs a String Input instance.
		 *
		 * @param   string                  $input          A string containing the arguments and options in an argv format.
		 * @param   parsers\StringToArgv    $parser         A string parser instance to use instead of the default.
		 */

		public function __construct($input, parsers\StringToArgv $parser = null)
		{
			// No point in storing it since we're only doing this once.
			if(null === $parser) $parser = new parsers\StringToArgv;

			parent::__construct($parser->parse($input));
		}
	}