<?php namespace nyx\utils\tests;

	// Internal dependencies
	use nyx\utils\Arr;

	/**
	 * Arr Tests
	 *
	 * @package     Nyx\Utils\Tests
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/utils/index.html
	 */

	class ArrTest extends \PHPUnit_Framework_TestCase
	{
		// Arr::add()
		public function testArrayAdd()
		{
			$source = ['alpha' => 'foo'];
			$target = ['alpha' => 'foo', 'beta' => 'bar'];

			// Add once. Should set the 'beta' key to the 'bar' string.
			$this->assertEquals($target, Arr::add($source, 'beta', 'bar'));

			// Add again. Should not change the source array anymore.
			$this->assertEquals($target, Arr::add($source, 'beta', 'bar'));
		}
	}