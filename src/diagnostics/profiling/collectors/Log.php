<?php namespace nyx\diagnostics\profiling\collectors;

	// Internal dependencies
	use nyx\diagnostics\debug;
	use nyx\diagnostics\profiling;

	/**
	 * Log Data Collector
	 *
	 * @package     Nyx\Diagnostics\Profiling
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/profiling/index.html
	 */

	class Log extends profiling\Collector
	{
		/**
		 * @var profiling\interfaces\Logger     A Logger instance usable for profiling.
		 */

		private $logger;

		/**
		 * {@inheritDoc}
		 *
		 * @param   profiling\interfaces\Logger     A Logger instance usable for profiling.
		 */

		public function __construct(profiling\interfaces\Logger $logger = null, $name = 'log')
		{
			$this->logger = $logger;

			parent::__construct($name);
		}

		/**
		 * {@inheritDoc}
		 */

		public function collect(profiling\Context $context = null)
		{
			$this->getLogs();
			$this->countErrors();
			$this->countDeprecations();
		}

		/**
		 * Returns an array of logs.
		 *
		 * @return  array
		 */

		public function getLogs()
		{
			// If the logs aren't available yet, we'll have to grab them.
			if(!isset($this->data['logs']))
			{
				// Fetch the logs from the logger and sanitize the log contexts.
				foreach($this->data['logs'] = $this->logger->getLogs() as $i => $log)
				{
					$this->data['logs'][$i]['context'] = $this->sanitizeContext($log['context']);
				}
			}

			return $this->data['logs'];
		}

		/**
		 * Returns the number of logs classified as errors.
		 *
		 * @return  int
		 */

		public function countErrors()
		{
			return isset($this->data['error_count'])
				? $this->data['error_count']
				: $this->data['error_count'] = $this->logger->countErrors();
		}

		/**
		 * Returns the number of logs classified as deprecation notices.
		 *
		 * @return  int
		 */

		public function countDeprecations()
		{
			return isset($this->data['deprecation_count'])
				? $this->data['deprecation_count']
				: $this->data['deprecation_count'] = $this->computeDeprecationCount();
		}

		/**
		 * Removes resources and objects from a log's context information leaving only the string representations of
		 * their types instead.
		 *
		 * @param   mixed   $context
		 * @return  mixed
		 */

		protected function sanitizeContext($context)
		{
			// Traverse the context recursively if it's an array.
			if(is_array($context))
			{
				foreach($context as $key => $value) $context[$key] = $this->sanitizeContext($value);

				return $context;
			}

			// Remove resources and objects from the context. Return their types instead.
			if(is_resource($context)) return 'Resource('.get_resource_type($context).')';
			if(is_object($context)) return 'Object('.get_class($context).')';

			return $context;
		}

		/**
		 * Calculates the number of deprecation notices contained in the logs based on the type of the log defined in
		 * its context.
		 *
		 * @return  int
		 */

		protected function computeDeprecationCount()
		{
			$count = 0;

			foreach($this->data['logs'] as $log)
			{
				if(isset($log['context']['type']) and $log['context']['type'] === diagnostics\handlers\Error::DEPRECATION)
				{
					$count++;
				}
			}

			return $count;
		}
	}