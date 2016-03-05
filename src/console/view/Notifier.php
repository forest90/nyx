<?php namespace nyx\console\view;

	// Internal dependencies
	use nyx\console\interfaces;
	use nyx\console\traits;

	/**
	 * Notifier
	 *
	 * Based on {@link https://github.com/jlogsdon/php-cli-tools}. See the LICENSE file distributed with this package
	 * for detailed copyright and licensing information.
	 *
	 * @package     Nyx\Console\Output\View
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/view/notifiers.html
	 * @todo        Refactor the Presented trait in favour of closures/callables handling the placeholder substitution?
	 */

	abstract class Notifier
	{
		/**
		 * The traits of a Notifier instance.
		 */

		use traits\Presented;

		/**
		 * @var interfaces\Output   The Output the Notifier will be displayed in.
		 */

		private $output;

		/**
		 * @var int     The start time of the Notifier.
		 */

		private $start;

		/**
		 * @var int     The current step of the Notifier.
		 */

		private $current;

		/**
		 * @var int     The frequency at which the output should be updated.
		 */

		private $frequency;

		/**
		 * @var int     The internal timer used to calculate time intervals between ticks.
		 */

		private $timer;

		/**
		 * @var int     Runtime values used for calculating the update speed.
		 */

		private $speed = ['tick' => null, 'iteration' => 0, 'current' => 0];

		/**
		 * Constructs a new Notifier instance.
		 *
		 * @param   interfaces\Output   $output     The Output the Notifier will be displayed in.
		 * @param   string              $format     The presentation format of the Notifier.
		 * @param   int                 $frequency  The update frequency in milliseconds.
		 */

		public function __construct(interfaces\Output $output, $format, $frequency = 100)
		{
			$this->output    = $output;
			$this->current   = 0;
			$this->frequency = (int) $frequency;

			$this->setPresentationFormat($format);
		}

		/**
		 * Starts the timer.
		 *
		 * Does not actually output anything. Is run automatically by self::tick() if the Notifier has not been started
		 * manually beforehand.
		 *
		 * @return  int     The timer start time in ms.
		 */

		public function start()
		{
			return $this->start = microtime(true);
		}

		/**
		 * Performs a tick.
		 *
		 * Automatically starts the timer if it's not yet running and refreshes the output if the frequency setting
		 * is met appropriately.
		 *
		 * @param   int $increment      By how much the current value should be incremented.
		 * @return  $this
		 */

		public function tick($increment = 1)
		{
			// No point in continuing if we didn't actually start to count, so let's just do it at this point.
			if(!$this->start) $this->start();

			$this->increment($increment);

			$this->shouldUpdate() and $this->render();

			return $this;
		}

		/**
		 * Stops the timer and displays the Notifier one last time.
		 *
		 * @return  $this
		 */

		public function finish()
		{
			$this->render(true);
			$this->start = null;
			$this->timer = null;

			return $this;
		}

		/**
		 * Returns the current step of the Notifier.
		 *
		 * @return  int
		 */

		public function getCurrent()
		{
			return $this->current;
		}

		/**
		 * Returns the number of seconds that have passed since the Notifier was started.
		 *
		 * @return  int
		 */

		public function getElapsed()
		{
			return $this->start ? round(time() - $this->start) : 0;
		}

		/**
		 * Calculates the speed (number of ticks per second) at which the Notifier is being updated and returns
		 * the current value thereof.
		 *
		 * @return  int
		 */

		public function getSpeed()
		{
			// No point in continuing if we didn't actually start to count.
			if(!$this->start) return 0;

			// For the first measurement, we'll have to use the start time as a tick time.
			if(!$this->speed['tick']) $this->speed['tick'] = $this->start;

			$now  = microtime(true);
			$span = $now - $this->speed['tick'];

			if($span > 1)
			{
				$this->speed['iteration']++;
				$this->speed['tick'] = $now;
				$this->speed['current'] = ($this->current / $this->speed['iteration']) / $span;
			}

			return $this->speed['current'];
		}

		/**
		 * Displays the Notifier in the given Output by overwriting the last line with its contents. It also updates
		 * the internal refresh timer with the current microtime to provide a semi-consistent update frequency even if
		 * you manually display the status at some point.
		 *
		 * @param   bool    $finish     Whether a finishing cleanup should be performed (additional newline etc.).
		 * @return  $this
		 */

		public function render($finish = false)
		{
			$messages = $this->asText();

			$size = $this->output->getWidth();

			$this->output->write(str_repeat("\x08", $size));
			$this->output->write($messages);
			$this->output->write(str_repeat(' ', $size - strlen($messages)));

			// Clean up the finishing line.
			$finish and $this->output->writeln(str_repeat("\x08", $size - strlen($messages)));

			// Update the refresh timer.
			$this->timer = microtime(true) * 1000;

			return $this;
		}

		/**
		 * Sets the current step of the Notifier. Protected access since this method may only be used by Notifiers as
		 * concrete implementation may require setting this private property.
		 *
		 * @param   int $step   The step to set.
		 * @return  $this
		 */

		protected function setCurrent($step)
		{
			$this->current = (int) $step;

			return $this;
		}

		/**
		 * Increments the current step by the given number.
		 *
		 * @param   int $steps
		 * @return  $this
		 */

		protected function increment($steps = 1)
		{
			$this->current += (int) $steps;

			return $this;
		}

		/**
		 * Determines whether the Output should be updated by comparing the set update frequency to the last time
		 * the Output was updated (ie. the display() method got called).
		 *
		 * @param   int $ms     The time in milliseconds that should be compared with the timer. Leave at null to use
		 *                      the current time.
		 * @return  bool        True when the timer exceeded the set frequency, false when not enough time has passed yet.
		 */

		protected function shouldUpdate($ms = null)
		{
			// Which time should we use?
			$ms = (int) $ms ?: microtime(true) * 1000;

			// Did the time required by the frequency pass?
			return ($ms - $this->timer) > $this->frequency;
		}

		/**
		 * {@inheritDoc}
		 */

		protected function getPresentationValues()
		{
			return [
				'speed'   => number_format(round($this->getSpeed())),
				'elapsed' => $this->humaneTime($this->getElapsed())
			];
		}

		/**
		 * Returns a human-readable string based on the time given in seconds.
		 *
		 * If the number of seconds exceeds a day, the string will be prepended by the number of days and the 'd' letter.
		 * If less than 1 second is given, three question marks will be returned. This simplifies presenting time
		 * estimates in the concrete Notifiers.
		 *
		 * @param   int     $secs       The number of seconds to base on.
		 * @param   string  $format     The format to be used for the output. Standard PHP date() formats are supported.
		 * @return  string
		 */

		protected function humaneTime($secs, $format = 'H:i:s')
		{
			if($secs < 1) return '???';

			return $secs < 86400 ? gmdate($format, $secs) : floor($secs / 86400).'d '.gmdate($format, $secs % 86400);
		}
	}
