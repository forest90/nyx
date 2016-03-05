<?php namespace nyx\console\exceptions;

	/**
	 * Chain of Command Violation Exception
	 *
	 * Exception thrown when a Command instance attempts to perform an action that should only be done by its superiors.
	 *
	 * @package     Nyx\Console\Diagnostics
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/exceptions.html
	 */

	class ChainViolation extends Hierarchy
	{

	}