<?php namespace nyx\console\exceptions;

	// Internal dependencies
	use nyx\console\interfaces;
	use nyx\console;

	/**
	 * Command Does Not Exist Exception
	 *
	 * @package     Nyx\Console\Diagnostics
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/exceptions.html
	 */

	class CommandNotExists extends \InvalidArgumentException implements interfaces\exceptions\Renderable
	{
		/**
		 * @var string  The name of the Command that has been requested.
		 */

		private $name;

		/**
		 * @var console\Suite   The Suite in which the lookup was performed.
		 */

		private $suite;

		/**
		 * {@inheritDoc}
		 *
		 * @param   string              $name       The name of the Command that has been requested.
		 * @param   console\Suite       $suite      The Suite in which the lookup was performed.
		 */

		public function __construct($name, console\Suite $suite, $message = null, $code = 0, \Exception $previous = null)
		{
			$this->name  = $name;
			$this->suite = $suite;

			// Proceed to create a casual exception.
			parent::__construct($message ?: "The Command or Suite [$name] does not exist. [Suite: ".($suite->chain() ?: $suite->getName())."]", $code, $previous);
		}

		/**
		 * Returns the name of the Command that has been requested.
		 *
		 * @return  string
		 */

		public function getName()
		{
			return $this->name;
		}

		/**
		 * Returns the Suite in which the lookup was performed.
		 *
		 * @return  console\Suite
		 */

		public function getSuite()
		{
			return $this->suite;
		}

		/**
		 * {@inheritDoc}
		 */

		public function render(interfaces\Output $output)
		{
			$suggestions = $this->suite->suggest($this->name);

			$messages[] = '';
			$messages[] = 'The command <comment>'.$this->name.'</comment> could not be found within <info>'.($this->suite->chain() ?: $this->suite->getName()).'</info>.';
			$messages[] = '';

			if($count = count($suggestions))
			{
				if($count > 1)
				{
					$messages[] = "Did you mean one of these?";

					foreach($suggestions as $suggestion)
					{
						$messages[] = " <comment>{$suggestion}</comment>";
					}
				}
				else
				{
					$messages[] = "Did you mean <comment>{$suggestions[0]}</comment>?";
				}

				$messages[] = '';
			}

			$output->write(join(PHP_EOL, $messages), 1);
		}
	}