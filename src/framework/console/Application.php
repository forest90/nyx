<?php namespace nyx\framework\console;

	// External dependencies
	use nyx\console;

	// Internal dependencies
	use nyx\framework;

	/**
	 * Framework Console Application
	 *
	 * @package     Nyx\Framework\Console
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/index.html
	 */

	class Application extends console\Application implements framework\interfaces\KernelAware
	{
		/**
		 * The traits of a Framework Console Application instance.
		 */

		use framework\traits\KernelAware;

		/**
		 * {@inheritDoc}
		 *
		 * @param   framework\Kernel    $kernel     The Application Kernel.
		 */

		public function __construct(framework\Kernel $kernel, $name = 'Nyx')
		{
			$this->kernel = $kernel;

			// Run the whole hierarchical construction, down to the Command level.
			parent::__construct($name);
		}

		/**
		 * {@inheritDoc}
		 *
		 * Overridden to boot the Framework before we do anything else.
		 */

		public function run(console\interfaces\Input $input = null, console\interfaces\Output $output = null)
		{
			$this->kernel->boot();

			parent::run($input, $output);
		}

		/**
		 * {@inheritDoc}
		 *
		 * Overridden to automatically set the Application Kernel in KernelAware Commands.
		 */

		protected function execute(console\Context $context)
		{
			if(($command = $context->getCommand()) instanceof framework\interfaces\KernelAware)
			{
				$command->setKernel($this->kernel);
			}

			return parent::execute($context);
		}
	}