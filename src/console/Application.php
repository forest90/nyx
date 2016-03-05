<?php namespace nyx\console;

	/**
	 * Application
	 *
	 * @package     Nyx\Console\Application
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/application.html
	 */

	class Application extends Suite implements \nyx\events\interfaces\EmitterAware, interfaces\shell\Aware
	{
		/**
		 * The traits of an Application instance.
		 */

		use \nyx\events\traits\EmitterAware;
		use traits\ShellAware;

		/**
		 * @var string  The version of the application.
		 */

		private $version = 'unknown';

		/**
		 * @var bool    Whether the Application should automatically exit after fully executing a Command.
		 */

		private $autoExit = false;

		/**
		 * @var helpers\Set     The base helper Set for this Application.
		 */

		private $helpers;

		/**
		 * @var string      The name of the Command which should be run by default if no command is defined in the Input.
		 */

		private $defaultCommand = 'ls';

		/**
		 * {@inheritDoc}
		 */

		protected function configure()
		{
			// Set a generic, application-wide input Definition.
			$this->setDefinition(new input\Definition(
			[
				new input\Argument('command', 'The command to execute', new input\Value(input\Value::REQUIRED))
			],
			[
				new input\Option('help',    'h', 'Display help for the given command.'),
				new input\Option('quiet',   'q', 'Do not output any message.'),
				new input\Option('verbose', 'v', 'Increase the verbosity of the output.'),
				new input\Option('version', 'V', 'Display the version of this application.'),
				new input\Option('ansi',    '',  'Force ANSI output.'),
				new input\Option('no-ansi', '',  'Disable ANSI output.'),
				new input\Option('yes',     'y', 'Do not ask any interactive question (use the default answer).')
			]));

			// Prepare a sensible Helper Set.
			$this->helpers = new helpers\Set([new helpers\Formatter, new helpers\Dialog, new helpers\Progress, new helpers\Descriptor]);

			// Grab some default Commands from the Suite class.
			parent::configure();

			// Set up our Error and Exception Handlers. Run the conditional code only if Debug::enable() returns true,
			// ie. this call actually registers the Handlers. Since this is the default configuration and we might
			// run this is alongside other Applications, we might end up with multiple Renderers otherwise.
			if(\nyx\diagnostics\Debug::enable(null, $exceptionHandler = new diagnostics\debug\handlers\Exception))
			{
				// Add a Console Renderer Delegate to the Exception Handler so we can print pretty errors. Like dat.
				$exceptionHandler->add(new diagnostics\debug\delegates\ConsoleRenderer);
			}
		}

		/**
		 * Gets the application version.
		 *
		 * @return  string
		 */

		public function getVersion()
		{
			return $this->version;
		}

		/**
		 * Sets the application version.
		 *
		 * @param   string  $version
		 */

		public function setVersion($version)
		{
			$this->version = $version;
		}

		/**
		 * Returns true when this Application instance is the topmost Application currently being run, ie. when it has
		 * no parents.
		 *
		 * @return  bool
		 */

		public function isRoot()
		{
			return $this->parent() === null;
		}

		/**
		 * Returns the main helper Set assigned to this Application.
		 *
		 * @param   $name               $name   The name of a specific Helper if it is to be requested.
		 * @return  helpers\Set|Helper
		 */

		public function helpers($name = null)
		{
			return $name ? $this->helpers->get($name) : $this->helpers;
		}

		/**
		 * Sets whether to automatically exit after a command execution or not.
		 *
		 * @param   bool    $boolean
		 */

		public function setAutoExit($boolean)
		{
			$this->autoExit = (bool) $boolean;
		}

		/**
		 * Sets the name of the Command which should be run by default if no command is defined in the Input.
		 *
		 * @param   string  $name   The name of the Command.
		 */

		public function setDefaultCommand($name)
		{
			$this->defaultCommand = $name;
		}

		/**
		 * Returns the name of the Command which should be run by default if no command is defined in the Input.
		 *
		 * @return  string
		 */

		public function getDefaultCommand()
		{
			return $this->defaultCommand;
		}

		/**
		 * Runs the current application. Prepares a Context object for the subsequent Commands and calls self::prepare()
		 * once the global runtime preparations are done.
		 *
		 * This method is by default called only once during an application's runtime - at the beginning. If the
		 * application should include sub-applications which, for instance, need to work with a specific debugger,
		 * the specific setup should be done by overriding the self::prepare() method, which gets called whenever
		 * a (sub)application gets invoked as if it were a Command.
		 *
		 * @param   interfaces\Input    $input      An Input instance.
		 * @param   interfaces\Output   $output     An Output instance.
		 * @return  int                             An exit code. 0 if everything went fine, or an error code otherwise.
		 * @throws  \Exception                      When Command::execute() returns an Exception.
		 */

		public function run(interfaces\Input $input = null, interfaces\Output $output = null)
		{
			// If no Input or Output instances were given, let's use the defaults.
			$context = new Context($this, $input ?: $input = new input\Argv, $output ?: $output = new output\Stdout);

			// Make the Execution Context available to our Exception Handler.
			if($exceptionHandler = \nyx\diagnostics\Debug::getExceptionHandler() and $exceptionHandler instanceof diagnostics\debug\handlers\Exception)
			{
				$exceptionHandler->setContext($context);
			}

			// No need to do much further if the user is only asking for the version. Note: Having this here instead of
			// the prepare() method means it will always return the version of the root Application.
			if($input->raw()->has(['--version', '-V']))
			{
				$output->writeln($this->getName().' '.$this->getVersion()); return 0;
			}

			// Configure our I/O based on the behavioural flags available in the Input.
			$this->prepareIO($input, $output);

			// Wrap the actual code execution in a try/catch block as we don't want to have PHP automatically exit
			// the script after an uncaught exception.
			try
			{
				$code = $this->prepare($context);
			}
			catch(\Exception $exception)
			{
				// Call the Exception Handler manually so that we can finish script execution on our own with a proper
				// exit code.
				if($exceptionHandler) $exceptionHandler->handle($exception);
				// When no Exception Handler is enabled through the Debug suite, re-throw the exception so "something"
				// else may pick up where we left off?
				else throw $exception;

				// Since we got an uncaught Exception, essentially we have to return an error code, ie. > 0 to stay
				// consistent.
				$code = ($code = $exception->getCode()) ? $code : 1;
			}

			// What's the exit code?
			$code = is_numeric($code) ? $code : 0;

			// If we are to automatically exit so the developer doesn't need to return the exit code manually.
			// And if we are doing it ourselves, make sure the exit code is something valid.
			if($this->autoExit) exit(max(0, min(254, $code)));

			return $code;
		}

		/**
		 * Configures the given Input and Output instances based on themselves, ie. parses the raw input tokens looking
		 * for parameters specified in the Input Definition of this Application. Since those are usually flags that
		 * affect the behaviour of the whole Application, they need to be acted upon before we pass them along to a
		 * concrete Command. Likewise, this method is used by Shell instances to configure I/O behaviour before they
		 * pass commands to the Application.
		 *
		 * You will want to override this if your Application uses a custom Input Definition.
		 *
		 * @param   interfaces\Input    $input      An Input instance.
		 * @param   interfaces\Output   $output     An Output instance.
		 */

		public function prepareIO(interfaces\Input $input, interfaces\Output $output)
		{
			// We have only got the raw tokens at this point, as the Input is not yet bound to a definition (or at least
			// we can't assume it is), since this will be done once we move into the context of a Command and the final
			// input gets constructed (ie. bound to an Input Definition).
			$raw = $input->raw();

			// Configure colored output.
			if($raw->has('--ansi'))
			{
				$output->setDecorated(true);
			}
			elseif($raw->has('--no-ansi'))
			{
				$output->setDecorated(false);
			}

			// Answer all potential interactive questions with the defaults?
			if($raw->has(['--yes', '-y']))
			{
				$input->setInteractive(false);
			}
			// Let's make sure that we're running on a TTY at all.
			elseif(function_exists('posix_isatty') and $this->helpers()->has('dialog'))
			{
				if(!posix_isatty($this->helpers('dialog')->getInput()->getStream()->expose())) $input->setInteractive(false);
			}

			// Configure verbosity. 5 levels are supported. The default, normal verbosity, runs without specifying any
			// flags. The others can be configured using the following:
			// --quiet = discards all output;
			// --
			if($raw->has(['--quiet', '-q']))
			{
				$output->setVerbosity(interfaces\Output::QUIET);
			}
			else
			{
				if($raw->has(['-vvv', '--verbose=3']) or $raw->get('--verbose') === 3)
				{
					$output->setVerbosity(interfaces\Output::DEBUG);
				}
				elseif($raw->has(['-vv', '--verbose=2']) or $raw->get('--verbose') === 2)
				{
					$output->setVerbosity(interfaces\Output::LOUD);
				}
				elseif($raw->has(['--verbose', '--verbose=1', '-v']))
				{
					$output->setVerbosity(interfaces\Output::VERBOSE);
				}
			}
		}

		/**
		 * {@inheritDoc}
		 *
		 * Overridden to allow Applications to act as commands within other applications. The method is responsible
		 * for routing to sub-applications and handles cases when no specific Command was requested (uses the default),
		 * or help for a Command was requested.
		 *
		 * Essentially it determines which Command should be run, sets it in the Execution Context and executes said
		 * Context.
		 */

		public function prepare(Context $context)
		{
			// Let the parent Suite do some basic checks, like whether this instance is even enabled etc.
			parent::prepare($context);

			// If the Context already contains a set Command, we will execute it right away.
			if($context->hasCommand()) return $this->execute($context);

			// We still can only assume to have raw input present.
			$raw = $context->getInput()->raw();

			// If we are dealing with a sub-application, we're going to route it right away.
			if($name = $raw->first() and $command = $this->get($name) and $command instanceof Application)
			{
				// Remove the name of the sub-Application from the raw tokens.
				$raw->remove($name);

				// Redirect to the requested Application by creating a new Context and executing it.
				return (new Context($command, $context->getInput(), $context->getOutput()))->execute();
			}

			if(!$raw->has(['--help', '-h']))
			{
				if((!$name or (isset($command) and $command instanceof Suite)))
				{
					$raw->prepend($this->getDefaultCommand());
				}
			}
			else
			{
				// If we are to provide help, make sure the help command is the very first in the chain. Thus, the rest
				// will become arguments for the help command.
				$raw->prepend('help');
			}

			// Make the Command to be executed available in the Context...
			$context->setCommand($this->get($raw->first()));

			// ... and then execute it.
			return $this->execute($context);
		}

		/**
		 * Executes the given Context.
		 *
		 * @param   Context $context    The execution Context.
		 * @return  int                 An exit code. 0 if everything went fine, or an error code otherwise.
		 * @throws  \Exception          Re-throws the Exception which occurred during execution, if this applies.
		 */

		protected function execute(Context $context)
		{
			// If we've got no Event Emitter, let's execute the Command right away.
			if(null === $this->emitter) return $context->getCommand()->prepare($context);

			$this->emitter->emit(new events\ExecutionBefore($context));

			try
			{
				$code = $context->getCommand()->prepare($context);
			}
			catch(\Exception $exception)
			{
				$this->emitter->emit($event = new events\ExecutionAfter($context, $exception->getCode()));
				$this->emitter->emit($event = new events\ExecutionException($context, $exception, $event->getExitCode()));

				throw $event->getException();
			}

			$this->emitter->emit($event = new events\ExecutionAfter($context, $code));

			return $event->getExitCode();
		}
	}