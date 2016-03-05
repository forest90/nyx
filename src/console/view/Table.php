<?php namespace nyx\console\view;

	// External dependencies
	use nyx\utils;

	// Internal dependencies
	use nyx\console\interfaces;

	/**
	 * Table
	 *
	 * Base table class which provides facilities for concrete classes to deal with row/header manipulations and allows
	 * them to offload onto this class some of the basic tasks related to displaying the data.
	 *
	 * @package     Nyx\Console\Output\View
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/view/tables.html
	 */

	abstract class Table
	{
		/**
		 * The string that will be printed if the table contains no data (rows) at all. Note: This is printed only in
		 * place of the rows - if the child classes print headers/borders, this string will only be part of the output.
		 * As such it is wrapped in curly brackets to make it easier to parse.
		 */

		const NO_DATA = "{NULL}";

		/**
		 * @var array   The headers of this table.
		 */

		private $headers;

		/**
		 * @var array   The rows containing data.
		 */

		private $rows;

		/**
		 * @var bool    Whether the displayed table shall include the column headers. Defaults to true.
		 */

		private $displayHeaders;

		/**
		 * Constructs a new Table instance.
		 *
		 * @param   array   $headers            The headers of this Table.
		 * @param   array   $rows               The rows containing data.
		 * @param   bool    $displayHeaders     whether the headers should also be included in the output or not.
		 */

		public function __construct(array $headers = null, array $rows = null, $displayHeaders = true)
		{
			$this->displayHeaders = (bool) $displayHeaders;

			$headers and $this->setHeaders($headers);
			$rows    and $this->replace($rows);
		}

		/**
		 * Prints the Table to the given Output.
		 *
		 * @param   interfaces\Output   $output         The Output the Table will be displayed in.
		 * @return  $this
		 */

		public function render(interfaces\Output $output)
		{
			$output->writeln($this->prepare($output));
			$output->ln();

			return $this;
		}

		/**
		 * Returns the headers row of this Table.
		 *
		 * @return  array
		 */

		public function getHeaders()
		{
			return $this->headers;
		}

		/**
		 * Sets the headers row for this Table.
		 *
		 * Note: For consistency, headers should be set *before* any rows are inserted so this class can ensure that
		 * the column count of any inserted row matches the column count of the headers without additional overhead.
		 *
		 * @param   array   $headers    The headers to set.
		 * @return  $this
		 */

		public function setHeaders(array $headers)
		{
			$this->validateRow($headers);
			$this->headers = $headers;

			// Re-validate the already set data rows.
			// *If* you are now complaining that you're getting swamped with exceptions and argumenting that you intended
			// to provide the correct rows afterwards, how about simply creating a fresh and shiny new Table? Instead?
			if(!empty($this->rows))
			{
				foreach($this->rows as $row) $this->validateRow($row);
			}

			return $this;
		}

		/**
		 * Returns all data rows contained in this table.
		 *
		 * @return  array
		 */

		public function getRows()
		{
			return $this->rows;
		}

		/**
		 * Sets a given row of data at the given index.
		 *
		 * @param   int|string  $key    The key to insert the row at. Different from the add() method, this method will
		 *                              silently overwrite an existing row if it is present at the given index.
		 * @param   array       $row    The row to set.
		 * @return  $this
		 */

		public function set($key, array $row)
		{
			$this->validateRow($row);
			$this->rows[$key] = $row;

			return $this;
		}

		/**
		 * Inserts a new row into the table.
		 *
		 * @param   array       $row    The row to insert.
		 * @param   int|string  $key    The key to insert the row at. Different from the set() method, defining a key
		 *                              here will first check if the given key is already present and only insert the
		 *                              row if that is not the case.
		 * @return  $this
		 */

		public function add(array $row, $key = null)
		{
			if($key === null)
			{
				$key = count($this->rows);
			}
			elseif(isset($this->rows[$key]))
			{
				return $this;
			}

			return $this->set($key, $row);
		}

		/**
		 * Replaces all current rows of data with the rows provided to this method.
		 *
		 * @param   array   $rows   The replacement rows.
		 * @return  $this
		 */

		public function replace(array $rows)
		{
			$this->rows = [];

			foreach($rows as $row) $this->add($row);

			return $this;
		}

		/**
		 * Sorts all rows according to the alphabetical order of the given column. Can either take a numerical index
		 * or the header of the column to sort by.
		 *
		 * @param   int|string  $column         The index of the column that should be used for sorting or the header
		 *                                      (ie. string) of the column. Defaults to the first column (0) for
		 *                                      standard use cases (IDs, names, titles etc.).
		 * @return  $this
		 * @throws  \InvalidArgumentException   When no column is defined at the given index or no column header with
		 *                                      the given name was found if a string was passed.
		 */

		public function sort($column = 0)
		{
			// Allow to sort by column names, if strings are passed instead of integers.
			if(is_string($column))
			{
				// Alias the variable in case we have to throw an exception in a moment.
				$header = $column;

				if(false === $column = array_search($column, $this->headers))
				{
					throw new \InvalidArgumentException("Unable to sort: No column header named [$header] was found in the table.");
				}
			}

			// Ensure the column is actually available. What would we sort by otherwise?
			if(!isset($this->headers[$column]))
			{
				throw new \InvalidArgumentException("Unable to sort: No column defined at index [$column].");
			}

			// Do a barrel roll! Yeah, well, no, just sort the data.
			usort($this->rows, function($a, $b) use ($column)
			{
				return strcmp($a[$column], $b[$column]);
			});

			return $this;
		}

		/**
		 * Sets whether the headers should also be included in the output or not.
		 *
		 * @param   bool    $bool
		 * @return  $this
		 */

		public function setDisplayHeaders($bool)
		{
			$this->displayHeaders = (bool) $bool;

			return $this;
		}

		/**
		 * Checks whether the table is set to also display its headers.
		 *
		 * @return  bool    True when the Table is set to also display its headers, false otherwise.
		 */

		public function isDisplayingHeaders()
		{
			return $this->displayHeaders;
		}

		/**
		 * Checks whether the Table contains any data rows.
		 *
		 * @return  bool    True when the Table contains any data rows, false otherwise.
		 */

		public function hasData()
		{
			return !empty($this->rows);
		}

		/**
		 * Formats all rows according to the implementation and returns an array of strings where each string represents
		 * a single line (formatted, so it may also include borders, padding etc.) of the table.
		 *
		 * @param   interfaces\Output   $output         The Output the Table should be prepared for.
		 * @return  array
		 */

		protected function prepare(interfaces\Output $output)
		{
			$messages = [];

			// Grab the border once for less reuse overhead. Child classes don't necessarily need to provide a border
			// and as such we'll go on checking if it was provided.
			$border = $this->border();
			$border and $messages[] = $border;

			// Output of table headers is optional, albeit enabled by default.
			if($this->displayHeaders)
			{
				$messages[] = $this->row($this->headers);

				// Separate them with a horizontal border from the rest of the rows.
				$border and $messages[] = $border;
			}

			if($this->hasData())
			{
				foreach($this->rows as $row) $messages[] = $this->row($row);
			}
			else
			{
				$messages[] = static::NO_DATA;
			}

			// Bottom horizontal border.
			$border and $messages[] = $border;

			return $messages;
		}

		/**
		 * Formats the given row according to the Table implementation and returns it as string.
		 *
		 * @param   array   $row
		 */

		abstract protected function row(array $row);

		/**
		 * When implemented, returns a horizontal border for the table. Not declared abstract as this behaviour is
		 * purely optional.
		 *
		 * @return  null|string
		 */

		protected function border()
		{
			return null;
		}

		/**
		 * Checks if the given row is a multidimensional array or contains not exactly as many elements as there are
		 * column headers, if those are defined.
		 *
		 * Throws an exception when the respective condition is met. Does not check for the existence of objects etc.,
		 * but PHP will still shower you with errors if you pass something that cannot be converted to a string.
		 *
		 * @param   array   $row                The row to validate.
		 * @return  $this
		 * @throws  \InvalidArgumentException   When the row contains a multidimensional array or when it contains not
		 *                                      exactly as many columns as there are headers.
		 */

		protected function validateRow(array $row)
		{
			if(utils\Arr::isMultidimensional($row))
			{
				throw new \InvalidArgumentException("Rows in a table must not be multidimensional arrays.");
			}

			// Ensure the row contains no more columns than there are defined by their headers.
			if(isset($this->headers) and ($columnCount = count($row) !== $headerCount = count($this->headers)))
			{
				throw new \InvalidArgumentException("A row must contain exactly as many columns as there are headers. $columnCount given, expected $headerCount.");
			}

			return $this;
		}
	}