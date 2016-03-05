<?php namespace nyx\console\view;

	/**
	 * Ncurses
	 *
	 * @package     Nyx\Console\Output\View
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/view/ncurses.html
	 */

	class Ncurses
	{
		/**
		 * Key constants.
		 */

		const KEY_LF  = 13;
		const KEY_CR  = 10;
		const KEY_ESC = 27;
		const KEY_TAB = 9;

		/**
		 * @var ncurses\Window  The main Window for this Session.
		 */

		private $window;

		/**
		 * @var ncurses\Cursor  The main Cursor for this Session.
		 */

		private $cursor;

		/**
		 * Constructs a new Ncurses instance.
		 */

		public function __construct()
		{
			ncurses_init();

			$this->window = (new ncurses\Window)->updateDimensions();
			$this->cursor = new ncurses\Cursor(0, 0, ncurses\Cursor::NORMAL, $this->window);

			ncurses_keypad($this->window->get(), true);
			ncurses_noecho();

			$this->window->drawBorder();
		}

		/**
		 * Ends the ncurses session and cleans up the screen of the terminal.
		 *
		 * @return  $this
		 */

		public function end()
		{
			ncurses_end();

			return $this;
		}

		/**
		 * Refreshes the main Window.
		 *
		 * @return  $this
		 */

		public function redraw()
		{
			$this->window->refresh();

			return $this;
		}

		/**
		 * Ensures the terminal gets cleaned up when the instance gets destructed.
		 */

		public function __destruct()
		{
			$this->end();
		}
	}