<?php namespace nyx\core\exceptions;

	/**
	 * Not Exists Exception
	 *
	 * A generic exception thrown when something that was required for further code to properly run does not exist
	 * even though it was pointed to explicitly and not requested through any sort of generic find/search mechanism.
	 *
	 * @package     Nyx\Core\Exceptions
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/exceptions.html
	 */

	class NotExists extends \RuntimeException
	{

	}