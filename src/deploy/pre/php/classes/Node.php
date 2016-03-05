<?php namespace nyx\deploy\pre\php\classes;

	/**
	 * PHP Class Node
	 *
	 * @package     Nyx\Deploy\Pre
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/deploy/pre.html
	 */

	class Node
	{
		/**
		 * @var mixed   The value of the Node.
		 */

		private $value;

		/**
		 * @var Node    The next Node.
		 */

		private $next;

		/**
		 * @var Node    The previous Node.
		 */

		private $previous;

		/**
		 * Constructs a new Node.
		 *
		 * @param   mixed   $value      The value of the Node.
		 * @param   Node    $previous   The previous Node.
		 */

		public function __construct($value = null, Node $previous = null)
		{
			$this->value    = $value;
			$this->previous = $previous;
		}

		/**
		 * Returns the value of the Node.
		 *
		 * @return  mixed
		 */

		public function getValue()
		{
			return $this->value;
		}

		/**
		 * Sets the value of the Node.
		 *
		 * @param   mixed   $value  The value to set.
		 * @return  $this
		 */

		public function setValue($value)
		{
			$this->value = $value;

			return $this;
		}

		/**
		 * Returns the next Node.
		 *
		 * @return  Node
		 */

		public function getNext()
		{
			return $this->next;
		}

		/**
		 * Sets the next Node.
		 *
		 * @param   Node    $node   The Node to set.
		 * @return  $this
		 */

		public function setNext(Node $node)
		{
			$this->next = $node;

			return $this;
		}

		/**
		 * Returns the next Node.
		 *
		 * @return  Node
		 */

		public function getPrevious()
		{
			return $this->previous;
		}

		/**
		 * Sets the previous Node.
		 *
		 * @param   Node    $node   The Node to set.
		 * @return  $this
		 */

		public function setPrevious(Node $node)
		{
			$this->previous = $node;

			return $this;
		}
	}