<?php namespace nyx\diagnostics\code;

	// External dependencies
	use nyx\storage;

	/**
	 * File
	 *
	 * Represents a File containing PHP code.
	 *
	 * @package     Nyx\Diagnostics\Code
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/code.html
	 * @todo        Strip comments/whitespaces on read?
	 * @todo        Properly hook into nyx\storage\file once it's done.
	 * @todo        Check if the file is readable. Handle appropriately (nyx\storage).
	 */

	class File extends \SplFileInfo
	{
		/**
		 * @var \PHPParser_Parser   The Parser in use.
		 */

		private $parser;

		/**
		 * {@inheritDoc}
		 *
		 * Overridden to ensure the file exists and to allow to set a Parser during construction.
		 *
		 * @param   \PHPParser_Parser   $parser             The Parser to use when parsing the code in this File.
		 * @throws  storage\file\exceptions\FileNotExists   When the given path does not point to an existing file or
		 *                                                  or the file is actually a directory.
		 */

		public function __construct($path, \PhpParser_Parser $parser = null)
		{
			parent::__construct($path);

			// Perform the check now, not sooner, as we want the Exception to get the path that we tried to set.
			if(!$this->isFile()) throw new storage\file\exceptions\FileNotExists($this);

			$this->parser = $parser;
		}

		/**
		 * Parses the contents of the File into statement nodes.
		 *
		 * @param   \PHPParser_Parser   $parser     The Parser to use when parsing the code in this File. If not given,
		 *                                          the Parser set in this instance will be used. If that is not present
		 *                                          either, a new default Parser will be instantiated and set in the
		 *                                          instance as well.
		 * @return  \PHPParser_Node[]               An array of statements.
		 * @throws  \PHPParser_Error                When the Parser encounters a syntax error.
		 */

		public function parse(\PhpParser_Parser $parser = null)
		{
			// See the description of the $parser parameter.
			if(null === $parser and null === $parser = $this->parser)
			{
				$parser = $this->parser = new \PHPParser_Parser(new \PHPParser_Lexer());
			}

			return $parser->parse(file_get_contents($this->getPath()));
		}
	}