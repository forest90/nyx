<?php namespace nyx\console\diagnostics\debug\delegates;

	// External dependencies
	use nyx\connect\streams;
	use nyx\diagnostics\debug;

	// Internal dependencies
	use nyx\console;

	/**
	 * Console Renderer Delegate
	 *
	 * Handles exceptions by rendering them either in the Output instance currently available in the Execution Context
	 * (or the ErrorOutput instance in case of Stdout) or in Stderr by default when no such Context is made available
	 * to this Delegate.
	 *
	 * The amount of information rendered and the way it is presented varies depending on the desired verbosity of the
	 * Output (which will be normal verbosity if the Delegate has to instantiate Stderr itself).
	 *
	 * @package     Nyx\Console\Diagnostics\Debug
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/diagnostics.html
	 */

	class ConsoleRenderer implements debug\interfaces\Delegate
	{
		/**
		 * @var console\helpers\Formatter   A Formatter Helper instance to deal with rendering error blocks.
		 */

		private $helper;

		/**
		 * {@inheritDoc}
		 */

		public function handle(debug\Inspector $inspector)
		{
			// Let's check how reliable our Inspector is. See what Output instance we can get.
			if($inspector instanceof console\diagnostics\debug\Inspector and $inspector->hasContext())
			{
				// So far, so good. We can reuse the Output instance already in use.
				$output = $inspector->getContext()->getOutput();

				// But we can do even better if it's a Stdout instance, in which case we can stream to the defined
				// error Output instance and stay consistent.
				if($output instanceof console\interfaces\output\ErrorAware) $output = $output->getErrorOutput();
			}
			// Otherwise we need to fall back to creating an Stderr Output instance manually.
			else
			{
				$output = new console\output\Stream(new streams\Stream("php://stderr", 'w'));
			}

			// Are we dealing with an Exception that can render itself?
			if($exception = $inspector->getException() and $exception instanceof console\interfaces\exceptions\Renderable)
			{
				$exception->render($output);
			}
			else
			{
				$this->render($inspector, $output);
			}
		}

		/**
		 * Returns the Formatter Helper instance in use to deal with rendering error blocks. If none is set, a new
		 * instance will be created.
		 *
		 * @return  console\helpers\Formatter
		 */

		public function getHelper()
		{
			return $this->helper ?: $this->helper = new console\helpers\Formatter;
		}

		/**
		 * Sets the Formatter Helper instance to be used to deal with rendering error blocks.
		 *
		 * @param   console\helpers\Formatter   $formatter
		 */

		public function setHelper(console\helpers\Formatter $formatter)
		{
			$this->helper = $formatter;
		}

		/**
		 * Renders the data an Inspector contains about the Exception inside the given Output.
		 *
		 * @param   debug\Inspector             $inspector  An Exception Inspector instance.
		 * @param   console\interfaces\Output   $output     An Output instance.
		 */

		protected function render(debug\Inspector $inspector, console\interfaces\Output $output)
		{
			$exception = $inspector->getException();

			$output->ln();
			$output->write($this->getHelper()->block([sprintf('Error: [%s]', get_class($exception)), $exception->getMessage()], 'error', true, $output->getWidth()), 2);

			// If the verbosity of the Output instance is higher than normal, we will also render a backtrace based
			// on the frames the Inspector has available.
			if(console\interfaces\Output::VERBOSE <= $output->getVerbosity())
			{
				$this->renderTrace($inspector->getTrace(), $output);
			}
		}

		/**
		 * Renders the data an Inspector contains about the Exception inside the given Output.
		 *
		 * @param   debug\Trace                 $trace      A Trace instance containing the backtrace frames.
		 * @param   console\interfaces\Output   $output     An Output instance.
		 */

		protected function renderTrace(debug\Trace $trace, console\interfaces\Output $output)
		{
			// Some variables which will come in handy to make the trace more legible.
			$count     = count($trace);
			$verbosity = $output->getVerbosity();
			$i         = 0;

			/* @var debug\Frame $frame */
			foreach($trace as $frame)
			{
				switch($verbosity)
				{
					// @todo Implement different views/templates for the trace depending on the Verbosity.
					case console\interfaces\Output::VERBOSE:
					case console\interfaces\Output::LOUD:
					case console\interfaces\Output::DEBUG:

						// The first frame is the one directly responsible for the exception. Treat it accordingly.
						if($i === 0)
						{
							$output->write('<comment>Thrown by:</comment>', 2);
							$output->write(sprintf(' <info>%s</info> on line <info>%s</info>', $frame->getPrettyPath(), $frame->getLine()), 2);
						}
						// Every other frame is part of the actual backtrace.
						else
						{
							// Print a little header before we start outputting the backtrace.
							if($i === 1) $output->write('<comment>Exception trace:</comment>', 2);

							$output->writeln(sprintf(' [%d] %s%s%s() at <info>%s:%s</info>',
								$count - $i, $frame->getClass(),
								$frame->getType(), $frame->getFunction(),
								$frame->getPrettyPath() ?: 'unknown',
								$frame->getLine() ?: 'unknown')
							);
						}

					break;
				}

				$i++;
			}

			$output->ln(1);
		}
	}