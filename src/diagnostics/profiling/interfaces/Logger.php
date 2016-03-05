<?php namespace nyx\diagnostics\profiling\interfaces;

	/**
	 * Debug Logger Interface
	 *
	 * @package     Nyx\Diagnostics\Profiling
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/profiling/index.html
	 */

	interface Logger
	{
		/**
		 * Returns an array of logs. A log is an array with a minimum of 4 mandatory keys: timestamp, message, priority
		 * and priorityName. It may also include an optional context key containing an array with additional data.
		 *
		 * @return  array   An array of logs.
		 */

		public function getLogs();

		/**
		 * Returns the number of logs classified as errors.
		 *
		 * @return  int
		 */

		public function countErrors();
	}