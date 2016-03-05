<?php namespace nyx\console\input;

	// External dependencies
	use nyx\connect;

	/**
	 * Standard Input
	 *
	 * Merely a convenient wrapper. By default one could use this, for instance, to pipe in data from system software
	 * to a Command within the Application (or between different commands, for that matter). {@see input\Stream} for
	 * more information.
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	class Stdin extends Stream
	{
		/**
		 * {@inheritDoc}
		 */

		public function __construct()
		{
			// Set up the stream resource for the output.
			parent::__construct(new connect\streams\Stream("php://stdin", 'r'));
		}
	}