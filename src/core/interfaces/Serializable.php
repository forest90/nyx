<?php namespace nyx\core\interfaces;

	/**
	 * Serializable Interface
	 *
	 * A Serializable object within Nyx is one that can be cast to an array, string, JSON string and serialized.
	 *
	 * @package     Nyx\Core\Interfaces
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/core/interfaces.html
	 */

	interface Serializable extends \Serializable, \JsonSerializable, Arrayable, Jsonable, Stringable
	{

	}