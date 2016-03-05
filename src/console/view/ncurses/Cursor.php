<?php namespace nyx\console\view\ncurses;

	/**
	 * Cursor
	 *
	 * @package     Nyx\Console\Output\View
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/view/ncurses.html
	 */

	class Cursor
	{
		/**
		 * Visibility constants.
		 */

		const INVISIBLE = 0;
		const NORMAL    = 1;
		const VISIBLE   = 2;

		/**
		 * @var Window  The Window this Cursor is being displayed in.
		 */

		private $window;

		/**
		 * @var array   The position of the Cursor (an array containing two values - x and y).
		 */

		private $position;

		/**
		 * @var int     The visibility level of this Cursor.
		 */

		private $visibility;

		/**
		 * Constructs a new Cursor.
		 *
		 * @param   int     $x          The x (horizontal) position of the Cursor.
		 * @param   int     $y          The y (vertical) position of the Cursor.
		 * @param   int     $visibility The visibility level of the Cursor.
		 * @param   Window  $window     The Window the Cursor is being displayed in.
		 */

		public function __construct($x = 0, $y = 0, $visibility = self::NORMAL, Window $window = null)
		{
			$this->position = [];

			$this->setPosition($x, $y);
			$this->setVisibility($visibility);

			if(null !== $window) $this->setWindow($window);
		}

		/**
		 * Returns the position of the cursor.
		 *
		 * @return  array   The position of the Cursor (an array containing two values - x and y).
		 */

		public function getPosition()
		{
			return $this->position;
		}

		/**
		 * Sets the position of the Cursor.
		 *
		 * @param   int     $x  The x (horizontal) position of the Cursor.
		 * @param   int     $y  The y (vertical) position of the Cursor.
		 * @return  $this
		 */

		public function setPosition($x, $y)
		{
			$this->position = [(int) $x, (int) $y];

			return $this;
		}

		/**
		 * Returns the x (horizontal) position of the Cursor.
		 *
		 * @return  int
		 */

		public function getX()
		{
			return $this->position[0];
		}


		/**
		 * Returns the y (vertical) position of the Cursor.
		 *
		 * @return  int
		 */

		public function getY()
		{
			return $this->position[1];
		}

		/**
		 * Returns the visibility level of the Cursor.
		 *
		 * @return  int
		 */

		public function getVisibility()
		{
			return $this->visibility;
		}

		/**
		 * Sets the visibility level of the Cursor.
		 *
		 * @param   int $level              The visibility level to set (one of the visibility constants defined in
		 *                                  this Class).
		 * @return  $this
		 * @throws  \OutOfBoundsException   When the given visibility level is not supported.
		 */

		public function setVisibility($level)
		{
			// Make sure the given visibility is supported, ie. defined by the visibility constants.
			if(0 > $level or 2 < $level)
			{
				throw new \OutOfBoundsException("The given Cursor visibility [$level] is not supported. Must be one of: 0, 1 or 2.");
			}

			ncurses_curs_set($level = (int) $level);

			$this->visibility = $level;

			return $this;
		}

		/**
		 * Returns the Window this Cursor is being displayed in.
		 *
		 * @return  Window
		 */

		public function getWindow()
		{
			return $this->window;
		}

		/**
		 * Sets the Window this Cursor is being displayed in.
		 *
		 * @param   Window  $window
		 * @return  $this
		 */

		public function setWindow(Window $window)
		{
			$this->window = $window;

			return $this;
		}
	}