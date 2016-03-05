<?php namespace nyx\console\output;

	// Internal dependencies
	use nyx\console;

	// External dependencies
	use nyx\connect\streams;

	/**
	 * Standard Output
	 *
	 * Merely a convenient wrapper for the Stream output since stdout is the default used by this component. It also
	 * wraps a errors Stream (stderr by default) on top of that and overrides certain setters so changes are applied
	 * both to the standard output and the error output simultaneously.
	 *
	 * @package     Nyx\Console\Output
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/output.html
	 */

	class Stdout extends Stream implements console\interfaces\output\ErrorAware
	{
		/**
		 * The traits of a Stdout Output instance.
		 */

		use console\traits\ErrorOutputAware;

		/**
		 * {@inheritDoc}
		 */

		public function __construct($verbosity = self::NORMAL, $decorated = null, console\interfaces\output\Formatter $formatter = null)
		{
			// IBM iSeries (OS400) systems have issues with converting ASCII to EBCDIC, so we need to account for that.
			$stream = ('OS400' != php_uname('s')) ? 'stdout' : 'output';

			// Set up the stream resource for the output.
			parent::__construct(new streams\Stream("php://{$stream}", 'w'), $verbosity, $decorated, $formatter);

			// And create a Stream Output instance for errors as well.
			$this->errorOutput = new Stream(new streams\Stream("php://stderr", 'w'), $verbosity, $decorated, $formatter);
		}

		/**
		 * {@inheritDoc}
		 */

		public function setFormatter(console\interfaces\output\Formatter $formatter)
		{
			parent::setFormatter($formatter);

			return $this->errorOutput->setFormatter($formatter);
		}

		/**
		 * {@inheritDoc}
		 */

		public function setDecorated($decorated)
		{
			parent::setDecorated($decorated);

			return $this->errorOutput->setDecorated($decorated);
		}

		/**
		 * {@inheritDoc}
		 */

		public function setVerbosity($level)
		{
			parent::setVerbosity($level);

			return $this->errorOutput->setVerbosity($level);
		}
	}