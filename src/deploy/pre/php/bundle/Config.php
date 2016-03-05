<?php namespace nyx\deploy\pre\php\bundle;

	// Internal dependencies
	use nyx\deploy\pre\php;

	/**
	 * Bundle Config
	 *
	 * @package     Nyx\Deploy\Pre
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/deploy/pre.html
	 */

	class Config implements \IteratorAggregate
	{
	    /**
	     * @var php\Visitor[]
	     */

	    private $visitors;

	    /**
	     * @var array   The paths to the files to bundle.
	     */

	    private $paths;

		/**
		 * @var array   The inclusive filters.
		 */

		private $allowed;

	    /**
	     * @var array   The exclusive filters.
	     */

	    private $disallowed;

		/**
		 * @var array   The paths to the files to bundle after they have been filtered.
		 */

		private $filtered;

		/**
		 * Constructs the Bundle Config.
		 */

		public function __construct()
		{
			$this->visitors   = [];
			$this->paths      = [];
			$this->allowed    = [];
			$this->disallowed = [];
		}
	    /**
	     * Adds a file path.
	     *
	     * @param   string  $path
	     * @return  $this
	     */

	    public function addFile($path)
	    {
	        $this->paths[]  = $path;
		    $this->filtered = null;

	        return $this;
	    }

	    /**
	     * Returns the file paths which satisfy the filters set.
	     *
	     * @return  array
	     */

	    public function getPaths()
	    {
		    // If we've got a cached result, return it right away.
		    if(null !== $this->filtered) return $this->filtered;

	        $paths = [];

	        foreach($this->paths as $path)
	        {
	            foreach($this->allowed as $filter)
	            {
	                if(!preg_match($filter, $path)) continue 2;
	            }

	            foreach($this->disallowed as $filter)
	            {
	                if(preg_match($filter, $path)) continue 2;
	            }

	            $paths[] = $path;
	        }

	        return $this->filtered = $paths;
	    }

	    /**
	     * Returns the registered Node Visitors.
	     *
	     * @return  php\Visitor[]
	     */

	    public function getVisitors()
	    {
	        return $this->visitors;
	    }

		/**
		 * Adds a Node Visitor which will be used to inspect each Node in the files being processed.
		 *
		 * @param   php\Visitor $visitor
		 * @return  $this
		 */

		public function addVisitor(php\Visitor $visitor)
		{
			$this->visitors[] = $visitor;

			return $this;
		}

		/**
		 * Adds a filter that will only allow the file paths which match the given pattern.
		 *
		 * @param   string  $pattern    The regular expression.
		 * @return  $this
		 */

		public function allow($pattern)
		{
			$this->allowed[] = $pattern;

			return $this;
		}

		/**
		 * Adds a filter that will disallow all the file paths which do not match the given pattern.
		 *
		 * @param   string  $pattern    The regular expression.
		 * @return  $this
		 */

		public function disallow($pattern)
		{
			$this->disallowed[] = $pattern;

			return $this;
		}

		/**
		 * Returns an Iterator for the file paths in this Config.
		 *
		 * @return  \ArrayIterator
		 */

		public function getIterator()
		{
			return new \ArrayIterator($this->getPaths());
		}
	}