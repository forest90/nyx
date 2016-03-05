<?php namespace nyx\diagnostics\profiling;

	/**
	 * Profiler
	 *
	 * @package     Nyx\Diagnostics\Profiling
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/profiling/index.html
	 */

	class Profiler
	{
		/**
		 * The traits of a Profiler instance.
		 */

		use traits\HasCollectors;

		/**
		 * @var interfaces\Storage    The Storage in use by this Profiler.
		 */

		private $storage;

		/**
		 * Constructs a new Profiler instance.
		 *
		 * @param   interfaces\Storage  $storage        The Storage to use for saving/loading Profiles.
		 * @param   Collectors          $collectors     A Collectors collection.
		 */

		public function __construct(interfaces\Storage $storage, Collectors $collectors = null)
		{
			$this->storage = $storage;

			// Let's see if we were given Collectors we can work with.
			if(null !== $collectors) $this->setCollectors($collectors);
		}

		/**
		 * Returns the Storage attached to this Profiler.
		 *
		 * @return  interfaces\Storage
		 */

		public function getStorage()
		{
			return $this->storage;
		}

		/**
		 * Imports a serialized Profile into the Profiler's storage and returns the Profile.
		 *
		 * @param   string          $data       A data string as exported by the Profile::export() method.
		 * @return  Profile|bool                The imported Profile instance or false if it could not be saved.
		 * @throws  \InvalidArgumentException   When the data string cannot be properly unserialized or the process
		 *                                      did not yield a valid Profile instance.
		 */

		public function import($data)
		{
			// Protect ourselves against unsupported data strings and make sure we end up with a Profile instance.
			if(!$profile = unserialize(base64_decode($data)) or !$profile instanceof Profile)
			{
				throw new \InvalidArgumentException('The given data string cannot be unserialized or did not yield a valid Profile instance.');
			}

			// Make sure the Profile got properly saved.
			if(false === $profile->save($this->storage)) return false;

			return $profile;
		}

		/**
		 * Loads the Profile for the given token.
		 *
		 * @param   string          $token  The token to look for.
		 * @return  Profile|bool            {@see interfaces\profiling\Storage::read()}
		 */

		public function load($token)
		{
			return $this->storage->read($token);
		}

		/**
		 * Purges all data from the Storage attached to this Profiler.
		 */

		public function purge()
		{
			$this->storage->purge();
		}

		/**
		 * Collects data for the given Context.
		 *
		 * @param   Context           $context  The Context for which data shall be collected.
		 * @return  Profile|null                Either a Profile instance or null if the profiler is disabled.
		 */

		public function collect(Context $context = null)
		{
			// We need a clean Profile and a new Collectors collection (to store the snapshots ready to be serialized)
			// which we will assign to the Profile after we are done collecting *all the data*.
			$profile    = new Profile;
			$collectors = new Collectors;

			// Loop through all Collectors, make them do their work and flatten them inside the new collection.
			/* @var Collector $collector */
			foreach($this->getCollectors() as $collector)
			{
				$collector->collect($context);

				// Forces the Collector to loose its object dependencies.
				$collectors->set($collector->snapshot());
			}

			// Finally assign the collection to the Profile and return the Profile (setCollectors is fluent).
			return $profile->setCollectors($collectors);
		}
	}
