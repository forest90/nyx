<?php namespace nyx\console\input;

	// Internal dependencies
	use nyx\console;

	/**
	 * Array Input
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	class Arr extends console\Input
	{
		/**
		 * Constructs a new Array Input instance.
		 *
		 * @param   array   $parameters     An array of input parameters.
		 */

		public function __construct(array $parameters)
		{
			// Store the raw arguments.
			$this->raw = new tokens\Arr($parameters);
		}

		/**
		 * {@inheritDoc}
		 */

		protected function parse()
		{
			foreach($this->raw as $name => $value)
			{
				if(0 === strpos($name, '--'))
				{
					$this->options()->set(substr($name, 2), $value);
				}
				elseif('-' === $name[0])
				{
					$this->options()->set(substr($name, 1), $value);
				}
				else
				{
					$this->arguments()->set($name, $value);
				}
			}
		}
	}