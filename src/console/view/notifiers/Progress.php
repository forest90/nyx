<?php namespace nyx\console\view\notifiers;

	// Internal dependencies
	use nyx\console\interfaces;
	use nyx\console\view;

	/**
	 * Progress Notifier
	 *
	 * Note: When finish()'ing the Notifier the current value will not be set to the maximum value automatically to
	 * remain flexible and allow for the usage of this method also when aborting a process (ie. when it is desired
	 * that the progress bar remains visible with the value it was aborted at). However, the Notifier will automatically
	 * finish() once it increments to or above the set maximum value.
	 *
	 * A Progress Notifier has a given maximum value of steps it can reach. Reaching that value means the Notifier
	 * is considered finished.
	 *
	 * @package     Nyx\Console\Output\View
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/view/notifiers.html
	 */

	abstract class Progress extends view\Notifier
	{
		/**
		 * @var int     The maximum value this Notifier can reach.
		 */

		private $max;

		/**
		 * {@inheritDoc}
		 *
		 * @param   int     $max            The maximum value this Notifier can reach.
		 * @throws  \OutOfRangeException    When the given maximum value is smaller than 1.
		 */

		public function __construct(interfaces\Output $output, $max, $format, $frequency = 100)
		{
			if(1 > $this->max = (int) $max)
			{
				throw new \OutOfRangeException("Maximum value out of range, must be greater or equal to 1.");
			}

			parent::__construct($output, $format, $frequency);
		}

		/**
		 * {@inheritDoc}
		 */

		public function tick($increment = 1)
		{
			parent::tick($increment);

			// Force a finish() if we've reached our maximum.
			if($this->getCurrent() >= $this->max) $this->finish();

			return $this;
		}

		/**
		 * Calculates the estimated total time for the step count to reach the maximum number of set steps and returns
		 * the rounded value.
		 *
		 * @return  int     The estimated total number of seconds for all steps to be completed. This is not the
		 *                  estimated time left, but total estimated time based on the current speed.
		 */

		public function getEstimated()
		{
			if(!$speed = $this->getSpeed() or !$this->getElapsed()) return 0;

			return round($this->max / $speed);
		}

		/**
		 * Calculates the percentage completed.
		 *
		 * @return  float  The percentage completed.
		 */

		public function getPercentage()
		{
			return $this->getCurrent() / $this->max;
		}

		/**
		 * {@inheritDoc}
		 *
		 * Overridden to ensure the steps don't exceed the maximal value set.
		 */

		protected function increment($steps = 1)
		{
			return $this->setCurrent(min($this->max, $this->getCurrent() + $steps));
		}

		/**
		 * {@inheritDoc}
		 */

		protected function getPresentationValues()
		{
			$return     = parent::getPresentationValues();
			$percentage = round($this->getPercentage(), 2);

			$return['estimated'] = $this->humaneTime($this->getEstimated());
			$return['percent']   = str_pad($percentage * 100, 3, ' ', STR_PAD_LEFT);

			return $return;
		}
	}
