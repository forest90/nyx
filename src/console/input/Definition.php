<?php namespace nyx\console\input;

	/**
	 * Input Definition
	 *
	 * This class represents a master Definition, ie. one that contains respective Definition Bags for both arguments
	 * and options.
	 *
	 * @package     Nyx\Console\Input
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/input.html
	 */

	class Definition
	{
		/**
		 * @var bags\definitions\Arguments  The Arguments Definition Bag.
		 */

		private $arguments;

		/**
		 * @var bags\definitions\Options    The Options Definition Bag.
		 */

	    private $options;

	    /**
	     * Constructs a new Input Definition instance.
	     *
	     * @param   bags\definitions\Arguments|array    $arguments  The Arguments in this definition. Either a bag or an
	     *                                                          array containing Argument objects.
	     * @param   bags\definitions\Options|array      $options    The Options in this definition. Either a bag or an
	     *                                                          array containing Option objects.
	     */

	    public function __construct($arguments = null, $options = null)
	    {
		    $this->arguments = $arguments ? (is_array($arguments) ? new bags\definitions\Arguments($arguments) : $arguments) : new bags\definitions\Arguments;
		    $this->options = $options ? (is_array($options) ? new bags\definitions\Options($options) : $options) : new bags\definitions\Options;
	    }

		/**
		 * Returns the Arguments Definition Bag.
		 *
		 * @return  bags\definitions\Arguments
		 */

		public function arguments()
		{
			return $this->arguments;
		}

		/**
		 * Returns the Options Definition Bag.
		 *
		 * @return  bags\definitions\Options
		 */

		public function options()
		{
			return $this->options;
		}

		/**
		 * Merges this Definition with other Definition(s) and returns the result as a new instance, meaning this one,
		 * even though used as base for the merger, will be left unscathed.
		 *
		 * The merge order works just like array_merge(). Since all parameters are named, duplicates will be overwritten.
		 * The method creates two new parameters bags for the merged Arguments and Options. If you use customized bags
		 * you will need to override the method, as they are not injected for simplicity's sake.
		 *
		 * @param   bool|Definition[]   $mergeArguments     Whether to merge the arguments. Can be omitted (ie. you may
		 *                                                  pass a Definition as the first argument to this method right
		 *                                                  away, in which case the default of "true" will be used).
		 * @return  Definition                              The merged Definition as a new instance.
		 * @throws  \InvalidArgumentException               When one or more of the parameters is not a Definition
		 *                                                  instance (not including the $mergeArguments bool).
		 */

		public function merge($mergeArguments = true)
		{
			// How many Definitions shall be merged?
			$definitions = func_get_args();

			// Whether to merge the arguments. When the first argument is a Definition already, we will use the default
			// of true. Otherwise, strip the first argument out of what we assume to be just an array of Definitions
			// after the func_get_args() call.
			$mergeArguments = ($definitions[0] instanceof Definition) ?: array_shift($definitions);

			// We are not simply going to merge the arrays. We'll let the bags do their work and report any duplicates
			// etc. as necessary.
			$arguments = new bags\definitions\Arguments($this->arguments->all());
			$options   = new bags\definitions\Options($this->options->all());

			foreach($definitions as $definition)
			{
				// We are checking for it only once in the method definition, as we expect at least one Definition,
				// but may just as well get multiple.
				if(!$definition instanceof Definition) throw new \InvalidArgumentException('Only Definition instances can be merged.');

				// Arguments are merged by default but don't necessarily have to be (for instance, help descriptions
				// only display the argument for the command itself, not for the whole command chain).
				if($mergeArguments) $arguments->set($definition->arguments->all());

				// Options are always merged.
				$options->set($definition->options->all());
			}

			// Return the blend in form of a new Definition.
			return new static($arguments, $options);
		}
	}