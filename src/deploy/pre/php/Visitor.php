<?php namespace nyx\deploy\pre\php;

	/**
	 * Visitor
	 *
	 * Represents an object visiting files containing PHP code.
	 *
	 * @package     Nyx\Deploy\Pre
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/deploy/pre.html
	 */

	abstract class Visitor extends \PHPParser_NodeVisitorAbstract
	{
	    /**
	     * @var string  The path to the file currently being parsed.
	     */

	    private $path = '';

	    /**
	     * Sets the path to the file currently being parsed.
	     *
	     * @param   string  $path
	     * @return  $this
	     */

	    public function setPath($path)
	    {
	        $this->path = $path;

	        return $this;
	    }

	    /**
	     * Returns the path to the file currently being parsed.
	     *
	     * @return  string
	     */

	    public function getPath()
	    {
	        return $this->path;
	    }

	    /**
	     * Returns the path to the directory of the file currently being parsed.
	     *
	     * @return  string
	     */

	    public function getDirectory()
	    {
	        return dirname($this->path);
	    }
	}