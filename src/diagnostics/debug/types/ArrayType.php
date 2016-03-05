<?php namespace nyx\diagnostics\debug\types;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * Array Type
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	class ArrayType extends Structure
	{
		/**
		 * The actual name of the type.
		 */

		const NAME = 'array';

		/**
		 * {@inheritDoc}
		 */

		public function __construct(array $value, $level = 0, $nestingLimit = 10)
		{
			$this->length = count($value);

			// We are going to populate the underlying value ourselves since we need the items of the array to be
			// instances of the base Type as well and thus we're passing an empty array.
			parent::__construct([], $level);

			// Populate the array if we're not above our nesting limit.
			if(!$this->isTerminal())
			{
				foreach($value as $key => $val)
				{
					$this->value[] = new arrays\Item($key, debug\Type::create($val, $this->level + 1));
				}
			}
		}

		/**
		 * {@inheritDoc}
		 */

		public function setLevel($level)
		{
			parent::setLevel($level);

			/* @var arrays\Item $value */
			foreach($this->value as $value)
			{
				$value->getValue()->setLevel($level + 1);
			}
		}

		/**
		 * {@inheritDoc}
		 */

		public function toString()
		{
			$values = [];

			/* @var arrays\Item $value */
			foreach($this->value as $value)
			{
				$values[] = $value->getValue()->toString();
			}

			return sprintf('array(%s)', implode(', ', $values));
		}
	}