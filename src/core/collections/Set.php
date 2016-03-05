<?php namespace nyx\core\collections;

	// External dependencies
	use nyx\utils;

	/**
	 * Set
	 *
	 * @package     Nyx\Core\Collections
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/collections.html
	 */

	class Set extends Collection implements \IteratorAggregate, interfaces\Set
	{
		use traits\Set;
	}