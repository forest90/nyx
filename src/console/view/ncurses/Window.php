<?php namespace nyx\console\view\ncurses;

	/**
	 * Window
	 *
	 * @package     Nyx\Console\Output\View
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/view/ncurses.html
	 */

	class Window
	{
		/**
		 * Window border style constants.
		 */

		const BORDER_SOLID  = 1;    // ┐
		const BORDER_DOUBLE = 2;    // ╗

		/**
		 * @var resource    The actual window resource once opened by the ncurses extension.
		 */

		private $window;

		/**
		 * @var int         The number of columns of the Window (width).
		 */

		private $width;

		/**
		 * @var int         The number of rows of the Window (height).
		 */

		private $height;

		/**
		 * Constructs a new Window.
		 *
		 * @param   int $columns    The number of columns of the Window (width)
		 * @param   int $rows       The number of rows of the Window (height).
		 * @param   int $x          The x-coordinate of the origin.
		 * @param   int $y          The y-coordinate of the origin.
		 */

		public function __construct($columns = 0, $rows = 0, $x = 0, $y = 0)
		{
			$this->window = ncurses_newwin($rows, $columns, $y, $x);

			$this->updateDimensions();
		}

		/**
		 * Returns the underlying window resource opened by the ncurses extension.
		 *
		 * @return  resource
		 */

		public function get()
		{
			return $this->window;
		}

		/**
		 * Fetches the (maximum) width and height of the underlying ncurses window and stores the up-to-date values
		 * in this Window instance.
		 *
		 * @return  $this
		 */

		public function updateDimensions()
		{
			// Might need to refresh first in order to ensure the values are up-to-date when the terminal got resized
			// in the meantime.
			$this->refresh();

			ncurses_getmaxyx($this->window, $this->height, $this->width);

			return $this;
		}

		/**
		 * Returns the (maximum) width of the underlying ncurses window.
		 *
		 * @return  int
		 */

		public function getWidth()
		{
			$this->updateDimensions();

			return $this->width;
		}

		/**
		 * Returns the (maximum) height of the underlying ncurses window.
		 *
		 * @return  int
		 */

		public function getHeight()
		{
			$this->updateDimensions();

			return $this->height;
		}

		/**
		 * Draws borders around this Window.
		 *
		 * @param   int     $style  One of the window border style constants defined in this class.
		 * @return  $this
		 * @todo                    Allow for dynamically added border styles (extract from arrays etc.).
		 */

		public function drawBorder($style = null)
		{
			switch($style)
			{
				case self::BORDER_DOUBLE:

					$this->doDrawBorder(ord('║'), ord('║'), ord('═'), ord('═'), ord('╔'), ord('╗'), ord('╚'), ord('╝'));

				break;

				default:

					$this->doDrawBorder();
			}

			return $this->refresh();
		}

		/**
		 * Performs the actual drawing of borders around this Window.
		 *
		 * @param   int     $l      The left border character.
		 * @param   int     $r      The right border character.
		 * @param   int     $t      The top border character.
		 * @param   int     $b      The bottom border character.
		 * @param   int     $tl     The top-left corner character.
		 * @param   int     $tr     The top-right corner character.
		 * @param   int     $bl     The bottom-left corner character.
		 * @param   int     $br     The bottom-right corner character.
		 * @return  $this
		 */

		protected function doDrawBorder($l = 0, $r = 0, $t = 0, $b = 0, $tl = 0, $tr = 0, $bl = 0, $br = 0)
		{
			ncurses_wborder($this->window, $l, $r, $t, $b, $tl, $tr, $bl, $br);

			return $this;
		}

		/**
		 * Refreshes the Window.
		 *
		 * @return  $this
		 */

		public function refresh()
		{
			ncurses_wrefresh($this->window);

			return $this;
		}
	}