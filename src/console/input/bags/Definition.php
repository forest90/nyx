<?php namespace nyx\console\input\bags;

	// External dependencies
	use nyx\core\collections;

	/**
	 * Input Definition Bag
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	abstract class Definition implements \IteratorAggregate, collections\interfaces\Set
	{
		/**
		 * The traits of a Definition Bag instance.
		 */

		use collections\traits\Set;

		/**
		 * Returns the default values of all items in the Bag.
		 *
		 * @return  array
		 */

		public function getDefaults()
		{
			$values = [];

			/* @var \nyx\console\input\Parameter $item */
			foreach($this->items as $item)
			{
				$values[$item->getName()] = $item->getValue()->getDefault();
			}

			return $values;
		}
	}