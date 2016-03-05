<?php namespace nyx\diagnostics\debug\types;

	// Internal dependencies
	use nyx\diagnostics\debug;

	/**
	 * Resource Type
	 *
	 * @package     Nyx\Diagnostics\Debug
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/debug.html
	 */

	class ResourceType extends debug\Type
	{
		/**
		 * The actual name of the type.
		 */

		const NAME = 'resource';

		/**
		 * @var int     The id of the underlying resource.
		 */

		private $id;

		/**
		 * @var string  The type of underlying resource.
		 */

		private $type;

		/**
		 * {@inheritDoc}
		 */

		public function __construct($value)
		{
			if(!is_resource($value))
			{
				throw new \InvalidArgumentException('Expected resource, '.gettype($value).' given.');
			}

			parent::__construct($value);

			$this->type = get_resource_type($value);
			$this->id   = (int) $value;

			// Properly treat file streams as files on UNIX systems.
			if($this->type === 'stream')
			{
				$vars = stream_get_meta_data($value);

				if(isset($vars['stream_type']) and $vars['stream_type'] === 'STDIO') $this->type = 'file';
			}
		}

		/**
		 * Returns the id of the underlying resource.
		 *
		 * @return  int
		 */

		public function getId()
		{
			return $this->id;
		}

		/**
		 * Returns the type of underlying resource.
		 *
		 * @return  string
		 */

		public function getResourceType()
		{
			return $this->type;
		}
	}