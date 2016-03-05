<?php namespace nyx\console\output;

	// Internal dependencies
	use nyx\console\interfaces;

	/**
	 * Formatter
	 *
	 * Responsible for processing strings, detecting formatting tags within them and - depending on whether it is set
	 * to decorate them or not - applying the respective Output Styles or removing any styling, while leaving any tags
	 * not matching the pattern unharmed.
	 *
	 * Styling can be applied either by:
	 *  - defining Styles, whose name in turn becomes the name of the tag to use, ie. for a Style named 'error',
	 *    in order to apply it to the string 'An error occurred!', you would write '<error>An error occurred!</error>'
	 *  - defining inline styling, by using tags resembling inline styles in HTML tags, ie.:
	 *    '<color: white; bg: red>An error occurred!</>'. The order is irrelevant (and whitespaces are optional),
	 *    but it is necessary to specify 'color:' to define a foreground color and 'bg:' for a background color.
	 *    In order to apply additional options, you may use any other prefix. For instance '<weight: bold>Woohoo</>'
	 *    will apply the 'bold' additional option (the word 'weight', however, has no meaning to the parser - it is
	 *    nonetheless needed to match the pattern).
	 *
	 * Please {@see console/output/Style} to see which colors and additional options are supported. ANSI support varies
	 * from system to system (and terminal to terminal) so use it with caution when portability is one of your concerns.
	 *
	 * Based on Symfony 2's Console component. See the LICENSE file distributed with this package for detailed copyright
	 * and licensing information.
	 *
	 * @package     Nyx\Console\Output
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/output.html
	 */

	class Formatter implements interfaces\output\Formatter
	{
		/**
		 * The styling pattern that should be be parsed by this Formatter.
		 */

		const PATTERN = '#(\\\\?)<(/?)([a-z][a-z0-9_;:-]+)?>((?: [^<\\\\]+ | (?!<(?:/?[a-z]|/>)). | .(?<=\\\\<) )*)#isx';

		/**
		 * @var bool            Whether this Formatter shall decorate strings.
		 */

		private $decorated;

		/**
		 * @var styles\Set      A Style Set available to this formatter.
		 */

		private $styles;

		/**
		 * @var styles\Stack     The style Stack for this Formatter.
		 */

		private $stack;

		/**
		 * Escapes opening tags ("<") in the given string.
		 *
		 * @param   string  $text   The text to escape.
		 * @return  string          The escaped text.
		 */

		public static function escape($text)
		{
			return preg_replace('/([^\\\\]?)</is', '$1\\<', $text);
		}

		/**
		 * Initializes the console output formatter.
		 *
		 * @param   bool            $decorated  Whether this formatter should actually decorate strings.
		 * @param   styles\Set      $set        A style Set to be used instead of the default.
		 * @param   Style|string    $default    The default text styling - either a Style or the name of a Style present
		 *                                      in the Set that's going to be used.
		 */

		public function __construct($decorated = null, styles\Set $set = null, $default = null)
		{
			$this->setDecorated($decorated);

			// If we were given a style Set, we shall use it. Otherwise we will construct a new default Set.
			$this->styles = $set ?: new styles\Set(
			[
				'error'     => new Style('white', 'red'),
				'info'      => new Style('green'),
				'comment'   => new Style('cyan'),
				'important' => new Style('red'),
				'header'    => new Style('black', 'cyan')
			]);

			// If we got the name of a default style, grab it from the Set while we're in this scope. Otherwise
			// pass it along and let the Stack do its validation.
			$this->stack = new styles\Stack(is_string($default) ? $this->styles->get($default) : $default);
		}

		/**
		 * {@inheritDoc}
		 */

		public function setDecorated($decorated)
		{
			$this->decorated = (bool) $decorated;
		}

		/**
		 * {@inheritDoc}
		 */

		public function isDecorated()
		{
			return $this->decorated;
		}

		/**
		 * {@inheritDoc}
		 */

		public function getStyles()
		{
			return $this->styles;
		}

		/**
		 * Returns the style processing stack in use by this Formatter.
		 *
		 * @return  styles\Stack
		 */

		public function getStack()
		{
			return $this->stack;
		}

		/**
		 * {@inheritDoc}
		 */

		public function format($message)
		{
			$message = preg_replace_callback(self::PATTERN, [$this, 'process'], $message);

			return str_replace('\\<', '<', $message);
		}

		/**
		 * Processes parts of the matched pattern (self::PATTERN) and applies or removes styling depending on what
		 * was matched.
		 *
		 * @param   array   $match
		 * @return  string
		 */

		protected function process($match)
		{
			// Handle "\<" characters (escaped opening tags)
			if($match[1] === '\\') return $this->applyCurrentStyle($match[0]);

			// For cases when there's no text included within the tag...
			if($match[3] === '')
			{
				// Empty closing tag - "</>"
				if('/' === $match[2])
				{
					$this->stack->pop();

					return $this->applyCurrentStyle($match[4]);
				}

				// Empty opening tag - "<>"
				return '<>'.$this->applyCurrentStyle($match[4]);
			}

			// Getting to this point means we'll be dealing with a specific style.
			if($this->styles->has($match[3]))
			{
				$style = $this->styles->get($match[3]);
			}
			// If the style is not present in our Styles Set, let's see if we can build it based on the string given.
			elseif(!$style = $this->detectInlineStyle($match[3]))
			{
				// Guess we couldn't parse it after all, so let's just stick with the current Style.
				return $this->applyCurrentStyle($match[0]);
			}

			// With an ending tag, stop applying the style of the tag we are closing.
			if('/' === $match[2])
			{
				$this->stack->pop($style);
			}
			// Otherwise push a new Style to the Stack and keep formatting with it until a closing tag gets processed.
			else
			{
				$this->stack->push($style);
			}

			return $this->applyCurrentStyle($match[4]);
		}

		/**
		 * Attempts to create a new style instance based on an inline styling syntax. Please consult the class description
		 * for more information on how inline styling works.
		 *
		 * @param   string      $string     The string to use.
		 * @return  Style|bool              False if the given string is not formatted in a way that makes it possible
		 *                                  for us to parse it; otherwise the Style that was created based on it.
		 */

		protected function detectInlineStyle($string)
		{
			if(!preg_match_all('/([^:]+):([^;]+)(;|$)/', strtolower($string), $matches, PREG_SET_ORDER)) return false;

			// H-okay, apparently we got a match, so let's create a new Style instance.
			$style = new Style;

			foreach($matches as $match)
			{
				// First match is full matched string, which we don't need here.
				array_shift($match);

				// Be forgiving regarding whitespaces (or lack thereof) in the tag.
				$match[0] = trim($match[0]);
				$match[1] = trim($match[1]);

				if($match[0] === 'color')
				{
					$style->setForeground($match[1]);
				}
				elseif($match[0] === 'bg')
				{
					$style->setBackground($match[1]);
				}
				else
				{
					$style->setAdditional([$match[1]]);
				}
			}

			return $style;
		}

		/**
		 * Applies the top-most Style in the Stack onto the given string.
		 *
		 * @param   string  $text   The text which should get styled.
		 * @return  string          The styled text if the Formatter is set to apply decorations and the string is not
		 *                          empty, the input string otherwise.
		 */

		protected function applyCurrentStyle($text)
		{
			return ($this->decorated and strlen($text) > 0) ? $this->stack->current()->apply($text) : $text;
		}
	}