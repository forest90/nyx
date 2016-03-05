<?php namespace nyx\console\exceptions;

	/**
	 * Command Disabled Exception
	 *
	 * Exception thrown when the execution of a Command that is disabled has been requested. Such Commands may be
	 * available in the hierarchy when they are registered but they themselves decided that they cannot function
	 * properly in the current environment and the developer has not removed them based on that status.
	 *
	 * @package     Nyx\Console\Diagnostics
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/exceptions.html
	 */

	class CommandDisabled extends Execution
	{

	}