<?php namespace nyx\console\view\tables;

	// Internal dependencies
	use nyx\console\interfaces;
	use nyx\console\view;

	/**
	 * Ascii Table
	 *
	 * A table which has ASCII borders and its data padded to conform to column widths in order to provide a legible
	 * grid, by default similar to that of the commandline MySQL client and its query results.
	 *
	 * @package     Nyx\Console\Output\View
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/view/tables.html
	 */

	class Ascii extends view\Table
	{
		/**
		 * The character constants used to identify the positions of a specific character in the string of characters
		 * used to build the Table.
		 */

		const CORNER     = 0;    // Table corner.
		const HORIZONTAL = 1;    // Horizontal delimiter.
		const VERTICAL   = 2;    // Vertical delimiter.

		/**
		 * @var string  The characters used to construct the borders of the Table.
		 */

		private $characters;

		/**
		 * @var array   The column widths.
		 */

		private $widths;

		/**
		 * @var string  Stores the horizontal table border after it has been calculated by the border() method.
		 */

		private $border;

		/**
		 * {@inheritDoc}
		 *
		 * @param   string  $characters     The characters that should be used to build the Table. Expects exactly 3
		 *                                  characters.
		 */

		public function __construct(array $headers = null, array $rows = null, $characters = '+-|', $displayHeaders = true)
		{
			$this->setCharacters($characters);

			// Let the parent do the basic stuff.
			parent::__construct($headers, $rows, $displayHeaders);
		}

		/**
		 * Sets the characters that should be used to build the Table.
		 *
		 * @param   string  $characters         The characters to set.
		 * @return  $this
		 * @throws  \InvalidArgumentException   When a string containing not exactly 3 characters has been given.
		 */

		public function setCharacters($characters)
		{
			if(3 !== $len = strlen($characters))
			{
				throw new \InvalidArgumentException("An Ascii Table expects exactly 3 characters to build the table from. [$len] given in string [$characters].");
			}

			$this->characters = $characters;

			return $this;
		}

		/**
		 * Sets a specific character in the string used to be build the Tabłe.
		 *
		 * @param   int     $which              Which character should be set (one of the character constants defined
		 *                                      in this class).
		 * @param   string  $character          The character to set. When a string longer than 1 character is given,
		 *                                      only the first character will be used.
		 * @return  $this
		 * @throws  \OutOfBoundsException       When the given character position is not recognized.
		 */

		public function setCharacter($which, $character)
		{
			// Make sure the given position is supported, ie. defined in the character constants.
			if(0 > $which or 2 < $which)
			{
				throw new \OutOfBoundsException("The given character position [$which] is not supported. Must be one of: 0, 1 or 2.");
			}

			$this->characters[$which] = $character[0];

			return $this;
		}

		/**
		 * {@inheritDoc}
		 *
		 * Overridden to reset the widths upon setting the headers.
		 */

		public function setHeaders(array $headers)
		{
			$this->widths = null;

			return parent::setHeaders($headers);
		}

		/**
		 * {@inheritDoc}
		 *
		 * Overridden to reset the widths upon setting a new data row.
		 */

		public function set($key, array $row)
		{
			$this->widths = null;

			return parent::set($key, $row);
		}

		/**
		 * {@inheritDoc}
		 */

		protected function prepare(interfaces\Output $output)
		{
			// We need to calculate the widths of the columns if this has not been done already for the given set.
			if($this->widths === null) $this->calculateWidths($output->getFormatter());

			return parent::prepare($output);
		}

		/**
		 * Returns the horizontal border as string. Its width depends on the current total width of the columns and
		 * the resulting border is cached. Therefore, this should only be called before the final output or the $redraw
		 * flag should be used if such is not the case.
		 *
		 * @param   bool    $redraw     Whether the border should be redrawn and cached anew, regardless whether it was
		 *                              already drawn or not.
		 * @return  string              The horizontal table border.
		 */

		protected function border($redraw = false)
		{
			if($redraw or !isset($this->border))
			{
				$this->border = $this->characters[self::CORNER];

				foreach($this->widths as $width)
				{
					$this->border .= str_repeat($this->characters[self::HORIZONTAL], $width + 2);
					$this->border .= $this->characters[self::CORNER];
				}
			}

			return $this->border;
		}

		/**
		 * Formats the given table row by applying borders and column padding where appropriate and returns it
		 * as a string.
		 *
		 * @param   array   $row    The row to be processed.
		 * @return  string          The formatted row as string.
		 */

		protected function row(array $row)
		{
			// We need every single column in the row padded to the total width of the given column.
			$row = array_map(function($content, $column)
			{
				$width = $this->widths[$column];

				return sprintf(" %-${width}s ", $content);

			}, $row, array_keys($row));

			// The columns will be joined using the border char in a second, but we also need an outer border so two
			// empty columns - one at the beginning and one at the end - should do the trick.
			array_unshift($row, '');
			array_push($row, '');

			return join($this->characters[self::VERTICAL], $row);
		}

		/**
		 * Loops through the rows currently available (including headers) and determines the maximal width of each
		 * column. Caches the results locally.
		 *
		 * @param   interfaces\output\Formatter $formatter  The Formatter to use to (pre)format the strings in order to
		 *                                                  calculate their final widths.
		 * @return  $this
		 */

		protected function calculateWidths(interfaces\output\Formatter $formatter)
		{
			// We'll merge the headers with the data rows locally and treat them equally as rows to simplify things.
			foreach(array_merge([$this->getHeaders()], $this->getRows()) as $row)
			{
				foreach($row as $column => $data)
				{
					$width = mb_strlen($formatter->format($data));

					if(empty($this->widths[$column]) or $width > $this->widths[$column])
					{
						$this->widths[$column] = $width;
					}
				}
			}

			return $this;
		}
	}