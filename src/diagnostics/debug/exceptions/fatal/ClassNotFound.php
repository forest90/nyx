<?php namespace nyx\diagnostics\debug\exceptions\fatal;

	// Internal dependencies
	use nyx\diagnostics\debug\exceptions;

	/**
	 * Class Not Found Fatal Error Exception
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	class ClassNotFound extends exceptions\FatalError
	{
		/**
		 * {@inheritDoc}
		 */

		public function __construct($message, \ErrorException $previous)
		{
			parent::__construct($message, $previous->getCode(), $previous->getSeverity(), $previous->getFile(), $previous->getLine(), $previous->getPrevious());
		}
	}