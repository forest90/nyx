<?php namespace nyx\diagnostics\profiling;

	/**
	 * Profile
	 *
	 * @package     Nyx\Diagnostics\Profiling
	 * @version     0.0.2
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/diagnostics/profiling/index.html
	 */

	class Profile
	{
		/**
		 * The traits of a Profile instance.
		 */

		use traits\HasCollectors;

		/**
		 * @var string      The Profile's read-only token (identifier).
		 */

		private $token;

		/**
		 * @var int         The creation timestamp of this Profile.
		 */

		private $time;

		/**
		 * @var Profile     The parent Profile of this Profile. if applicable.
		 */

		private $parent;

		/**
		 * @var Profile[]   The children Profiles of this Profile, if applicable.
		 */

		private $children;

		/**
		 * Constructs a new Profile instance.
		 *
		 * @param   string  $token  The Profile's token (identifier) or null to generate a random 6-char long token.
		 * @param   int     $time   The creation timestamp of this Profile.
		 */

		public function __construct($token = null, $time = null)
		{
			$this->token = $token ?: substr(sha1(uniqid(mt_rand(), true)), 0, 6);
			$this->time  = (int) $time ?: time();

			$this->children = [];
		}

		/**
		 * Returns the Profile's token (identifier).
		 *
		 * @return  string
		 */

		public function getToken()
		{
			return $this->token;
		}

		/**
		 * Returns the creation timestamp of this Profile.
		 *
		 * @return  int
		 */

		public function getTime()
		{
			return $this->time;
		}

		/**
		 * Returns the parent Profile of this Profile.
		 *
		 * @return  Profile     The parent Profile of this Profile if applicable, null otherwise.
		 */

		public function getParent()
		{
			return $this->parent;
		}

		/**
		 * Returns the parent Profile of this Profile.
		 *
		 * @param   Profile     $parent     The parent Profile of this Profile.
		 */

		public function setParent(Profile $parent)
		{
			$this->parent = $parent;
		}

		/**
		 * Returns the token of the parent Profile.
		 *
		 * @return  string  The token of the parent Profile if applicable, null otherwise.
		 */

		public function getParentToken()
		{
			return $this->parent ? $this->parent->getToken() : null;
		}

		/**
		 * Returns the children Profiles of this Profile.
		 *
		 * @return  Profile[]   An array of children Profiles of this Profile if applicable, null otherwise.
		 */

		public function getChildren()
		{
			return $this->children;
		}

		/**
		 * Sets the children Profiles of this Profile.
		 *
		 * @param   Profile[]   $children       An array of Profiles to add as children of this Profile.
		 */

		public function setChildren(array $children)
		{
			// Make sure we start with a tabula rasa.
			$this->children = [];

			foreach($children as $child) $this->addChild($child);
		}

		/**
		 * Adds a single child Profile to this Profile.
		 *
		 * @param   Profile $child  The Profile to add as child of this Profile.
		 */

		public function addChild(Profile $child)
		{
			$this->children[] = $child;

			$child->setParent($this);
		}

		/**
		 * Serializes this Profile, encodes it in base64 and returns it. The returned string is in a format which
		 * can be easily imported into a Profiler instance to re-create the Profile (and save it in the Profiler's
		 * storage at the same time).
		 *
		 * @return  string
		 */

		public function export()
		{
			return base64_encode(serialize($this));
		}

		/**
		 * Saves the Profile in the given Storage.
		 *
		 * @param   interfaces\Storage  $storage    The Storage this Profile should be saved in.
		 * @return  bool                            {@see interfaces\profiling\Storage::write()}
		 */

		public function save(interfaces\Storage $storage)
		{
			return $storage->write($this);
		}

		/**
		 * Returns the names of the properties that should be serialized.
		 *
		 * @return  array
		 */

		public function __sleep()
		{
			return ['token', 'collectors', 'time', 'children', 'parents'];
		}
	}