<?php namespace nyx\core\traits;

	/**
	 * Named
	 *
	 * A Named object is one that has a name which needs to conform to certain rules defined in the validateName()
	 * method.
	 *
	 * This trait allows for the implementation of the core\interfaces\Named interface.
	 *
	 * The default assumption is that a Named object is one whose name is required to be not empty. If that is not the
	 * case and the name does not need to meet any rules that could be defined in an overridden validateName() method,
	 * the usage worth of this trait becomes highly questionable.
	 *
	 * @package     Nyx\Core\Traits
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/traits.html
	 */

	trait Named
	{
		/**
		 * @var string  The name of this object.
		 */

		private $name;

		/**
		 * @see core\interfaces\Named::getName()
		 */

		public function getName()
		{
			return $this->name;
		}

		/**
		 * @see core\interfaces\Named::setName()
		 */

		public function setName($name)
		{
			$this->validateName($name);
			$this->name = $name;

			return $this;
		}

		/**
		 * Validates the given name to ensure that it is a non-empty string.
		 *
		 * @param   string                      $name   The name to be validated.
		 * @throws  \InvalidArgumentException           When the name does not conform to the validation rules.
		 */

		protected function validateName($name)
		{
			if(empty($name) or !is_string($name)) throw new \InvalidArgumentException("A name must be a non-empty string.");
		}
	}