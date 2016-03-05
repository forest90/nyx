<?php namespace nyx\work\interfaces;

	// External dependencies
	use nyx\core;

	/**
	 * Worker Interface
	 *
	 * @package     Nyx\Work\Workers
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/work/workers.html
	 */

	interface Worker extends core\interfaces\Process
	{
		/**
		 * Returns the name of this instance.
		 *
		 * @return  string
		 */

		public function getName();

		/**
		 * Returns the Manager responsible for this Worker.
		 *
		 * @return  Manager
		 */

		public function getManager();

		/**
		 * Exposes the jobs supported by this Worker.
		 *
		 * @return  array
		 */

		public function getSupported();

		/**
		 * Returns the instance limit for this Worker.
		 *
		 * @return  int
		 */

		public function getInstanceLimit();

		/**
		 * Sets the instance limit for this Worker.
		 *
		 * @param   int $amount
		 */

		public function setInstanceLimit($amount);

		/**
		 * Describes a function so the Worker can support it.
		 *
		 * @param   string  $function           The name of the function.
		 * @param   array   $description        The description, which must at least contain a 'method' key pointing to
		 *                                      the method within the Worker responsible for handling the given function.
		 * @throws  \InvalidArgumentException
		 */

		public function describe($function, array $description);
	}