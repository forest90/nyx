<?php namespace nyx\console;

	// External dependencies
	use nyx\core;

	/**
	 * Shell
	 *
	 * A Shell wraps an Application in an interactive process, adding an additional layer of I/O handling on top of
	 * the Application to provide a fluent workflow.
	 *
	 * The basic capabilities are limited when PHP is compiled without the Readline extension. In order to enable
	 * support for command history and autocompletion, please see {@link http://php.net/manual/readline.installation.php}.
	 *
	 * To accommodate the hierarchy of Applications within the Console component, this class makes use of 'virtual
	 * working directories' by default, which - coupled with the 'Cd' command - allow you to traverse the Application's
	 * structure as it if were a directory in your filesystem, making it easier to execute multiple commands within
	 * only parts of the Application. Consult {@see console\commands\shell\Cd} and {@see self::getCwd()} for more
	 * information.
	 *
	 * Shells work under one hard assumption - all input comes from Stdin (this goes both for when Readline is enabled
	 * and when not). It *is* possible to hack around this, but Shells are primarily meant as an interface for humans
	 * and programmatic access to Applications is better served by executing commands directly. As such, there are and
	 * most likely will be no plans to support other sources of input.
	 *
	 * Note: Shell Aware Applications are a work in progress and should not be used at all at this point.
	 *
	 * @package     Nyx\Console\Shell
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/shell.html
	 * @todo        Properly implement all methods of the Process interface (ie. timeouts, stopping, restarting etc.).
	 */

	class Shell implements core\interfaces\Process, interfaces\output\Aware
	{
		/**
		 * The traits of a Shell instance.
		 */

		use core\traits\Process{start as private traitStart;}
		use traits\OutputAware;

		/**
		 * @var Application     The Application wrapped by this Shell.
		 */

		private $application;

		/**
		 * @var array   The virtual current working directory for the Application. {@see self::getCwd()}
		 */

		private $cwd = [];

		/**
		 * @var string  The command chain of the last issued command. Used internally to detect sub-shell exits.
		 */

		private $lastCommand;

		/**
		 * @var bool    Indicates whether the Readline extension is available in this environment.
		 */

		private $hasReadline;

		/**
		 * @var string  The path to the file containing the command history, to be read by Readline when applicable.
		 *              Defaults to $HOME/.nyx_history if Readline is available.
		 */

		private $historyFilePath;

		/**
		 * @var string  The prompt to be displayed.
		 */

		private $prompt;

		/**
		 * Constructs a new Shell instance.
		 *
		 * @param   Application $application        An instance of a console Application.
		 * @param   string      $prompt             The prompt to be displayed.
		 * @param   string      $historyFilePath    The path to the file containing the command history, to be read
		 *                                          by Readline when applicable. Will not be set when the Readline
		 *                                          extension is not available. Defaults to $HOME/.nyx_history if
		 *                                          Readline is available.
		 */

		public function __construct(Application $application, $prompt = null, $historyFilePath = null)
		{
			// Make sure the Application is accessible before we set our defaults.
			$this->application = $application;

			// Override the default prompt when appropriate.
			if(null !== $prompt) $this->setPrompt($prompt);

			// If you use PHP commandline applications extensively, do consider installing the Readline extension as
			// it will make your work somewhat more bearable (and this class somewhat more usable).
			if($this->hasReadline = function_exists('readline'))
			{
				$this->setHistoryFilePath($historyFilePath ?: getenv('HOME').'/.nyx_history');
			}

			// Prepare our defaults.
			$this->configure();
		}

		/**
		 * {@inheritDoc}
		 */

		public function start()
		{
			// Run the before hook.
			$this->before();

			// Update the state we're in (let the Process trait do the basics).
			$this->traitStart();

			// Ad infinitum. Well, hopefully not.
			while($this->isRunning())
			{
				// Sub-shells break a lot of things when running in the same thread of execution, so we need to make
				// sure our basic configuration is always up to date after exiting a sub-shell. Note: It's enough to
				// check for the last command, because shells run their own loops, so whenever that loop breaks, the
				// last command we will have here will be the one that activated said loop (sub-shell).
				// This, of course, assumes you've got a command named 'shell' and that it did indeed run a shell.
				if($this->lastCommand == 'shell') $this->configure();

				// Wait for input. If the input is false (^D for instance), break the loop.
				if(false === $command = $this->readLine())
				{
					$this->output->ln(2); break;
				}

				// Update Readline's history if the extension is available.
				if($this->hasReadline)
				{
					readline_add_history($command);
					readline_write_history($this->historyFilePath);
				}

				// Instead of working with a raw command name, we need to figure out its full command chain.
				$command = implode($this->application->getDelimiter(), array_merge($this->cwd, [$command]));

				// Run it through the Application and see if it returned an error code. Don't halt execution, however,
				// just display a notification that something went fishy.
				if(0 !== $code = $this->application->run($input = new input\String($command), $this->output))
				{
					$this->output->writeln("<error> The command [$command] resulted in an error code [$code]. </error>");
				}

				// Remember the last command (string) before the next iteration as we will check against sub-shells.
				$this->lastCommand = $input->arguments()->has('command') ? $input->arguments()->get('command') : '';
			}

			// Update the state we're in (let the Process trait do the basics).
			$this->stop();

			// Run the after hook to clean up and (preferably) return an exit code.
			return $this->after();
		}

		/**
		 * Returns the Application wrapped by this Shell.
		 *
		 * @return  Application
		 */

		public function getApplication()
		{
			return $this->application;
		}

		/**
		 * Returns the virtual current working directory.
		 *
		 * This is a minor utility to reduce the amount of writing needed when working with complex applications within
		 * the shell. For instance, assuming you have the shell's default 'cd' command available and your application
		 * includes a sub-Application called 'alpha' with commands 'ls' and 'help', you could simply type 'cd alpha'
		 * and then 'help' or 'ls' to access them. Assuming 'alpha' is an Application, those will also be executed
		 * by 'alpha', not by your root application.
		 *
		 * @return  array
		 */

		public function getCwd()
		{
			return $this->cwd;
		}

		/**
		 * Sets the virtual current working directory. {@see self::getCwd()}
		 *
		 * @param   array   $dir    An array representing the elements in the chain of command with the last key being
		 *                          the final 'directory'.
		 */

		public function setCwd(array $dir)
		{
			$this->cwd = $dir;
		}

		/**
		 * Returns the command chain of the last issued command.
		 *
		 * @return  string
		 */

		public function getLastCommand()
		{
			return $this->lastCommand;
		}

		/**
		 * Checks whether the Readline extension is available in this CLI environment.
		 *
		 * @return  bool    True when the Readline extension is available in this environment, false otherwise.
		 */

		public function hasReadline()
		{
			return $this->hasReadline;
		}

		/**
		 * Returns the path to the command history file.
		 *
		 * @return  string
		 */

		public function getHistoryFilePath()
		{
			return $this->historyFilePath;
		}

		/**
		 * Sets the path to the command history file and attempts to feed it to Readline right away when the extension
		 * is available, thus making it possible to switch out histories on-the-fly (for whatever reason there may be).
		 * If the extension is not available, however, the method will silently accept whatever you give it without
		 * performing *any* sanity checks.
		 *
		 * @param   string  $path               The path to the command history file.
		 * @return  $this
		 * @throws  \InvalidArgumentException   When Readline is available and was not able to or will not be able to
		 *                                      read the given path into its memory / write to it when the time is right.
		 */

		public function setHistoryFilePath($path)
		{
			// If Readline is available, attempt to read the history right away and be vocal when it's not possible.
			if($this->hasReadline)
			{
				// This will fail when a file exists and is not readable at all or does not contain the string
				// '_HiStOrY_V2_' on its first line.
				if(is_file($path) and !readline_read_history($path))
				{
					throw new \InvalidArgumentException("The path [$path] could not be read by the Readline extension.");
				}
				// With not-yet-existing files we should be on the safe side, so just check if it's going to be possible
				// to write the file. And come on, don't you dare mention race conditions in this context.
				elseif(!is_writable(dirname($path)))
				{
					throw new \InvalidArgumentException("The path [$path] is not writable.");
				}

				$this->historyFilePath = $path;
			}

			return $this;
		}

		/**
		 * Returns the prompt that will visually (but not functionally) prepend any user input in the terminal.
		 *
		 * @return  string
		 */

		public function getPrompt()
		{
			// If a prompt was set externally, respect it.
			if(null !== $this->prompt) return $this->prompt;

			// Otherwise generate one.
			$delimiter = $this->application->getDelimiter();

			$name = $this->application->chain();
			$name = $this->application->root()->getName().($name ? $delimiter . $name : '');

			$chain = implode($delimiter, $this->cwd);

			$prompt =  PHP_EOL . date('H:i:s')." | ";
			$prompt .= "<info>$name</info>".$delimiter;
			$prompt .= ($chain and $this->application->get($chain)) ? $chain.' ' : ' ';

			return $prompt;
		}

		/**
		 * Sets the prompt that will visually (but not functionally) prepend any user input in the terminal.
		 *
		 * @param   string  $prompt
		 * @return  $this
		 */


		public function setPrompt($prompt)
		{
			$this->prompt = (string) $prompt;

			return $this;
		}

		/**
		 * Configures the wrapped Application. Separated from the before() hook because of the way subshells are handled,
		 * ie. contrary to the before() hook this method will get called whenever a subshell exits, to clean up after it
		 * and reconfigure the current shell.
		 *
		 * @return  string
		 */

		protected function configure()
		{
			// Make the 'cd' command available throughout the whole Application (including its children).
			$this->application->set(new commands\shell\Cd($this), true);
		}

		/**
		 * All operations which should be done once the Shell is run but before it enters the loop, ie. becomes
		 * interactive.
		 */

		protected function before()
		{
			// We don't want to break the whole script execution after every single command or exception.
			$this->application->setAutoExit(false);

			// Find out if we can use the Readline extension and adjust accordingly.
			if($this->hasReadline)
			{
				// Set the command history file path if none is present yet.
				$this->historyFilePath === null and $this->setHistoryFilePath(getenv('HOME').'/.history_'.$this->application->getName());

				readline_completion_function([$this, 'suggest']);
			}

			// Configure the Application's IO instances before we enter the loop. The output will remain the same for
			// the Shell (for prompts etc.) regardless of whether the output changes inside the Application's execution
			// Context itself (be aware, though, that if the Application/Command decides to output to something else
			// than Stdout you might not see anything in your console even though you will remain in the Shell).
			// Argv input is used here only once (instead of String input for the actual commands - {@see self::start()})
			// so the Application can configure the base IO objects for its start. For instance, by default, if you
			// start the Shell with a "-vvv" flag, all subsequent commands will run with this flag without you having
			// to specify it explicitly for each and every command.
			$this->application->prepareIO(new input\Argv, $this->output = new output\Stdout);

			// Print a cool header, yo.
			$this->output->writeln($this->getHeader());

			// Let a shell aware Application know what's going on...
			if($this->application instanceof interfaces\shell\Aware) $this->application->setExecutingShell($this);
		}

		/**
		 * All operations which should be done after the Shell exits its loop. The return value of this method will
		 * be returned by the self::start() method. Returning a code is not necessary but a good default.
		 *
		 * @return  int
		 */

		protected function after()
		{
			return 0;
		}

		/**
		 * Returns the Shell's header. The header is usually displayed only once, when the Shell is started.
		 *
		 * @return  string
		 */

		protected function getHeader()
		{
			return <<<EOF

  /^^ /^^  /^^   /^^/^^   /^^   <info>{$this->application->getName()}</info>
   /^^  /^^ /^^ /^^   /^ /^^    {$this->application->getVersion()}
   /^^  /^^   /^^^     /^
   /^^  /^^    /^^   /^  /^^
  /^^^  /^^   /^^   /^^   /^^

  At the prompt, type <comment>help</comment> for some help,
  or <comment>ls</comment> to get a list of available commands.

  To exit the shell, press <comment>^D</comment>.
EOF;
		}

		/**
		 * Provides basic auto-completion based on the Commands available within the Application and the assigned
		 * Input Definitions thereof. Requires the Readline extension to be enabled (this method gets registered
		 * as a auto-completion callback in the self::before() method by default).
		 *
		 * It is somewhat experimental but should work in an acceptable manner in basic use cases.
		 *
		 * @param   string      $segment    The segment of the input text where the cursor was positioned.
		 * @param   int         $position   The actual position of the cursor within said segment.
		 * @return  array|bool              An array containing the suggestions as strings or a boolean true if nothing
		 *                                  worthy of a suggestion could be found.
		 */

		protected function suggest($segment, $position)
		{
			$info = readline_info();
			$text = substr($info['line_buffer'], 0, $info['end']);

			// Make sure the cursor was at the end of the whole line.
			if($info['point'] !== $info['end']) return true;

			// Construct a temporary Input instance. Also ITT: Don't let whitspaces at the beginning mess us up.
			$input = new input\String(ltrim($text));

			// Grab the raw input locally to reduce some overhead.
			$raw = $input->raw();

			// Grab the first argument from the raw tokens, assuming it's the command name. And remove it, as we are
			// going to append it to our virtual CWD in a moment and use that as the command chain.
			$raw->remove($name = $raw->first());

			// Merge the command name with the CWD, into a usable command chain, and ensure it's the first parameter
			// of our input.
			$raw->prepend(implode($this->application->getDelimiter(), array_merge($this->cwd, [$name])));

			// Now figure out who is responsible for what is about to come. We need to find out which Application
			// would run the given Command (to grab the input definitions etc.) - the call to resolveApplication() will
			// also remove the respective parts from the command chain and give us an object instead. Next we need to
			// figure out if the remaining chain points to a standalone Suite, in which case we will use it instead
			// of the executing Application for the suggestion queries.
			$executive = $this->resolveApplication($raw, $this->application);
			$suite     = $this->resolveSuite($raw, $executive);

			// When we get no text at all or the text contains no spaces, we are safe to let the executive Application
			// (the inherited Suite class, by default) provide suggestions for Command names. If we are working within
			// a virtual CWD, the suggestions will contain command chains instead of standalone command names.
			if(empty($text) or (false === strpos($text, ' ') and false === strpos($text, '-'))) return $suite->suggest($text, empty($this->cwd));

			// At this point we can focus on providing suggestions for options.
			try
			{
				// @todo Grab the Command in a separate call and pave a way for Commands to provide their own suggestions
				// not only for options, but also for arguments.
				$definition = $executive->getDefinition()->merge($suite->get($raw->first())->getDefinition());

				// It would be great if we could work with the parsed input, but chances are the current input failed
				// validation rather miserably.
				try
				{
					$input->bind($definition);
				}
				catch(\Exception $e) {}

				// We are going to traverse the merged definition for the defined options and return all of them minus
				// those that are set (assuming the current query got successfully bound) and don't accept multiple values.
				$options = [];

				foreach($definition->options()->all() as $name => $option)
				{
					// Skip options that are set and don't accept multiple values.
					/* @var input\Option $option */
					if($input->options()->has($name) and !$option->getValue() instanceof input\values\Multiple) continue;

					$options[] = '--'.$name;
				}

				return $options;
			}
			catch(\Exception $e)
			{
				return $executive->suggest($raw->first());
			}
		}

		/**
		 * Given some raw input and an Application as base, parses the command chain contained in the input and returns
		 * the last Application contained within said chain or the base Application if no other Applications could
		 * be resolved from the chain.
		 *
		 * @param   interfaces\input\Tokens   $input    The raw input which should be parsed.
		 * @param   Application               $parent   The Application to use as base of the resolution.
		 * @return  Application
		 */

		protected function resolveApplication(interfaces\input\Tokens $input, Application $parent)
		{
			$name = $input->first();

			if($name and $parent->has($name) and ($child = $parent->get($name)) instanceof Application)
			{
				$input->remove($name);

				return $this->resolveApplication($input, $child);
			}

			return $parent;
		}

		/**
		 * Given some raw input and a Suite (might just as well be an Application) as base, parses the command chain
		 * contained in the input and returns the last Suite contained within said chain or the base Suite if no other
		 * Suites could be resolved from the chain.
		 *
		 * @param   interfaces\input\Tokens   $input    The raw input which should be parsed.
		 * @param   Suite                     $parent   The Suite to use as base of the resolution.
		 * @return  Suite
		 */

		protected function resolveSuite(interfaces\input\Tokens $input, Suite $parent)
		{
			$name = $input->first();

			if($name and $parent->has($name) and ($child = $parent->get($name)) instanceof Suite)
			{
				$input->remove($name);

				return $child;
			}

			return $parent;
		}

		/**
		 * Reads a single line from standard input and returns it.
		 *
		 * @return  string
		 */

		protected function readLine()
		{
			// Well, having Readline makes this simple.
			if($this->hasReadline) return readline($this->output->getFormatter()->format($this->getPrompt()));

			// Otherwise fall back to basic support.
			$this->output->write($this->getPrompt());

			// @todo Consider plugging connect\Stream into this if a fairly robust Readline replacement is to be made.
			return ((!$line = fgets(STDIN, 1024)) and strlen($line) == 0) ? false : rtrim($line);
		}
	}