<?php namespace nyx\diagnostics\debug;

	// External dependencies
	use nyx\core\collections;

	/**
	 * Trace
	 *
	 * Collection for \Exception::getTrace() frames which get converted into diagnostic\Frame instances when passed
	 * to the constructor. The collection *is* mutable - the frames are numerically indexed (per assumption, in the
	 * order returned by getTrace(), but other uses are obviously possible).
	 *
	 * Please refer to {@see core\Collection} and the corresponding trait for details on which methods are available
	 * for Collections. This class only overrides those which might directly inject elements into the Collection, in
	 * order to ensure proper types. Some methods (eg. map, filter) will return new Trace instances with the results
	 * of their calls, and ultimately the constructor of a Frame will take care of type checks and only use the data
	 * it knows about.
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.0.2
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	class Trace extends collections\Map
	{
		/**
		 * {@inheritDoc}
		 */

		public function set($key, $frame)
		{
			parent::set($key, $this->assertValueIsFrame($frame));
		}

		/**
		 * {@inheritDoc}
		 */

		public function push($frame)
		{
			parent::push($this->assertValueIsFrame($frame));
		}

		/**
		 * {@inheritDoc}
		 *
		 * Overridden to ensure all values are Frames. If they are not such already, they will be converted to Frame
		 * instances. Internally called by collections\Map 's constructor, ie. this will also cover cases of map(),
		 * reduce() etc.
		 */

		public function replace($items)
		{
			array_map([$this, 'push'], $this->extractItems($items));
		}

		/**
		 * Attempts to convert the given value to a Frame instance (if it isn't one already) and returns it if possible.
		 *
		 * @param   mixed   $value              The value to check.
		 * @return  Frame
		 * @throws  \InvalidArgumentException   When the given value is not a frame and could not be converted to one.
		 */

		protected function assertValueIsFrame($value)
		{
			if($value instanceof Frame) return $value;

			if(is_array($value)) return new Frame($value);

			throw new \InvalidArgumentException('The given value is not a Frame instance and could not be converted to one.');
		}
	}