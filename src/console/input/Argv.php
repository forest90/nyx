<?php namespace nyx\console\input;

	// Internal dependencies
	use nyx\console;

	/**
	 * Argv Input
	 *
	 * Note: When passing an argv array yourself, ensure it does not contain the scriptname (when using $_SERVER['argv']
	 * it will be present as the first element).
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.2
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	class Argv extends console\Input
	{
		/**
		 * @var parsers\ArgvToNative    The token parser to be used.
		 */

		private $parser;

		/**
		 * Constructs a Argv Input instance..
		 *
		 * @param   array                   $argv           An array of parameters from the CLI (in the argv format).
		 * @param   parsers\ArgvToNative    $parser         An argv parser instance to use instead of the default.
		 */

		public function __construct(array $argv = null, parsers\ArgvToNative $parser = null)
		{
			$this->parser = $parser;

			// If no arguments were passed, let's use the globals returned by the CLI SAPI.
			if($argv === null)
			{
				$argv = $_SERVER['argv'];

				// Strip the script name from the arguments.
				array_shift($argv);
			}

			// Store the raw arguments.
			$this->raw = new tokens\Argv($argv);
		}

		/**
		 * {@inheritDoc}
		 */

		protected function parse()
		{
			// If we've got a parser, use it instead of constructing a new one.
			if(null === $this->parser) $this->parser = new parsers\ArgvToNative;

			// Fill our parameter bags with the parsed input.
			$this->parser->fill($this->arguments(), $this->options(), $this->raw);

			return $this;
		}
	}