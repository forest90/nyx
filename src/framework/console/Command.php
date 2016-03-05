<?php namespace nyx\framework\console;

	// External dependencies
	use nyx\console;

	// Internal dependencies
	use nyx\framework;

	/**
	 * Framework Console Command
	 *
	 * @package     Nyx\Framework\Console
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/index.html
	 */

	class Command extends console\Command implements framework\interfaces\KernelAware
	{
		/**
		 * The traits of a Framework Console Command instance.
		 */

		use framework\traits\KernelAware;

		/**
		 * Calls another Console Command.
		 *
		 * @param   string                      $command
		 * @param   array                       $arguments
		 * @param   console\interfaces\Output   $output
		 * @return  int
		 */

		public function call($command, array $arguments = [], $output = null)
		{
			return $this->parent(true)->run(new console\input\Arr(array_merge(['command' => $command], $arguments)), $output);
		}
	}