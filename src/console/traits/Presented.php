<?php namespace nyx\console\traits;

	/**
	 * Presented
	 *
	 * A Presented object is one that has a text representation according to a set pattern, by having the placeholders
	 * in the pattern substituted with their respective values.
	 *
	 * @package     Nyx\Console\Output
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/output.html
	 */

	trait Presented
	{
		/**
		 * @var string  The presentation format to use.
		 */

		private $presentationFormat;

		/**
		 * Returns the presentation format.
		 *
		 * @return  string
		 */

		public function getPresentationFormat()
		{
			return $this->presentationFormat;
		}

		/**
		 * Sets the presentation format.
		 *
		 * @param   string  $format
		 */

		public function setPresentationFormat($format)
		{
			$this->presentationFormat = (string) $format;
		}

		/**
		 * Returns an array with the keys corresponding to placeholders in the presentation format.
		 *
		 * @return  array
		 */

		abstract protected function getPresentationValues();

		/**
		 * Returns a text representation of the object.
		 *
		 * @return  string
		 */

		public function asText()
		{
			$message = $this->getPresentationFormat();

			// Loop through all available placeholder and swap the values.
			foreach($this->getPresentationValues() as $name => $value) $message = str_replace("%{$name}%", $value, $message);

			return $message;
		}
	}