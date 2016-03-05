<?php namespace nyx\diagnostics\code;

	/**
	 * Code Inspector
	 *
	 * @package     Nyx\Diagnostics\Code
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/code.html
	 */

	class Inspector
	{
		/**
		 * @var \PHPParser_Parser   The Parser in use.
		 */

		private $parser;

		/**
		 * Constructs a new Code Inspector instance.
		 *
		 * @param   \PHPParser_Parser   $parser             The Parser to use when parsing code.
		 */

		public function __construct(\PhpParser_Parser $parser = null)
		{
			$this->parser = $parser;
		}
	}