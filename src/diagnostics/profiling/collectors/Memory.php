<?php namespace nyx\diagnostics\profiling\collectors;

	// External dependencies
	use nyx\utils;

	// Internal dependencies
	use nyx\diagnostics\profiling;

	/**
	 * Memory Data Collector
	 *
	 * Represents a set of data about the memory usage during runtime of your script.
	 *
	 * @package     Nyx\Diagnostics\Profiling
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/profiling/index.html
	 */

	class Memory extends profiling\Collector
	{
		/**
		 * {@inheritDoc}
		 *
		 * @param   int $offset     A positive or negative integer denoting the number of bytes that should be added or
		 *                          subtracted from the peak usage upon calling self::getUsage(). This could, for
		 *                          instance, be the negative memory_get_usage(true) value measured right at the
		 *                          beginning of your script, before any heavy lifting starts to happen, to give
		 *                          more meaningful data.
		 */

		public function __construct($offset = 0, $name = 'memory')
		{
			$this->data['offset'] = (int) $offset;

			parent::__construct($name);
		}

		/**
		 * {@inheritDoc}
		 */

		public function collect(profiling\Context $context = null)
		{
			$this->update();

			// Grab PHP's memory limit if we haven't done so already.
			!isset($this->data['limit']) and $this->data['limit'] = utils\Unit::sizeStringToBytes(ini_get('memory_limit'));
		}

		/**
		 * Returns the peak memory usage in bytes allocated to the script at the moment of data collection plus
		 * the static offset {@see __construct()}.
		 *
		 * @return  int
		 */

		public function getUsage()
		{
			return $this->data['peak'] + $this->data['offset'];
		}

		/**
		 * Returns PHP's memory limit in bytes.
		 *
		 * @return  int
		 */

		public function getLimit()
		{
			return $this->data['limit'];
		}

		/**
		 * Updates the peak memory usage as reported by PHP.
		 */

		public function update()
		{
			$this->data['peak'] = memory_get_peak_usage(true);
		}
	}
