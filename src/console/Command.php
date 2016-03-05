<?php namespace nyx\console;

	// External dependencies
	use nyx\core;

	/**
	 * Command
	 *
	 * Generic command class, to be extended by all specific Commands, Suites and Applications.
	 *
	 * @package     Nyx\Console\Application
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/command.html
	 */

	class Command implements core\interfaces\Named
	{
		/**
		 * The traits of a Command instance.
		 */

		use traits\Named;

		/**
		 * Command status constants.
		 */

		const DISABLED  = 1;
		const HIDDEN    = 2;
		const SINGLETON = 4;

		/**
		 * @var Suite   The direct parent of this Command.
		 */

		private $parent;

		/**
		 * @var input\Definition    A definition of the accepted input for this Command.
		 */

		private $definition;

		/**
		 * @var core\Mask   The status mask of this Command.
		 */

		private $status;

		/**
		 * @var string  The help for this Command.
		 */

		private $help;

		/**
		 * @var string  The description of this Command.
		 */

		private $description;

		/**
		 * @var callable            The code that should be run upon execution instead of the self::execute() method.
		 * @see self::setCode()
		 */

		private $code;

		/**
		 * @var mixed   The chain of command (unique identifier) of this Command, once fetched. Set to false by default
		 *              because the chain() method may return null and that is a valid value from which we need to
		 *              differentiate.
		 */

		private $chain = false;

		/**
		 * Constructs a new Command instance.
		 *
		 * @param   string              $name   The name of this Command.
		 * @throws  \LogicException             When the name of the Command is empty.
		 */

		public function __construct($name = null)
		{
			// Make sure we run the configuration before attempting to set potentially unnecessary defaults.
			$this->configure();

			// If a name was given to the constructor, it could mean that this Command (and by extension this Suite
			// or Application, since those inherit from the Command class) is injected into another one and the
			// developer wants to have it available under the given name.
			$name and $this->setName($name);

			// Make sure we've got a configured name for this Command.
			if(!$this->name) throw new \LogicException("The name of the Command cannot be empty.");
		}

		/**
		 * Configures the current command. Override this to set up the Command properly. It is automatically
		 * run during object construction.
		 */

		protected function configure(){ }

		/**
		 * Returns the status mask of the Command.
		 *
		 * @return  core\Mask
		 */

		public function status()
		{
			return $this->status ?: $this->status = new core\Mask;
		}

		/**
		 * Returns true if the status mask of this Command contains a given bit. Refer to the class constants
		 * to see what can be set in the status.
		 *
		 * @param   int     $status     The flag (status) that should be checked.
		 * @return  bool
		 */

		public function is($status)
		{
			return $this->status()->is($status);
		}

		/**
		 * Sets the parent Suite instance for this command.
		 *
		 * @param   Suite   $suite      The parent Suite.
		 * @return  $this               The current instance.
		 */

		public function strap(Suite $suite)
		{
			$this->parent = $suite;

			return $this;
		}

		/**
		 * Removes the reference to the parent Suite instance of this command.
		 *
		 * @return  $this   The current instance.
		 */

		public function unstrap()
		{
			$this->parent = null;

			return $this;
		}

		/**
		 * Returns the direct parent Suite of this Command.
		 *
		 * @param   bool    $application    Whether to return the first parent Application instead of the first direct
		 *                                  parent. It may be the same instance, but this flag will ensure we get an
		 *                                  Application instead of a possible Suite.
		 * @return  Suite|Application
		 */

		public function parent($application = false)
		{
			// Return the direct parent if we're not necessarily looking for an Application. However, even if we are,
			// if the parent is not at least a Suite, there's no point to look further.
			if(!$application or !$this->parent instanceof Suite) return $this->parent;

			// Since Suites can contain Suites and so on, we will run this recursively if need be.
			return !$this->parent instanceof Application ? $this->parent->parent(true) : $this->parent;
		}

		/**
		 * Returns the base Application instance this Command is part of.
		 *
		 * @return  Application
		 */

		public function root()
		{
			if($parent = $this->parent() and $parent->parent()) return $parent->root();

			return $parent ?: $this;
		}

		/**
		 * Returns the command chain string required to get to this Command instance from the console. This is also
		 * a unique identifier of sorts because the given chain can be used to trace this particular instance in
		 * your application, regardless of its complexity.
		 *
		 * @param   string  $to     The name of the command to use instead of this command's name.
		 * @return  string
		 */

		public function chain($to = null)
		{
			if($this->chain !== false) return $this->chain;

			$elements = [$to ?: $this->name];

			// Only add something to the chain if we've got a direct parent and the given parent also has an ancestor.
			// This will exclude the top level namespace.
			if($parent = $this->parent() and $parent->parent()) $elements[] = $parent->chain();

			return $this->chain = $parent ? implode($parent->getDelimiter(), array_reverse($elements)) : null;
		}

		/**
		 * Sets an array of argument and option instances.
		 *
		 * @param   input\Argument[]|input\bags\Arguments|input\Definition  $arguments  An array of Arguments, an Arguments
		 *                                                                              bag or an instance of a Definition.
		 * @param   input\Option[]|input\bags\Options                       $options    An array of Options or an Options
		 *                                                                              bag.
		 * @return  $this                                                               The current instance.
		 */

		public function setDefinition($arguments = null, $options = null)
		{
			$this->definition = $arguments instanceof input\Definition ? $arguments : new input\Definition($arguments, $options);

			return $this;
		}

		/**
		 * Returns the input Definition of the Command. If none is set, it will instantiate a new, empty one.
		 *
		 * @return  input\Definition
		 */

		public function getDefinition()
		{
			return $this->definition ?: $this->definition = new input\Definition;
		}

		/**
		 * Sets the description of the Command.
		 *
		 * @param   string  $description    The description of the Command.
		 * @return  $this                   The current instance.
		 */

		public function setDescription($description)
		{
			$this->description = $description;

			return $this;
		}

		/**
		 * Returns the description of the Command.
		 *
		 * @return  string
		 */

		public function getDescription()
		{
			return $this->description;
		}

		/**
		 * Sets the help for the Command.
		 *
		 * @param   string  $help   The help for the Command.
		 * @return  $this           The current instance.
		 */

		public function setHelp($help)
		{
			$this->help = (string) $help;

			return $this;
		}

		/**
		 * Returns the help for the Command.
		 *
		 * @param   bool    $replace    Whether to automatically replace the placeholders in the help string.
		 * @return  string
		 */

		public function getHelp($replace = false)
		{
			return $replace ? $this->replacePlaceholders($this->help) : $this->help;
		}

		/**
		 * Sets the code to execute when running this command. If this method is used, it overrides the code
		 * defined in the execute() method.
		 *
		 * @param   callable    $code   A callable to execute.
		 * @return  $this
		 * @see     self::execute()
		 */

		public function setCode(callable $code)
		{
			$this->code = $code;

			return $this;
		}

		/**
		 * Prepares the Command for execution and executes it.
		 *
		 * @see     self::setCode()
		 * @param   Context $context            The Context in which the Command is to be executed.
		 * @return  int                         An exit code.
		 * @throws  exceptions\CommandDisabled  When the Command to be run is disabled.
		 */

		public function prepare(Context $context)
		{
			// Ensure the Command is enabled.
			if($this->is(Command::DISABLED) or $this->parent()->is(Command::DISABLED))
			{
				throw new exceptions\CommandDisabled($context, "The command [{$this->chain()}] is disabled.");
			}

			$input     = $context->getInput();
			$executive = $context->getApplication();

			// Merge the input definition of this Command with the definition of the executing Application and bind
			// the input to it.
			$input->bind($executive->getDefinition()->merge($this->definition), $context);

			// Shall we use a closure instead of the execute() method?
			if($this->code) return call_user_func($this->code, $context);

			return $this->execute($context);
		}

		/**
		 * Executes the current Command.
		 *
		 * This method is only run when the self::setCode() had not been used to define a closure which should be run
		 * instead. If that is the case, this method needs to be extended in your concrete class, otherwise it will
		 * throw an exception.
		 *
		 * @param   Context $context    The Context in which the Command is to be executed.
		 * @return  int                 An exit code.
		 * @throws  \LogicException     When this method is not implemented in the concrete Command.
		 * @see     self::setCode()
		 */

		protected function execute(Context $context)
		{
			throw new \LogicException('The Command::execute() method must be overridden in the concrete Command unless Command::setCode() is used.');
		}

		/**
		 * Returns a string with the placeholders therein replaced by the defined replacements.
		 *
		 * @param   string  $in     The string which should be processed.
		 * @return  string          The processed help for the command.
		 * @todo                    Make overrides more sensible.
		 * @todo                    Use the Executive instead of the root application (the full chain gets displayed
		 *                          in sub-shells otherwise).
		 */

		protected function replacePlaceholders($in)
		{
			$name = $full = $this->chain();

			// When the Application is not aware that it's running in a Shell or if it's simply not running in a Shell,
			// prepend the script name to the chain.
			if(!($root = $this->root()) instanceof interfaces\shell\Aware or !$root->isRunningInShell())
			{
				$full = $_SERVER['PHP_SELF'].' '.$full;
			}

			$placeholders = ['%command.name%', '%command.fullName%'];
			$replacements = [$name,	$full];

			return str_replace($placeholders, $replacements, $in);
		}
	}