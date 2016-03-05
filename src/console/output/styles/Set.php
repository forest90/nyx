<?php namespace nyx\console\output\styles;

	// Internal dependencies
	use nyx\console\interfaces;

	// External dependencies
	use nyx\core;

	/**
	 * Styłe Set
	 *
	 * Styles within the set are identified by names, although those names are not set explicitly within the Styles
	 * themselves. So, while those names are used internally by an output Formatter to determine how to style text
	 * within the respective tags, neither a style Set nor a Style itself run under the assumption of being used only
	 * for formatting aforementioned tags. As such, naming them is merely a helpful way to access them.
	 *
	 * @package     Nyx\Console\Output
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/output.html
	 */

	class Set extends core\collections\Map
	{
		/**
		 * Prepares a new style Set.
		 *
		 * @param   array   $styles     An array of Style instances to be added to the Collection.
		 */

		public function __construct(array $styles = [])
		{
			if(!empty($styles)) $this->replace($styles);
		}

		/**
		 * {@inheritDoc}
		 *
		 * @param   string  $name               The name of the Style to look for.
		 * @throws  \InvalidArgumentException   When no Style with the given name is available within the collection.
		 */

		public function get($name, $default = null)
		{
			if(!$this->has($name)) throw new \InvalidArgumentException("The style [$name] is not defined.");

			return $this->items[strtolower($name)];
		}

		/**
		 * {@inheritDoc}
		 *
		 * @throws  \InvalidArgumentException   When the given $style is not a valid instance of the Style interface.
		 */

		public function set($name, $style)
		{
			if(!$style instanceof interfaces\output\Style)
			{
				throw new \InvalidArgumentException("Expected an instance of nyx\\console\\interfaces\\output\\Style, got [".gettype($style)."] instead.");
			}

			$this->items[strtolower($name)] = $style;

			return $this;
		}

		/**
		 * {@inheritDoc}
		 *
		 * Overriding the trait to ensure keys are lowercased.
		 */

		public function has($name)
		{
			return array_key_exists(strtolower($name), $this->items);
		}
	}