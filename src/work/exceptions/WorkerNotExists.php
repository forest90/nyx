<?php namespace nyx\work\exceptions;

	// Internal dependencies
	use nyx\work\interfaces;

	/**
	 * WorkerNotExists Exception
	 *
	 * Exception thrown when a requested Worker could not be found.
	 *
	 * @package     Nyx\Work\Workers
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/work/workers.html
	 */

	class WorkerNotExists extends SloppyManagement
	{
		/**
		 * {@inheritDoc}
		 *
		 * @param   string              $name       The name of the Job which couldn't be found.
		 * @param   interfaces\Manager  $manager    The manager which has thrown the exception.
		 */

		public function __construct($name, interfaces\Manager $manager, $message = null, $code = 0, \Exception $previous = null)
		{
			// Set the message.
			parent::__construct($manager, $message ?: "The Work Manager was unable to find the Worker [$name].");
		}
	}