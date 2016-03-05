<?php namespace nyx\storage\file\exceptions;

	// External dependencies
	use nyx\core;

	/**
	 * File Does Not Exist Exception
	 *
	 * @package     Nyx\Storage\File
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/storage/file.html
	 */

	class FileNotExists extends core\exceptions\NotExists
	{
		/**
		 * @var \SplFileInfo    $file   The File which does not exist.
		 */

		private $file;

		/**
		 * {@inheritDoc}
		 *
		 * @param   \SplFileInfo    $file   The File which does not exist.
		 */

		public function __construct(\SplFileInfo $file, $message = null, $code = null, \Exception $previous = null)
		{
			// Make sure the File is available within this Exception.
			$this->file = $file;

			// Proceed to create the base Exception.
			parent::__construct($message !== null ? $message : "The file [{$file->getPath()}] does not exist.", $code, $previous);
		}
	}