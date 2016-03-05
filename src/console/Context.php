<?php namespace nyx\console;

	/**
	 * Execution Context
	 *
	 * Contexts are created automatically by Applications during the execution procedure, based on the Input and Output
	 * an Application is given.
	 *
	 * However, a Context may also be used to directly execute a Command or it could be directly run {@see self::execute()}.
	 * The latter can be useful, for instance, when a Context gets modified by events or in a Command and you want to
	 * ensure that the modified Context gets fully executed. It could also be used to execute other Commands with the
	 * same I/O (think somewhere along the lines of HMVC requests).
	 *
	 * @package     Nyx\Console\Application
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/context.html
	 */

	class Context implements interfaces\input\Aware, interfaces\output\Aware
	{
		/**
		 * The traits of a Context instance.
		 */

		use traits\InputAware, traits\OutputAware;

		/**
		 * @var Application         The Application in use within the Context. Read-only.
		 */

		private $application;

		/**
		 * @var Command             The Command in use within the Context. Set explicitly after the construction of the
		 *                          Execution Context.
		 */

		private $command;

		/**
		 * Creates a new execution Context.
		 *
		 * As a bare minimum, a Context requires at least an Application and I/O instances. Commands may be set explicitly
		 * after the Context is already created.
		 *
		 * @param   Application         $application    The Application instance to be used.
		 * @param   interfaces\Input    $input          The Input instance to be used or null to use the default of Argv.
		 * @param   interfaces\Output   $output         The Output instance to be used or null to use the default of Stdout.
		 */

		public function __construct(Application $application, interfaces\Input $input = null, interfaces\Output $output = null)
		{
			$this->application = $application;
			$this->input       = $input  ?: new input\Argv;
			$this->output      = $output ?: new output\Stdout;
		}

		/**
		 * Returns the Application in use within the Context.
		 *
		 * @return  Application
		 */

		public function getApplication()
		{
			return $this->application;
		}

		/**
		 * Returns the Command in use within the Context.
		 *
		 * @return  Command
		 */

		public function getCommand()
		{
			return $this->command;
		}

		/**
		 * Sets the Command to be used within the Context.
		 *
		 * @param   Command $command
		 * @return  $this
		 */

		public function setCommand(Command $command)
		{
			$this->command = $command;

			return $this;
		}

		/**
		 * Checks if this Context has a Command already set.
		 *
		 * @return  bool    True if a Command is set, false otherwise.
		 */

		public function hasCommand()
		{
			return null !== $this->command;
		}

		/**
		 * Executes this Context.
		 *
		 * If a Command is set in the Context, the Command will be executed directly with this Context as argument
		 * unless the $ignoreSetCommand argument is set to true. If no Command is given, the current Input and Output
		 * will be passed to the set Application instead.
		 *
		 * @param   bool    $ignoreSetCommand   Whether the currently set Command should be ignored if set, running
		 *                                      the underlying Application instead.
		 * @return  Command
		 */

		public function execute($ignoreSetCommand = false)
		{
			// If a Command is set in the Context and we are told not to ignore it, bypass an Application's run() method
			// and pass it directly to the prepare() method, which in turn will directly execute it since this Context
			// already contains a Command.
			if(!$ignoreSetCommand and $this->command) return $this->application->prepare($this);

			// Otherwise we will run the Input through the executive Application set in this Context.
			return $this->application->run($this->input, $this->output);
		}
	}