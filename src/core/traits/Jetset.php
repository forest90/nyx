<?php namespace nyx\core\traits;

	/**
	 * Jetset
	 *
	 * @package     Nyx\Core\Traits
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/traits.html
	 */

	trait Jetset
	{
		/**
		 * Magic setter. This will take care of setting all properties in the private scope from the outside
		 * by either calling a specific getter if it exists (for the property "name", the getter would be called
		 * "getName" and so on) or directly getting the value.
		 *
		 * @param   string  $name
		 * @param   mixed   $value
		 */

		public function __set($name, $value)
		{
			// Do not permit setting of arbitrary values.
			if(property_exists($this, $name))
			{
				// First let's see if we've got a specific method for this.
				if(method_exists($this, $method = 'set'.ucfirst($name))) return $this->$method($value);

				// Otherwise let's just run some generic automagic.
				$this->$name = $value;
			}
		}
	}