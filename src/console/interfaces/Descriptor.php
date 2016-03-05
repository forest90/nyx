<?php namespace nyx\console\interfaces;

	/**
	 * Descriptor Interface
	 *
	 * @package     Nyx\Console\Descriptors
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/descriptors.html
	 */

	interface Descriptor
	{
	    /**
	     * Describes the given object.
	     *
	     * @param   object  $object             The object to describe.
	     * @param   array   $options            Additional options to be considered by the Descriptor.
	     * @return  mixed                       The description.
	     * @throws  \InvalidArgumentException   When the given object cannot be described by the Descriptor.
	     */

	    public function describe($object, array $options = []);
	}