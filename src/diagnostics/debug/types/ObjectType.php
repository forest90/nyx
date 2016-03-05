<?php namespace nyx\diagnostics\debug\types;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * Object Type
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 * @todo        Methods, method parameters (including default values).
	 */

	class ObjectType extends Structure
	{
		/**
		 * The actual name of the type.
		 */

		const NAME = 'object';

		/**
		 * Constant to use instead of the file path if the underlying object is of an internal PHP class.
		 */

		const INTERNAL_CLASS = '** INTERNAL **';

		/**
		 * Visibility constants for properties and methods.
		 */

		const VISIBILITY_PUBLIC    = 0;
		const VISIBILITY_PROTECTED = 1;
		const VISIBILITY_PRIVATE   = 2;

		/**
		 * @var string  The name of the object's class.
		 */

		private $className;

		/**
		 * @var string  The path to the file the object's class is defined in or self::INTERNAL_CLASS if the class
		 *              is an internal PHP class.
		 */

		private $fileName;

		/**
		 * @var objects\Constant[]  The class constants of the underlying object.
		 */

		private $constants;

		/**
		 * @var objects\Property[]  The properties of the underlying object.
		 */

		private $properties;

		/**
		 * @var \ReflectionClass    The reflection of the underlying class.
		 */

		private $reflection;

		/**
		 * {@inheritDoc}
		 */

		public function __construct($value, $level = 0, $nestingLimit = 10)
		{
			if(!is_object($value))
			{
				throw new \InvalidArgumentException('Expected object, '.gettype($value).' given.');
			}

			parent::__construct($value, $level, $nestingLimit);

			$this->className  = get_class($value);
			$this->reflection = new \ReflectionClass($this->className);
		}

		/**
		 * Returns the name of the object's class.
		 *
		 * @return  string
		 */

		public function getName()
		{
			return $this->className;
		}

		/**
		 * Returns the path to the file the object's class is defined in or self::INTERNAL_CLASS if the class
		 * is an internal PHP class.
		 *
		 * @return  string
		 */

		public function getFileName()
		{
			return $this->fileName ?: ($this->fileName = $this->reflection->getFileName() ?: self::INTERNAL_CLASS);
		}

		/**
		 * Returns the class constants of the underlying object.
		 *
		 * @return  objects\Constant[]
		 */

		public function getConstants()
		{
			return $this->loadConstants()->constants;
		}

		/**
		 * Returns the given class constant identified by its name.
		 *
		 * @param   string              $name   The name of the constant.
		 * @return  objects\Constant            The Constant or null if it couldn't be found.
		 */

		public function getConstant($name)
		{
			$constants = $this->getConstants();

			return isset($constants[$name]) ? $constants[$name] : null;
		}

		/**
		 * Returns the properties of the underlying object. The resulting array will include both static and
		 * non-static properties.
		 *
		 * @return  objects\Property[]
		 */

		public function getProperties()
		{
			return $this->loadProperties()->properties;
		}

		/**
		 * Returns the given property identified by its name.
		 *
		 * @param   string              $name   The name of the property.
		 * @return  objects\Property            The Property or null if it couldn't be found.
		 */

		public function getProperty($name)
		{
			$properties = $this->getProperties();

			return isset($properties[$name]) ? $properties[$name] : null;
		}

		/**
		 * Returns the reflection of the underlying class.
		 *
		 * @return  \ReflectionClass
		 */

		public function getReflection()
		{
			return $this->reflection;
		}

		/**
		 * Loads the class constants from the reflection, converts them to instances of objects\Constant to allow
		 * further inspections and stores them in the internal constants array.
		 *
		 * @return  $this
		 */

		protected function loadConstants()
		{
			if(null !== $this->constants) return $this;

			$this->constants = [];

			foreach($this->getReflection()->getConstants() as $name => $value)
			{
				$this->constants[$name] = new objects\Constant($name, debug\Type::create($value, $this->level + 1));
			}

			return $this;
		}

		/**
		 * Loads the object's properties from the reflection, converts them to instances of objects\Property to allow
		 * further inspections, sorts them (static first, then public, protected, private) and stores them in
		 * the internal properties array.
		 *
		 * @return  $this
		 */

		protected function loadProperties()
		{
			if(null !== $this->properties) return $this;

			$this->properties = [];

			foreach($this->getReflection()->getProperties() as $property)
			{
				$this->properties[$property->getName()] = new objects\Property($property, $this);
			}

			// Sort the properties - static first, then public, protected, private.
			uasort($this->properties, function(objects\Property $a, objects\Property $b) {

				$strA = ((int) !$a->isStatic()) . $a->getVisibility() . $a->getName();
				$strB = ((int) !$b->isStatic()) . $b->getVisibility() . $b->getName();

				return strcasecmp($strA, $strB);
			});

			return $this;
		}

		/**
		 * Dynamically delegate all not covered method calls to the underlying reflected class.
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