<?php namespace nyx\core\collections\traits;

	// External dependencies
	use nyx\utils;

	// Internal dependencies
	use nyx\core\collections\interfaces;
	use nyx\core;

	/**
	 * Collection
	 *
	 * A Collection is an object that contains other items which can be set, get and removed from the Collection.
	 *
	 * Usage of this trait allows you to implement the interfaces\Collection interface and \IteratorAggregate.
	 *
	 * Important notes:
	 * 1) Some of the methods, like self::map() or self::filter() for instance, make assumptions as to the constructor
	 *    of the exhibitor of this trait, assuming that it accepts a Collection, Arrayable object or array as
	 *    its first argument.
	 * 2) For simplicity and performance reasons, some of the methods do not rely on each other to reduce some
	 *    overhead of additional function calls. This is the case, for instance, for self::get(), which does not make
	 *    use of self::has() to check for the existence of an item or self::replace() which will not call self::set()
	 *    for each item passed to it. Keep this in mind when overriding them.
	 * 3) Collection::set() does *not* check whether the value is not null in order not to introduce
	 *    additional overhead on an operation that is very common. However, several methods *do rely* on the fact that
	 *    values are not null (first() and last() for instance will return "null" if the Collection is empty - they can
	 *    not return false since false is a valid value for an item in the Collection. Therefore, passing null values
	 *    to the Collection may yield unexpected results.
	 *
	 * @package     Nyx\Core\Collections
	 * @version     0.0.8
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/collections.html
	 * @todo        Lo-dash-style find() / findWhere() (pluck and where).
	 * @todo        Decide: Regarding important note #2 - make use of the respective methods internally?
	 */

	trait Collection
	{
		/**
		 * The traits of a Collection trait.
		 */

		use core\traits\Serializable;

		/**
		 * @var array   An array of the items contained within the object exhibiting this trait, ie. the concrete
		 *              Collection.
		 */

		protected $items = [];

		/**
		 * @see interfaces\Collection::get()
		 */

		public function get($key, $default = null)
		{
			if(array_key_exists($key, $this->items)) return $this->items[$key];

			return $default;
		}

		/**
		 * @see interfaces\Collection::has()
		 */

		public function has($key)
		{
			return array_key_exists($key, $this->items);
		}

		/**
		 * @see interfaces\Collection::contains()
		 */

		public function contains($item)
		{
			return null !== $this->key($item);
		}

		/**
		 * @see interfaces\Collection::key()
		 */

		public function key($item)
		{
			foreach($this->items as $key => $value)
			{
				if($value === $item) return $key;
			}

			return null;
		}

		/**
		 * @see interfaces\Collection::remove()
		 */

		public function remove($key)
		{
			unset($this->items[$key]);

			return $this;
		}

		/**
		 * @see interfaces\Collection::all()
		 */

		public function all()
		{
			return $this->items;
		}

		/**
		 * @see interfaces\Collection::values()
		 */

		public function values()
		{
			return array_values($this->items);
		}

		/**
		 * @see interfaces\Collection::keys()
		 */

		public function keys($of = null)
		{
			return array_keys($this->items, $of, true);
		}

		/**
		 * Pushes an item onto the beginning of the Collection.
		 *
		 * @param   mixed  $value   The item to push into the Collection.
		 */

		public function prepend($value)
		{
			array_unshift($this->items, $value);
		}

		/**
		 * Returns and then removes the first item from the Collection.
		 *
		 * @return  mixed|null
		 */

		public function shift()
		{
			return array_shift($this->items);
		}

		/**
		 * Pushes an item onto the the end of the Collection.
		 *
		 * @param   mixed  $value   The item to push into the Collection.
		 */

		public function push($value)
		{
			$this->items[] = $value;
		}

		/**
		 * Returns and then removes the last item from the Collection.
		 *
		 * @return  mixed|null
		 */

		public function pop()
		{
			return array_pop($this->items);
		}

		/**
		 * @see interfaces\Collection::find()
		 */

		public function find(callable $callback, $default = null)
		{
			return utils\Arr::find($this->items, $callback, $default);
		}

		/**
		 * @see interfaces\Collection::first()
		 */

		public function first($callback = false, $default = null)
		{
			return utils\Arr::first($this->items, $callback, $default);
		}

		/**
		 * @see interfaces\Collection::last()
		 */

		public function last($callback = false, $default = null)
		{
			return utils\Arr::last($this->items, $callback, $default);
		}

		/**
		 * @see interfaces\Collection::initial()
		 */

		public function initial($callback = false, $default = null)
		{
			return utils\Arr::initial($this->items, $callback, $default);
		}

		/**
		 * @see interfaces\Collection::rest()
		 */

		public function rest($callback = false, $default = null)
		{
			return utils\Arr::rest($this->items, $callback, $default);
		}

		/**
		 * @see interfaces\Collection::slice()
		 */

		public function slice($offset, $length = null, $preserveKeys = false)
		{
			return new static(array_slice($this->items, $offset, $length, $preserveKeys));
		}

		/**
		 * @see interfaces\Collection::pluck()
		 */

		public function pluck($value, $key = null)
		{
			return utils\Arr::pluck($this->items, $value, $key);
		}

		/**
		 * @see interfaces\Collection::select()
		 */

		public function select(callable $callback)
		{
			return new static(array_filter($this->items, $callback));
		}

		/**
		 * @see interfaces\Collection::reject()
		 *
		 * Usage of self::select() with the comparison in your callback inversed is preferred as this method is
		 * somewhat slower than simply running array_filter.
		 */

		public function reject(callable $callback)
		{
			$result = [];

			foreach($this->items as $key => $item)
			{
				if(!call_user_func($callback, $item)) $result[$key] = $item;
			}

			return $result;
		}

		/**
		 * @see interfaces\Collection::map()
		 */

		public function map(callable $callback)
		{
			return new static(array_map($callback, $this->items, array_keys($this->items)));
		}

		/**
		 * @see interfaces\Collection::each()
		 */

		public function each(callable $callback)
		{
			array_map($callback, $this->items);

			return $this;
		}

		/**
		 * @see interfaces\Collection::reduce()
		 */

		public function reduce(callable $callback, $initial = null)
		{
			return array_reduce($this->items, $callback, $initial);
		}

		/**
		 * @see interfaces\Collection::implode()
		 */

		public function implode($value, $glue = '')
		{
			return implode($glue, $this->pluck($value));
		}

		/**
		 * @see interfaces\Collection::reverse()
		 */

		public function reverse()
		{
			return new static(array_reverse($this->items));
		}

		/**
		 * @see interfaces\Collection::collapse()
		 */

		public function collapse()
		{
			$results = [];

			// Merge all values down to a single array.
			foreach($this->items as $values) $results = array_merge($results, $values);

			return new static($results);
		}

		/**
		 * @see interfaces\Collection::flatten()
		 */

		public function flatten()
		{
			return new static(utils\Arr::flatten($this->items));
		}

		/**
		 * @see interfaces\Collection::fetch()
		 */

		public function fetch($name)
		{
			return new static(utils\Arr::fetch($this->items, $name));
		}

		/**
		 * @see interfaces\Collection::merge()
		 */

		public function merge()
		{
			$result = $this->items;

			// Since we're variadic, let's loop through all the values given. Collections and Arrayable values
			// will be handled appropriately while all other values will be cast to arrays by self::extractItems().
			foreach(func_get_args() as $items) $result = array_merge($result, $this->extractItems($items));

			return $result;
		}

		/**
		 * Sorts this Collection using the given callable.
		 *
		 * @param   callable    $callback
		 * @return  $this
		 */

		public function sort(callable $callback)
		{
			uasort($this->items, $callback);

			return $this;
		}

		/**
		 * @see interfaces\Collection::isEmpty()
		 */

		public function isEmpty()
		{
			return empty($this->items);
		}

		/**
		 * @see \Countable::count()
		 */

		public function count()
		{
			return count($this->items);
		}

		/**
		 * Returns an Iterator for the items in this Collection. Allows for the implementation of \IteratorAggregate.
		 *
		 * @return  \ArrayIterator
		 */

		public function getIterator()
		{
			return new \ArrayIterator($this->items);
		}

		/**
		 * @see \Serializable::unserialize()
		 */

		public function unserialize($data)
		{
			$this->items = unserialize($data);
		}

		/**
		 * @see core\interfaces\Arrayble::toArray()
		 */

		public function toArray()
		{
			return array_map(function($value)
			{
				return $value instanceof core\interfaces\Arrayable ? $value->toArray() : $value;

			}, $this->items);
		}

		/**
		 * Magic alias for {@see self::get()}.
		 */

		public function __get($key)
		{
			return $this->get($key);
		}

		/**
		 * Magic alias for {@see self::has()}.
		 */

		public function __isset($key)
		{
			return $this->has($key);
		}

		/**
		 * Make sure we're able to handle deep copies properly. This will work for instances of the exhibitor of this
		 * trait contained within the exhibitor's Collection itself, but may require overrides for customized
		 * Collections.
		 */

		public function __clone()
		{
			foreach($this->items as $key => $value)
			{
				if($value instanceof interfaces\Collection) $this->items[$key] = clone $value;
			}
		}

		/**
		 * Inspects the given $items and attempts to figure out whether and how to extract its elements or whether
		 * to simply cast the variable to an array to make use of it.
		 */

		protected function extractItems($items)
		{
			// If we were given an object implementing the Collection interface, grab all its items, preserving
			// the keys.
			if($items instanceof interfaces\Collection) return $items->all();

			// If we were given an object that is Arrayable, convert it to an array using the exposed method.
			if($items instanceof core\interfaces\Arrayable) return $items->toArray();

			// Worst case scenario - use PHP's internals to cast it to an array.
			return (array) $items;
		}
	}