<?php namespace nyx\framework\diagnostics\debug\delegates;

	// External dependencies
	use nyx\diagnostics\debug as base;

	/**
	 * Base Displayer Delegate
	 *
	 * @package     Nyx\Framework
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/index.html
	 */

	abstract class Displayer implements base\interfaces\Delegate
	{
		/**
		 * @var string  The path to the resources used by the Displayer.
		 */

		private $resourcesPath;

		/**
		 *
		 *
		 * @return string
		 */

		public function getResourcesPath()
		{
			return $this->resourcesPath ?: __DIR__ . '/../Resources';
		}

		/**
		 *
		 *
		 * @param   string  $path               The path to set.
		 * @throws  \InvalidArgumentException   When the given argument does not point to an existing directory.
		 */

		public function setResourcesPath($path)
		{
			if(!is_dir($path))
			{
				throw new \InvalidArgumentException("Non-existent resource path given [$path].");
			}

			$this->resourcesPath = $path;
		}
	}