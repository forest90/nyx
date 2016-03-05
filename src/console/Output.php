<?php namespace nyx\console;

	// External dependencies
	use nyx\utils;

	/**
	 * Abstract Output
	 *
	 * Fully implements interfaces\Output but introduces an abstract doWrite() method which needs to be implemented
	 * in order to perform the actual writing of the output.
	 *
	 * @package     Nyx\Console\Output
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/output.html
	 */

	abstract class Output implements interfaces\Output
	{
		/**
		 * @var interfaces\output\Formatter     A Output Formatter instance.
		 */

		private $formatter;

		/**
		 * @var int     The verbosity level of the output.
		 */

		private $verbosity;

		/**
		 * @var interfaces\Terminal     A Terminal adapter instance.
		 */

		private $terminal;

		/**
		 * @var array   The dimensions of the output. An array of two key-value pairs: [0] => width, [1] => height.
		 */

		private $dimensions;

		/**
		 * Constructs a new Output instance.
		 *
		 * @param   int                         $verbosity  The desired verbosity level.
		 * @param   bool                        $decorated  Whether to decorate messages or not (null for auto-guessing).
		 * @param   interfaces\output\Formatter $formatter  An Output formatter instance.
		 */

		public function __construct($verbosity = interfaces\Output::NORMAL, $decorated = null, interfaces\output\Formatter $formatter = null)
		{
			$this->verbosity  = null === $verbosity ? interfaces\Output::NORMAL : $verbosity;
			$this->formatter  = null === $formatter ? new output\Formatter : $formatter;

			// Are we to colorize the output?
			$decorated !== null and $this->formatter->setDecorated($decorated);

			// Prepare a Terminal adapter and a holder for the dimensions in case they get forced.
			$this->terminal = utils\System::isWindows() ? new terminals\Windows : new terminals\TTY;
			$this->dimensions = [];
		}

		/**
		 * {@inheritDoc}
		 */

		public function getFormatter()
		{
			return $this->formatter;
		}

		/**
		 * {@inheritDoc}
		 */

		public function setFormatter(interfaces\output\Formatter $formatter)
		{
			$this->formatter = $formatter;

			return $this;
		}

		/**
		 * {@inheritDoc}
		 */

		public function setDecorated($decorated)
		{
			$this->formatter->setDecorated($decorated);

			return $this;
		}

		/**
		 * {@inheritDoc}
		 */

		public function isDecorated()
		{
			return $this->formatter->isDecorated();
		}

		/**
		 * {@inheritDoc}
		 */

		public function getVerbosity()
		{
			return $this->verbosity;
		}

		/**
		 * {@inheritDoc}
		 */

		public function setVerbosity($level)
		{
			$this->verbosity = (int) $level;

			return $this;
		}

		/**
		 * {@inheritDoc}
		 */

		public function isQuiet()
		{
			return interfaces\Output::QUIET === $this->verbosity;
		}

		/**
		 * {@inheritDoc}
		 */

		public function isVerbose()
		{
			return interfaces\Output::VERBOSE <= $this->verbosity;
		}

		/**
		 * {@inheritDoc}
		 */

		public function isLoud()
		{
			return interfaces\Output::LOUD <= $this->verbosity;
		}

		/**
		 * {@inheritDoc}
		 */

		public function isDebug()
		{
			return interfaces\Output::DEBUG <= $this->verbosity;
		}

		/**
		 * {@inheritDoc}
		 */

		public function getDimensions()
		{
			return [$this->getWidth(), $this->getHeight()];
		}

		/**
		 * {@inheritDoc}
		 */

		public function setDimensions($width, $height)
		{
			$this->setWidth($width);
			$this->setHeight($height);

			return $this;
		}

		/**
		 * {@inheritDoc}
		 */

		public function getWidth()
		{
			// If the height was set manually and is not null, return the forced value.
			if(isset($this->dimensions[0])) return $this->dimensions[0];

			// Otherwise use the Terminal adapter to determine it and if that fails, just use a max int.
			return $this->terminal ? $this->terminal->getWidth() : PHP_INT_MAX;
		}

		/**
		 * {@inheritDoc}
		 */

		public function setWidth($width)
		{
			$this->dimensions[0] = null !== $width ? (int) $width : null;

			return $this;
		}

		/**
		 * {@inheritDoc}
		 */

		public function getHeight()
		{
			// If the height was set manually and is not null, return the forced value.
			if(isset($this->dimensions[1])) return $this->dimensions[1];

			// Otherwise use the Terminal adapter to determine it and if that fails, just use a max int.
			return $this->terminal ? $this->terminal->getHeight() : PHP_INT_MAX;
		}

		/**
		 * {@inheritDoc}
		 */

		public function setHeight($height)
		{
			$this->dimensions[1] = null !== $height ? (int) $height : null;

			return $this;
		}

		/**
		 * {@inheritDoc}
		 */

		public function write($messages, $newlines = 0, $format = interfaces\Output::NORMAL)
		{
			// Do not write any output if the verbosity is set to quiet.
			if(interfaces\Output::QUIET === $this->verbosity) return $this;

			// Loop through the given messages.
			foreach((array) $messages as $message)
			{
				switch($format)
				{
					// Format the message as per normal rules.
					case interfaces\Output::NORMAL:
						$message = $this->formatter->format($message);
					break;

					// Don't perform any formatting for raw output.
					case interfaces\Output::RAW: break;

					// Remove misc. tags, but leave any formatting intact.
					case interfaces\Output::PLAIN:
						$message = strip_tags($this->formatter->format($message));
					break;

					default:
						throw new \InvalidArgumentException("Unknown output type given [$format].");
				}

				$this->doWrite($message, $newlines);
			}

			return $this;
		}

		/**
		 * {@inheritDoc}
		 */

		public function writeln($messages, $format = interfaces\Output::NORMAL)
		{
			return $this->write($messages, 1, $format);
		}

		/**
		 * {@inheritDoc}
		 */

		public function ln($lines = 1)
		{
			return $this->doWrite(null, $lines);
		}

		/**
		 * Performs the actual writing of a message to the output.
		 *
		 * @param   string  $message    A message to write to the output.
		 * @param   int     $newlines   The number of newlines that should be appended after all the messages.
		 * @return  $this
		 */

		abstract protected function doWrite($message, $newlines = 0);
	}
