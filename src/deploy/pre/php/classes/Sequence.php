<?php namespace nyx\deploy\pre\php\classes;

	/**
	 * PHP Class Sequence
	 *
	 * @package     Nyx\Deploy\Pre
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/deploy/pre.html
	 */

	class Sequence
	{
		/**
		 * @var Node    The root Node in the sequence.
		 */

		private $root;

		/**
		 * @var Node    The currently processed Node.
		 */

		private $current;

		/**
		 * Constructs a new Sequence.
		 */

		public function __construct()
		{
			$this->current = $this->root = new Node(null);
		}

		/**
		 * Pushes a value into the sequence at its end.
		 *
		 * @param   mixed   $value  The value to push into the sequence.
		 */

		public function push($value)
		{
			if(null !== $this->current->getValue())
			{
				$this->current->setValue($value);
			}
			else
			{
				$temp = $this->current;

				$this->current = new Node($value, $temp->getPrevious());
				$this->current->setNext($temp);
				$temp->setPrevious($this->current);

				if($temp === $this->root)
				{
					$this->root = $this->current;
				}
				else
				{
					$this->current->getPrevious()->setNext($this->current);
				}
			}
		}

		/**
		 * Moves the internal pointer of the Sequence to the next Node.
		 */

		public function next()
		{
			if(null !== $next = $this->current->getNext())
			{
				$this->current = $next;
			}
			else
			{
				$next = new Node(null, $this->current);

				$this->current->setNext($next);
				$this->current = $next;
			}
		}

		/**
		 * Traverses the Sequence and returns the values of the Nodes in the order they are present in the Sequence.
		 *
		 * @return  array
		 */

		public function getValues()
		{
			$classes = [];
			$current = $this->root;

			while($current and $value = $current->getValue())
			{
				$classes[] = $value;
				$current   = $current->getNext();
			}

			return array_filter($classes);
		}
	}