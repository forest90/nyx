<?php namespace nyx\console\view\tables;

	// Internal dependencies
	use nyx\console\interfaces;
	use nyx\console\view;

	/**
	 * CSV Table
	 *
	 * Note: This class is only meant as a very basic helper and as such - if you need more granular control over
	 * how this works, inheritance shall be your friend. Headers are included by default {@see self::setDisplayHeaders()}.
	 *
	 * To provide a tabbed table (ie. with the fields separated by a tab) just set the delimiter to "\t". It's as simple
	 * as can be, but keep the CSV RFC {@see http://www.ietf.org/rfc/rfc4180.txt} in mind if you want to be standards
	 * compliant.
	 *
	 * @package     Nyx\Console\Output\View
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/view/tables.html
	 */

	class Csv extends view\Table
	{
		/**
		 * @var string  The delimiter to use to separate values.
		 */

		private $delimiter;

		/**
		 * @var string  The enclosure to use for fields containing special characters (ie. the delimiter etc.).
		 */

		private $enclosure;

		/**
		 * @var array   Contains the preg_quote()'d delimiter and enclosure characters after construction.
		 */

		private $escaped;

		/**
		 * {@inheritDoc}
		 *
		 * @param   string  $delimiter  The delimiter to use to separate values.
		 */

		public function __construct(array $headers = null, array $rows = null, $delimiter = ',', $enclosure = '"')
		{
			$this->escaped = [];

			$this->setDelimiter($delimiter);
			$this->setEnclosure($enclosure);

			parent::__construct($headers, $rows);
		}

		/**
		 * {@inheritDoc}
		 */

		public function render(interfaces\Output $output)
		{
			// We will need to ensure all lines are as clean as possible.
			$output->getFormatter()->setDecorated(false);

			return parent::render($output);
		}

		/**
		 * Returns the delimiter used to separate values.
		 *
		 * @return  string
		 */

		public function getDelimiter()
		{
			return $this->delimiter;
		}

		/**
		 * Sets the delimiter to use to separate values.
		 *
		 * @param   string  $char   The delimiter.
		 * @return  $this
		 */

		public function setDelimiter($char)
		{
			$this->delimiter = $char;
			$this->escaped['delimiter'] = preg_quote($char, '/');

			return $this;
		}

		/**
		 * Returns the enclosure used to wrap fields containing special characters.
		 *
		 * @return  string
		 */

		public function getEnclosure()
		{
			return $this->enclosure;
		}

		/**
		 * Sets the enclosure to use for fields containing special characters (ie. the delimiter etc.).
		 *
		 * @param   string  $char      The enclosure.
		 * @return  $this
		 */

		public function setEnclosure($char)
		{
			$this->enclosure = $char;
			$this->escaped['enclosure'] = preg_quote($char, '/');

			return $this;
		}

		/**
		 * {@inheritDoc}
		 */

		protected function row(array $row)
		{
			$output = [];

			foreach($row as $field)
			{
				if(preg_match("/(?:{$this->escaped['delimiter']}|{$this->escaped['enclosure']}|\s)/", $field))
				{
					$output[] = $this->enclosure.str_replace($this->enclosure, $this->enclosure.$this->enclosure, $field).$this->enclosure;
				}
				else
				{
					$output[] = $field;
				}
			}

			return join($this->delimiter, $output);
		}
	}