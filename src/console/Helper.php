<?php namespace nyx\console;

	/**
	 * Helper
	 *
	 * Base class for concrete Helpers.
	 *
	 * @package     Nyx\Console\Helpers
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/helpers.html
	 */

	abstract class Helper implements interfaces\Helper
	{
		/**
		 * The traits of a Helper instance.
		 */

		use traits\Named;
		use traits\OutputAware;

		/**
		 * Constructor.
		 *
		 * @param   string              $name       The name of this Helper.
		 * @param   interfaces\Output   $output     A default Output instance to use.
		 */

		public function __construct($name, interfaces\Output $output = null)
		{
			$this->setName($name);

			if(null !== $output) $this->setOutput($output);
		}
	}
