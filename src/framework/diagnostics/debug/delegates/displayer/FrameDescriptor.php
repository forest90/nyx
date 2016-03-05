<?php namespace nyx\framework\diagnostics\debug\delegates\displayer;

	// External dependencies
	use nyx\diagnostics\debug;

	/**
	 * Frame Descriptor
	 *
	 * Utility class for the Debug Page Displayer
	 *
	 * @package     Nyx\Framework
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/index.html
	 */

	class FrameDescriptor
	{
		/**
		 * @var debug\Frame
		 */

		private $frame;

		public function setFrame(debug\Frame $frame)
		{
			$this->frame = $frame;
		}

		public function isClosure()
		{
			return false !== stripos($this->frame->getFunction(), '{closure}');
		}

		public function getFile()
		{
			return(str_replace(base_path().'/', '', $this->frame->getFile()) ?: '<#unknown>');
		}

		public function getFunction()
		{
			return $this->isClosure() ? '{closure}' :  $this->frame->getFunction();
		}

		/**
		 *
		 */

		public function __get($key)
		{
			$method = 'get'.ucfirst($key);

			// First let's see if we've got a specific method for this.
			if(method_exists($this, $method)) return $this->$method();

			// Otherwise let's just run some generic automagic.
			return $this->frame->$method();
		}

		public function escape($_, $allowLinks = false)
		{
			$escaped = htmlspecialchars($_, ENT_QUOTES, 'UTF-8');

			// convert URIs to clickable anchor elements:
			if($allowLinks) {
				$escaped = preg_replace(
					'@([A-z]+?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@',
					"<a href=\"$1\" target=\"_blank\">$1</a>", $escaped
				);
			}

			return $escaped;
		}
	}