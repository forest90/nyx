<?php namespace nyx\deploy\pre\php\visitors;

	// Internal dependencies
	use nyx\deploy\pre\php;

	/**
	 * File Visitor
	 *
	 * Replaces all occurrences of the __FILE__ magic constant with the actual file path.
	 *
	 * @package     Nyx\Deploy\Pre
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/deploy/pre.html
	 */

	class File extends php\Visitor
	{
		/**
		 * {@inheritDoc}
		 */

		public function enterNode(\PHPParser_Node $node)
		{
			if($node instanceof \PHPParser_Node_Scalar_FileConst)
			{
				return new \PHPParser_Node_Scalar_String($this->getPath());
			}
		}
	}