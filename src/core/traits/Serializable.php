<?php namespace nyx\core\traits;

	/**
	 * Serializable
	 *
	 * A Serializable object within Nyx is one that can be cast to an array, string, JSON string and serialized.
	 *
	 * This trait allows for the implementation of the core\interfaces\Serializable interface *if* both a toArray() and
	 * a unserialize() method get implemented as well.
	 *
	 * @package     Nyx\Core\Traits
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/traits.html
	 */

	trait Serializable
	{
		/**
		 * @see core\interfaces\Arrayble::toArray()
		 */

		abstract public function toArray();

		/**
		 * @see \Serializable::unserialize()
		 */

		abstract public function unserialize($data);

		/**
		 * @see \Serializable::serialize()
		 */

		public function serialize()
		{
			return serialize($this->toArray());
		}

		/**
		 * @see \JsonSerializable::jsonSerialize()
		 */

		public function jsonSerialize()
		{
			return $this->toArray();
		}

		/**
		 * @see core\interfaces\Jsonable::toJson()
		 */

		public function toJson($options = 0)
		{
			return json_encode($this->jsonSerialize(), $options);
		}

		/**
		 * @see core\interfaces\Stringable::toString()
		 */

		public function toString()
		{
			return $this->toJson();
		}

		/**
		 * Magic alias for {@see self::toString()}.
		 */

		public function __toString()
		{
			return $this->toString();
		}
	}