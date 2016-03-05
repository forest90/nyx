<?php namespace nyx\console\interfaces\exceptions;

	// Internal dependencies
	use nyx\console\interfaces;

	/**
	 * Renderable Exception Interface
	 *
	 * A Renderable Exception is one that is able to render itself properly in the given Output and does not rely on
	 * external classes nor information to do so.
	 *
	 * For an example of such an Exception, check {@see \nyx\console\exceptions\CommandNotExists} which implements
	 * this interface to provide suggestions for similarly named Commands in a legible manner instead of showing a
	 * dangerously looking error message and a stack trace.
	 *
	 * The included ConsoleRenderer Delegate {@see \nyx\console\diagnostics\debug\delegates\ConsoleRenderer} respects
	 * this interface and uses the interface's render() method instead of doing the rendering on its own.
	 *
	 * @package     Nyx\Console\Diagnostics
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/interfaces.html
	 */

	interface Renderable
	{
		/**
		 * Renders the exception in the given Output.
		 *
		 * @param   interfaces\Output   $output
		 */

		public function render(interfaces\Output $output);
	}