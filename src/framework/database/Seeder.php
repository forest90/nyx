<?php namespace nyx\framework\database;

	// External dependencies
	use nyx\console;

	// Internal dependencies
	use nyx\framework;

	/**
	 * Abstract Seeder
	 *
	 * @package     Nyx\Framework\Database
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/database/index.html
	 */

	abstract class Seeder implements interfaces\Seeder
	{
		/**
		 * The traits of a Seeder instance.
		 */

		use console\traits\ContextAware, framework\traits\KernelAware;

		/**
		 * Seed the given connection from the given path.
		 *
		 * @param  string  $class
		 * @return void
		 */

		public function call($class)
		{
			$this->resolve($class)->run();

			if($this->context)
			{
				$this->context->getOutput()->writeln("Finished seeding <info>$class</info>");
			}
		}

		/**
		 * Resolves an instance of the given Seeder class.
		 *
		 * @param   string              $class  The class to resolve.
		 * @return  interfaces\Seeder
		 */

		protected function resolve($class)
		{
			$instance = $this->kernel ? $this->kernel->make($class) : new $class;

			// Set the Application Kernel on the Seeder instance if applicable.
			if($this->kernel and $instance instanceof framework\interfaces\KernelAware)
			{
				$instance->setKernel($this->kernel);
			}

			// Set the Console Context on the Seeder instance if applicable.
			if($this->context and $instance instanceof console\interfaces\ContextAware)
			{
				$instance->setContext($this->context);
			}

			return $instance;
		}
	}