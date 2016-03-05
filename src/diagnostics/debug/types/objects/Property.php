<?php namespace nyx\diagnostics\debug\types\objects;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * Class Property
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	class Property
	{
		/**
		 * @var debug\types\ObjectType  The parent Object the property belongs to.
		 */

		private $parent;

		/**
		 * @var debug\interfaces\Type   The value of the underlying class property.
		 */

		private $value;

		/**
		 * @var int     The visibility of the underlying class property.
		 */

		private $visibility;

		/**
		 * @var \ReflectionProperty     The reflection of the underlying class property.
		 */

		private $reflection;

		/**
		 * Constructs a new Class Property.
		 *
		 * @param   debug\types\ObjectType  $parent         The parent Object the property belongs to.
		 * @param   \ReflectionProperty     $reflection     The reflection of the underlying class property.
		 */

		public function __construct(\ReflectionProperty $reflection, debug\types\ObjectType $parent)
		{
			$reflection->setAccessible(true);

			$this->reflection = $reflection;
			$this->parent     = $parent;

			// Create a Type for the value of the property set in the parent object, if the property is non-static.
			$this->value = debug\Type::create(!$reflection->isStatic()
				? $reflection->getValue($parent->getValue())
				: $reflection->getValue()
			);

			// Make the visibility available for the getVisibility() method.
			if($reflection->isPrivate())
			{
				$this->visibility = debug\types\ObjectType::VISIBILITY_PRIVATE;
			}
			elseif($reflection->isProtected())
			{
				$this->visibility = debug\types\ObjectType::VISIBILITY_PROTECTED;
			}
			else
			{
				$this->visibility = debug\types\ObjectType::VISIBILITY_PUBLIC;
			}
		}

		/**
		 * Returns the name of the underlying class property.
		 *
		 * @return  string
		 */

		public function getName()
		{
			return $this->reflection->getName();
		}

		/**
		 * Returns the value of the underlying class property.
		 *
		 * @return  debug\interfaces\Type
		 */

		public function getValue()
		{
			return $this->value;
		}

		/**
		 * Returns the nesting level of the underlying class property.
		 *
		 * @return  int
		 */

		public function getLevel()
		{
			return $this->value->getLevel();
		}

		/**
		 * Returns the visibility level of the underlying class property.
		 *
		 * @return  int     One of the visibility constants defined in debug\types\ObjectType.
		 */

		public function getVisibility()
		{
			return $this->visibility;
		}

		/**
		 * Checks whether the underlying class property is static.
		 *
		 * @return  bool
		 */

		public function isStatic()
		{
			return $this->reflection->isStatic();
		}

		/**
		 * Returns the reflection of the underlying property.
		 *
		 * @return  \ReflectionProperty
		 */

		public function getReflection()
		{
			return $this->reflection;
		}

		/**
		 * Dynamically delegate all not covered method calls to the underlying reflected property.
		 *
		 * @param   string  $method         The name of the method to call.
		 * @param   array   $parameters     The parameters to pass to the method.
		 * @return  mixed
		 */

		public function __call($method, $parameters)
		{
			return call_user_func_array($this->reflection->$method, $parameters);
		}
	}