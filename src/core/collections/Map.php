<?php namespace nyx\core\collections;

	// External dependencies
	use nyx\utils;

	/**
	 * Map
	 *
	 * @package     Nyx\Core\Collections
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/collections.html
	 */

	class Map extends Collection implements \IteratorAggregate, interfaces\Map
	{
		use traits\Map;
	}