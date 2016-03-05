<?php namespace nyx\console\commands\shell;

	// Internal dependencies
	use nyx\console\input;
	use nyx\console\exceptions;
	use nyx\console;

	/**
	 * Cd
	 *
	 * The "cd" Command allows you to traverse your Command hierarchy as if you were traversing your local filesystem
	 * from your average shell of choice.
	 *
	 * You can combine the following special strings into chains delimited by the delimiter set in the root Application:
	 *   ~  - root (when used midway in a chain it will reset the chain from that point onward)
	 *   .. - up one level
	 *
	 * This is a very simplistic approach to this functionality and relies on the fact that the target Suite you cd into
	 * also has the "cd" Command registered so you can continue traversing the hierarchy from that point onwards. This
	 * also means that while you can directly cd into casual Commands (in which case anything typed at the prompt will
	 * be treated as parameters for the Command), they will not contain the "cd" Command and make further traversal
	 * impossible from that point until you manually exit the shell.
	 *
	 * @package     Nyx\Console\Shell\Commands
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/commands.html
	 */

	class Cd extends Base
	{
		/**
		 * {@inheritdoc}
		 */

		protected function configure()
		{
			$this
				->setName('cd')
				->setDescription('Changes namespaces within a shell.')
				->setHelp(<<<EOF
The <info>%command.name%</info> command sets the given namespace as root for subsequent commands.

  <info>%command.fullName% testSuite</info>

You may also traverse up the namespace hierarchy or directly back to the root:

  <info>%command.fullName% ..</info>       - up one level
  <info>%command.fullName% ..%delimiter%..%delimiter%..</info> - up three levels
  <info>%command.fullName% ~</info>        - back to the root level
EOF
				)
				->setDefinition(
				[
					new input\Argument('namespace', 'The namespace name to change into.', new input\Value(input\Value::REQUIRED))
				]);

			parent::configure();
		}

		/**
		 * {@inheritDoc}
		 */

		protected function execute(console\Context $context)
		{
			$namespace = $context->getInput()->arguments()->get('namespace');
			$executive = $context->getApplication();
			$shell     = $this->getExecutingShell();

			// The string might be formatted just like a normal path, including several segments and dots denoting
			// we should move up a level. All of them are to be separated by the Application's internal delimiter
			// which is a colon by default, but might just as well be a slash or whatever is defined.
			$segments = explode($delimiter = $executive->getDelimiter(), $namespace);

			// Which 'virtual directory' are we in right now?
			$cwd = $shell->getCwd();

			foreach($segments as $segment)
			{
				if(!$segment = trim($segment)) continue;

				// Back to the root(s).
				if(0 === strpos($segment, "~") and 1 === strlen($segment))
				{
					$cwd = []; continue;
				}

				// Should we move up one level?
				if(0 === strpos($segment, ".."))
				{
					if(null === array_pop($cwd))
					{
						$context->getOutput()->writeln('You are already at the root level of the shell.'); break;
					}
				}
				else
				{
					$cwd[] = $segment;

					if(!$executive->has($chain = implode($delimiter, $cwd)))
					{
						throw new exceptions\CommandNotExists($chain, $executive);
					}

					if($executive->get($chain)->is(console\Command::DISABLED))
					{
						$context->getOutput()->writeln("Cannot cd into a disabled command [$chain].");
					}
				}
			}

			// Update the virtual cwd.
			$shell->setCwd($cwd);
		}

		/**
		 * {@inheritDoc}
		 */

		protected function replacePlaceholders($in)
		{
			return str_replace('%delimiter%', $this->root()->getDelimiter(), parent::replacePlaceholders($in));
		}
	}