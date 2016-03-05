<?php namespace nyx\console\input;

	// External dependencies
	use nyx\connect;

	/**
	 * Stream Input
	 *
	 * Note: Stream input is completely raw and unparsed by default. Since streams allow for a lot of flexibility, no
	 * base assumption can be made as to what will be contained in the stream.
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	class Stream
	{
		/**
		 * @var connect\streams\interfaces\Stream   The Stream instance in use.
		 */

		private $stream;

		/**
		 * {@inheritDoc}
		 *
		 * @param   connect\streams\interfaces\Stream   $stream     A stream instance.
		 * @throws  \InvalidArgumentException                       When an invalid resource was given.
		 */

		public function __construct(connect\streams\interfaces\Stream $stream)
		{
			$this->stream = $stream;
		}

		/**
		 * Gets the Stream instance attached to this Stream Input instance.
		 *
		 * @return  connect\streams\interfaces\Stream
		 */

		public function getStream()
		{
			return $this->stream;
		}
	}