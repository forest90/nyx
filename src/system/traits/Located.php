<?php namespace nyx\system\traits;

	/**
	 * Located
	 *
	 * A Located object is one that operates within a directory on the local filesystem.
	 *
	 * @package     Nyx\System\Traits
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/system/traits.html
	 */

	trait Located
	{
		/**
		 * @var string  The working directory.
		 */

		private $directory;

		/**
		 * Returns the working directory for the call.
		 *
		 * @return  string
		 */

		public function getDirectory()
		{
			return $this->directory;
		}

		/**
		 * Sets the working directory for the call.
		 *
		 * @param   string|null $directory  The working directory.
		 * @param   bool        $create     Whether the directory should be automatically created when
		 *                                  it does not exist.
		 * @return  $this
		 * @throws  \LogicException         When the given directory was not null and couldn't be found
		 */

		public function setDirectory($directory = null, $create = false)
		{
			// Make sure the directory is either null or a valid string
			$this->directory = empty($directory) ? null : (string) $directory;

			// Check whether the given directory actually exists
			// Note: We're throwing this after already setting the directory anyway since
			// the developer might want to catch the exception and do something about it.
			if($this->directory and !is_dir($this->directory))
			{
				// Attempt to create the directory if we were asked to do so and throw and
				// exception when mkdir() fails
				if($create and !mkdir($this->directory, null, true))
				{
					throw new \LogicException("The directory [$directory] does not exist and could not be created.");
				}
				else
				{
					throw new \LogicException("The directory [$directory] does not exist.");
				}
			}

			return $this;
		}
	}