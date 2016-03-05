<?php namespace nyx\console\helpers;

	// Internal dependencies
	use nyx\console\interfaces;
	use nyx\console\descriptors;
	use nyx\console;

	/**
	 * Descriptor Helper
	 *
	 * An Output instance may be set within the helper - to provide sane defaults. If it is not set, the helper will
	 * assume Stdout. The Output instance to use may also be passed to the describe method directly, which will override
	 * any set defaults.
	 *
	 * @package     Nyx\Console\Helpers
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/helpers.html
	 */

	class Descriptor extends console\Helper
	{
		/**
		 * @var interfaces\Descriptor[]     The registered Descriptors.
		 */

		private $descriptors;

		/**
		 * {@inheritDoc}
		 */

		public function __construct($name = null, interfaces\Output $output = null)
		{
			parent::__construct($name ?: 'descriptor', $output);

			// Populate this helper with some default Descriptors.
			$this->descriptors = [];

			$this
				->register('txt',  new descriptors\Text)
				->register('json', new descriptors\Json);
		}

		/**
		 * Describes the given object, assuming it is supported by the Descriptor registered for the given format.
		 *
		 * @param   object              $object     The object which should be described.
		 * @param   string              $format     Which format the description should be in (defaults to 'txt').
		 * @param   bool                $raw        Whether the output should be raw.
		 * @param   array               $options    Additional options to be passed to the descriptor (may overwrite
		 *                                          $format and $raw).
		 * @param   interfaces\Output   $output     Refer to the class description for information about I\O defaults.
		 * @return  $this
		 * @throws  \InvalidArgumentException       When the given format is not supported.
		 */

		public function describe($object, $format = null, $raw = false, array $options = [], interfaces\Output $output = null)
		{
			// Prepare the options to be passed to the concrete Descriptor.
			$options = array_merge([
				'raw' => $raw,
				'format' => $format ?: 'txt'
			], $options);

			// Make sure the given format is supported.
			if(!isset($this->descriptors[$options['format']]))
			{
				throw new \InvalidArgumentException(sprintf('Unsupported format "%s".', $options['format']));
			}

			// Output needs to be handled with the appropriate flag if raw output was requested.
			$type = (!$raw and 'txt' === $options['format']) ? interfaces\Output::NORMAL : interfaces\Output::RAW;

			// Grab the Descriptor for this format.
			$descriptor = $this->descriptors[$options['format']];

			// Write the description to the Output.
			$output->write($descriptor->describe($object, $options), 2, $type);

			return $this;
		}

		/**
		 * Registers a Descriptor within this Helper. Will overwrite the Descriptor for the given format if one is
		 * already set.
		 *
		 * @param   string                  $format         The name of the format the Descriptor should handle.
		 * @param   interfaces\Descriptor   $descriptor     The Descriptor instance to register for the given format.
		 * @return  $this
		 */

		public function register($format, interfaces\Descriptor $descriptor)
		{
			$this->descriptors[$format] = $descriptor;

			return $this;
		}
	}