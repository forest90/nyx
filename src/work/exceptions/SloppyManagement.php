<?php namespace nyx\work\exceptions;

	// Internal dependencies
	use nyx\work\interfaces;

	/**
	 * SloppyManagement Exception
	 *
	 * Exception thrown when a Manager failed a given task.
	 *
	 * @package     Nyx\Work\Workers
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/work/workers.html
	 */

	class SloppyManagement extends \RuntimeException
	{
		/**
		 * @var interfaces\Manager  The Manager instance responsible for this Exception.
		 */

		private $manager;

		/**
		 * {@inheritDoc}
		 *
		 * @param   interfaces\Manager  $manager    The manager which has thrown the exception.
		 */

		public function __construct(interfaces\Manager $manager, $message = null, $code = 0, \Exception $previous = null)
		{
			// Store a reference to the bad-mannered Manager.
			$this->manager = $manager;

			// Set the message.
			parent::__construct($message ?: "The Work Manager was unable to complete its task.", $code, $previous);
		}

		/**
		 * Returns the Manager instance responsible for this Exception.
		 *
		 * @return  interfaces\Manager
		 */

		public function getManager()
		{
			return $this->manager;
		}
	}