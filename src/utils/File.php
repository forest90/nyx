<?php namespace nyx\utils;

	/**
	 * File
	 *
	 * Provides basic helper methods to handle the filesystem
	 *
	 * @package     Nyx\Utils\Files
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/utils/files.html
	 */

	class File
	{
		/**
		 * Read the contents of a file into a variable passed by reference.
		 *
		 * @param   string  $filename   The name of the file which should be read.
		 * @param   int     $lines      How many lines should be read? Null to read the whole file.
		 * @param   int     $bytes      How many bytes of each line should be read?
		 * @param   bool    $verbose    Whether to throw exceptions or not.
		 * @return  bool
		 * @throws  \RuntimeException
		 */

		public static function read($filename, $lines = null, $bytes = 4096, $verbose = true)
		{
			$contents = "";
			$curLine = 1;

			if(file_exists($filename))
			{
				if($fd = fopen($filename, 'r'))
				{
					while(!feof($fd))
					{
						$contents .= fgets($fd, $bytes);

						if($lines <= $curLine and $lines !== null) break;

						$curLine++;
					}

					fclose($fd);
					return $contents;
				}
				else
				{
					if($verbose) throw new \RuntimeException('fopen('.$filename.') failed. File could not be read.');
				}
			}
			else
			{
				if($verbose) throw new \RuntimeException('file_exists('.$filename.') failed. The file does not exist.');
			}

			return false;
		}
	}