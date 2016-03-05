<?php namespace nyx\console\helpers;

	// Internal dependencies
	use nyx\console\view\notifiers;
	use nyx\console\interfaces;
	use nyx\console;

	/**
	 * Progress Helper
	 *
	 * Essentially merely a factory class to provide an easy to use access point to all the available progress
	 * notifiers. All methods herein instantiate the respective notifiers but do not perform any further actions with
	 * them, ie. you must perform even the starting tick on your own. For more information on how to use the notifiers
	 * in general, please refer directly to the class of interest.
	 *
	 * An Output instance may be set within the helper. If it's not present and not passed to any of the factory methods
	 * directly, the Helper will instantiate a output\Stdout instance to use as default fallback.
	 *
	 * @package     Nyx\Console\Helpers
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/helpers.html
	 * @todo        Provide some more, easily accessible customization options for the notifiers.
	 */

	class Progress extends console\Helper
	{
		/**
		 * {@inheritDoc}
		 */

		public function __construct($name = null, interfaces\Output $output = null)
		{
			parent::__construct($name ?: 'progress', $output);
		}

		/**
		 * Instantiates a progress bar and returns it, without performing any ticks nor displaying anything yet.
		 *
		 * @see     notifiers\progress\Bar
		 * @param   int                     $max        The maximal number of ticks until progress is to be considered
		 *                                              finished.
		 * @param   interfaces\Output       $output     The Output the notifier is to be displayed in. Leave as null
		 *                                              to use the default.
		 * @return  notifiers\progress\Bar              The Progress Bar Notifier.
		 */

		public function bar($max, interfaces\Output $output = null)
		{
			return new notifiers\progress\Bar($output ?: $this->getOutput(), $max);
		}

		/**
		 * Instantiates a Dots notifier and returns it, without performing any ticks nor displaying anything yet.
		 *
		 * @see     notifiers\Dots
		 * @param   int                     $dots       How many dots should be iterated over.
		 * @param   interfaces\Output       $output     The Output the notifier is to be displayed in. Leave as null
		 *                                              to use the default.
		 * @return  notifiers\Dots                      The Dots Notifier.
		 */

		public function dots($dots = 3, interfaces\Output $output = null)
		{
			return new notifiers\Dots($output ?: $this->getOutput(), $dots);
		}

		/**
		 * Instantiates a Spinner and returns it, without performing any ticks nor displaying anything yet.
		 *
		 * @see     notifiers\Spinner
		 * @param   interfaces\Output       $output     The Output the notifier is to be displayed in. Leave as null
		 *                                              to use the default.
		 * @return  notifiers\Alternator                The Alternator Notifier.
		 */

		public function spinner(interfaces\Output $output = null)
		{
			return new notifiers\Alternator($output ?: $this->getOutput());
		}
	}