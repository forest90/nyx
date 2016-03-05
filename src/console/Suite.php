<?php namespace nyx\console;

	// External dependencies
	use nyx\core\collections;

	/**
	 * Suite
	 *
	 * A Suite is a self-registering set of Commands grouped together in a namespace. A Suite can operate both
	 * within an Application but it can also wrap an Application within its namespace, which allows for bundling
	 * together separate applications into one.
	 *
	 * @package     Nyx\Console\Application
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/suite.html
	 */

	class Suite extends Command implements \IteratorAggregate, collections\interfaces\Set
	{
		/**
		 * The traits of a Suite instance.
		 */

		use collections\traits\Set;

		/**
		 * @var array       An array of result filters.
		 */

		private $filters;

		/**
		 * @var string      The namespace delimiter. Approach with caution - change this only when *absolutely* necessary.
		 *                  If you change this, you should also extend and import the Named trait {@see traits\Named).
		 *
		 *                  Keep in mind that your Suite/Application might be extended by other developers and using
		 *                  a non-standard delimiter will force them to use the setDelimiter() method for consistency.
		 *                  If your Commands have non-standard names on top of that, extensions will have to manually
		 *                  set the names of those so they conform to the extending Application.
		 */

		private $delimiter = ':';

		/**
		 * {@inheritDoc}
		 */

		protected function configure()
		{
			$this->set([new commands\Help, new commands\Ls, new commands\Shell]);
		}

		/**
		 * Returns the delimiter used to separate namespaces in input command strings.
		 *
		 * @return  string
		 */

		public function getDelimiter()
		{
			return $this->delimiter;
		}

		/**
		 * Sets the delimiter used to separate namespaces in input command strings. This is done recursively on all
		 * children automatically and cannot be done otherwise for the sake of consistency. By default the method
		 * will check whether this Suite is currently the absolute root (ie. if it's an Application and then if it's
		 * the same instance as the main Application this Suite is part of).
		 *
		 * The above check can be omitted (and will be omitted for recursive calls automatically, to reduce overhead,
		 * since we already checked it once) but be advised that this forces the main Application to also force its own
		 * delimiter or the end-user may end up with command strings like "foo:bar-some/command".
		 *
		 * @param   string                      $delimiter      The delimiter to set.
		 * @param   bool                        $ignoreChain    Whether to ignore the chain of command.
		 * @throws  exceptions\ChainViolation                   When this Suite is not the topmost Application and we
		 *                                                      were not allowed to ignore the chain of command.
		 */

		public function setDelimiter($delimiter = ':', $ignoreChain = false)
		{
			if(!$ignoreChain and !$this instanceof Application and !$this === $root = $this->root())
			{
				throw new exceptions\ChainViolation($this, $root, 'Suite::setDelimiter() may only be called by the topmost Application.');
			}

			// At this point we're clear to go.
			$this->delimiter = $delimiter;

			// Now do that recursively.
			/* @var Suite $suite */
			foreach($this->commands(['suites', 'apps']) as $suite) $suite->setDelimiter($delimiter, true);
		}

		/**
		 * @todo    Decouple filters into separate filter sets and objects/closures.
		 *
		 * @return  callable|array|bool
		 */

		public function filters($name = null)
		{
			$this->filters === null and $this->filters =
			[
				'commands' => function(Command $command)
				{
					return !$command instanceof Suite;
				},
				'suites' => function(Command $command)
				{
					return ($command instanceof Suite and !$command instanceof Application);
				},
				'apps' => function(Command $command)
				{
					return $command instanceof Application;
				},
				'visible' => function(Command $command)
				{
					return !$command->is(Command::HIDDEN) and !$command->is(Command::DISABLED);
				},
				'hidden' => function(Command $command)
				{
					return $command->is(Command::HIDDEN) or $command->is(Command::DISABLED);
				},
				'all' => function()
				{
					return true;
				}
			];

			return $name ? (isset($this->filters[$name]) ? $this->filters[$name] : false) : $this->filters;
		}

		/**
		 * Sets a Command to this Suite.
		 *
		 * @param   Command|array   $command    A Command object or an array of them..
		 * @param   bool            $recursive  Whether to register the given Command (note: register, not add new
		 *                                      instances of) in all children Suites of this Suite.
		 * @return  $this
		 * @throws  \InvalidArgumentException   When the given argument is not an instance of Command.
		 */

		public function set($command, $recursive = false)
		{
			// Allow for the passing of arrays of commands.
			if(is_array($command))
			{
				foreach($command as $item) $this->set($item); return $this;
			}

			// Ensure the command is something we can work with.
			if(!$command instanceof Command) throw new \InvalidArgumentException("The command needs to be an instance of console\\Command.");

			// Strap the Command to this Suite.
			$command->strap($this);

			// Update its status accordingly if we're about to register it recursively.
			$recursive and $command->status()->set(Command::SINGLETON);

			// Add the command to our registry.
			return $this->register($command);
		}

		/**
		 * Unstraps the given Command from this Suite. Similarly to registration this only works for this particular
		 * Suite, not the whole hierarchy. If you want to deregister something from a child suite, Suite::get() it
		 * and then deregister the command.
		 *
		 * @param   string  $name       The name of the Command.
		 * @param   bool    $recursive  Whether to deregister the given Command in all children Suites of this Suite.
		 * @return  bool                True when the removal succeeded, false when the Command was not registered in
		 *                              this Suite in the first place.
		 */

		public function remove($name, $recursive = false)
		{
			// First let's see if the Command exists. See the method's description on why we are not simply doing ::get().
			if(isset($this->items[$name]))
			{
				// Reference shortcut.
				/* @var Command $command */
				$command = $this->items[$name];

				// Recursive Commands are available in a subset of the hierarchy but are instanced only once and strapped
				// to one parent. If this is the parent of a recursive Command, we will deregister it in the whole hierarchy.
				if($recursive or ($command->is(Command::SINGLETON) and $command->parent() === $this))
				{
					/* @var Suite $suite */
					foreach($this->commands(['suites', 'apps']) as $suite) $suite->remove($name, $recursive);
				}

				// Let the Command know that it's an orphan from now on. This applies only if this Suite is the direct
				// parent of the Command.
				$command->parent() === $this and $command->unstrap();

				// Remove the reference.
				unset($this->items[$name]); return true;
			}

			return false;
		}

		/**
		 * Returns a registered Command by its name. The name may be namespaced, but the destination Command must be
		 * a child of this Suite. The method will traverse the hierarchy recursively, albeit only downwards.
		 *
		 * @param   string                      $name       The name of the Command to look for.
		 * @return  Command|Suite                           A Command or a Suite.
		 * @param   string                      $default    The name of the default Command which should get returned
		 *                                                  if the actual Command can not be found.
		 * @throws  exceptions\CommandNotExists             When a Command or Suite defined by the name does not exist
		 *                                                  and no default was given or the default could not be found
		 *                                                  either.
		 */

		public function get($name, $default = null)
		{
			// Make sure the given key exists.
			if(!$this->has($name)) throw new exceptions\CommandNotExists($name, $this);

			// Get the base command name and the remaining route.
			list($command, $remaining) = $this->splitName($name);

			// However, if there was any remaining route, it means the Command is actually a Suite, and we need to grab
			// the actual command recursively.
			return $remaining ? $this->items[$command]->get($remaining) : $this->items[$command];
		}

		/**
		 * Checks whether the given Command or Suite defined by its name exists.
		 *
		 * @param   string  $name   The name of the Command to check for.
		 * @return  bool            True if the Command or Suite exists, false otherwise.
		 */

		public function has($name)
		{
			// Get the base command name and the remaining route.
			list($command, $remaining) = $this->splitName($name);

			// If there's something remaining in the chain *but* the command is not a suite itself, return false.
			if($remaining and (!isset($this->items[$command]) or !$this->items[$command] instanceof Suite)) return false;

			// Either check for the existence directly, or do it recursively when a remaining route is present.
			return $remaining ? $this->items[$command]->has($remaining) : isset($this->items[$command]);
		}

		/**
		 * Returns all commands of which this Suite is a direct parent of.
		 *
		 * @param   array   $filters    An array of command filter names that should be applied on the results.
		 * @param   bool    $chained    Whether command chains should be provided instead of the singular names
		 *                              as the keys in the resulting array.
		 * @return  Command[]           An array of Command instances.
		 */

		public function commands(array $filters = null, $chained = false)
		{
			// Temporary store
			$return = [];
			$filters = (array) $filters;
			$filtered = [];

			/* @var Command $command */
			foreach($this->items as $name => $command)
			{
				// If we are to use chains instead of casual names...
				$name = $chained ? $command->chain() : $name;

				if(!empty($filters))
				{
					foreach($filters as $filter)
					{
						$callable = $this->filters($filter);

						if($callable($command)) $filtered[$filter][$name] = $command;
					}
				}
				else
				{
					$return[$name] = $command;
				}
			}

			if(!empty($filtered))
			{
				foreach($filtered as $results)
				{
					$return = array_merge($return, $results);
				}
			}

			return $return;
		}

		/**
		 * Sorts an array of Commands alphabetically by their names, prioritizing normal Commands over Suites,
		 * ie. returning sorted Commands before any sorted Suites.
		 *
		 * @param   array               &$commands  An array of Commands, passed by reference.
		 * @see     Suite::commands()               For more information on the array's structure.
		 */

		public function sort(array &$commands)
		{
			$suites = [];

			// First we have to split the Suites from casual Commands.
			foreach($commands as $name => $command)
			{
				if(!$command instanceof Suite) continue;

				// Add the suite to our temporary array and unset it from the main array. We'll append it back
				// after we've run two separate sorts.
				$suites[$name] = $command;

				unset($commands[$name]);
			}

			// Perform two separate sorts on the keys.
			ksort($commands);
			ksort($suites);

			$commands = array_merge($commands, $suites);
		}

		/**
		 * Provides an array of Command names which could be relevant to the provided needle.
		 *
		 * @param   string  $needle     The string to check for.
		 * @param   bool    $chained    Whether the suggested names should include their full command chain or just
		 *                              the name.
		 * @return  array
		 */

		public function suggest($needle, $chained = false)
		{
			// In case we got an empty needle, provide all enabled commands.
			if(empty($needle)) return array_keys($this->commands(null, $chained));

			// Get the base command name and the remaining route, if applicable.
			list($needle, $remaining) = $this->splitName($needle);

			// If the base of the needle points to another Suite registered within this one, we are going to start
			// a recursive trip and provide suggestions for the last matched Suite.
			if($this->has($needle) and $command = $this->get($needle) and $command instanceof Suite)
			{
				return $command->suggest($remaining, true);
			}

			// At this point we may provide suggestions for the Commands registered in this instance.
			$suggestions = [];

			// @todo Filtering by status here seems to segfault when requested from the Shell. WTF?
			foreach($this->commands(null, $chained) as $name => $command)
			{
				// Grab the levensthein distance between our needle and the name of the Command.
				$lev = levenshtein($needle, $name);

				if(strlen($needle) / 3 >= $lev or strpos($name, $needle) !== false)
				{
					// We are temporarily going to store the levenshtein distance as the value, so that we can easily sort
					// the suggestions by relevancy afterwards.
					$suggestions[$name] = $lev;
				}
			}

			asort($suggestions);

			return array_keys($suggestions);
		}

		/**
		 * {@inheritDoc}
		 */

		public function prepare(Context $context)
		{
			// Ensure the Suite is enabled.
			if($this->is(Command::DISABLED))
			{
				throw new exceptions\CommandDisabled($context, "The suite [{$this->chain()}] is disabled.");
			}
		}

		/**
		 * Performs the actual registration of the command, ie. it adds it to our stack of Commands, regardless of the
		 * Command's status at this point. It will overwrite an already registered command with the same name.
		 *
		 * @param   Command     $command    A Command object.
		 * @return  $this
		 */

		protected function register(Command $command)
		{
			// Add the command to our registry.
			$this->items[$command->getName()] = $command;

			if($command->is(Command::SINGLETON))
			{
				/* @var Suite $suite */
				foreach($this->commands(['suites', 'apps']) as $suite) $suite->register($command);
			}

			return $this;
		}

		/**
		 * Splits the given string into a namespace and the remaining route.
		 *
		 * @param   string  $name       The name from which the namespace should be extracted.
		 * @return  array               An array of 2 items, where the first is the namespace and the second is the
		 *                              remaining string (which may also contain further namespaces).
		 */

		protected function splitName($name)
		{
			$result = explode($this->delimiter, $name, 2);

			return [$result[0], isset($result[1]) ? $result[1] : null];
		}
	}