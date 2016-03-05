<?php namespace nyx\connect\streams;

	// External dependencies
	use nyx\core;

	/**
	 * Stream
	 *
	 * @package     Nyx\Connect\Streams
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/connect/streams.html
	 */

	class Stream implements interfaces\Stream
	{
		/**
		 * @var array       Hash table of readable and writable stream modes for fast lookups.
		 */

		private static $rwh =
		[
			'read' =>
			[
				'r'  => true, 'w+'  => true, 'r+'  => true, 'x+'  => true, 'c+'  => true,
				'rb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true, 'c+b' => true,
				'rt' => true, 'w+t' => true, 'r+t' => true, 'x+t' => true, 'c+t' => true
			],
			'write' =>
			[
				'w'   => true, 'w+'  => true, 'rw'  => true, 'r+'  => true, 'x+' => true, 'c+' => true,
				'w+b' => true, 'r+b' => true, 'x+b' => true, 'c+b' => true,
				'w+t' => true, 'r+t' => true, 'x+t' => true, 'c+t' => true
			]
		];

		/**
		 * @var resource    The resource in use.
		 */

		private $stream;

		/**
		 * @var resource    The stream context resource in use.
		 */

		private $context;

		/**
		 * @var int         The size of the stream's contents in bytes.
		 */

		private $size;

		/**
		 * @var array       Cached data about the stream.
		 */

		private $metadata = [];

		/**
		 * @var bool        Whether the stream is open or not.
		 */

		private $open;

		/**
		 * @var core\Mask   Status/mode mask of the Stream.
		 */

		private $status;

		/**
		 * Constructs a new Stream.
		 *
		 * @param   string|resource     $uri        The URI of the stream resource that should be opened or an already
		 *                                          created stream resource.
		 * @param   string              $mode       The mode in which the stream should be opened.
		 * @param   array               $context    Stream context options. Will be ignored if an already created stream
		 *                                          is passed to the constructor.
		 * @param   int                 $size       The size of the stream in bytes. Should only be passed if it cannot be
		 *                                          obtained by directly analyzing the stream.
		 * @throws  \InvalidArgumentException       When a resource was given but was not a valid stream resource or when
		 *                                          it was to be created but no mode was given.
		 * @throws  \RuntimeException               When a stream was to be created but couldn't be opened.
		 */

		public function __construct($uri, $mode = null, array $context = null, $size = null)
		{
			// Are we dealing with an already existing stream?
			if(is_resource($uri))
			{
				// Ensure the resource is a stream.
				if(get_resource_type($uri) !== 'stream')
				{
					throw new \InvalidArgumentException('A resource was given but it was not a valid stream.');
				}

				$this->stream = $uri;
			}
			// Otherwise we should create our own.
			else
			{
				// We need a mode to work with.
				if(null === $mode or !is_string($mode)) throw new \InvalidArgumentException('Invalid stream mode given.');

				// First let's prepare a stream context if asked to.
				if(null !== $context) $this->context = stream_context_create($context);

				// Open it either with a specific context or leave the default one.
				if(!$this->stream = $this->context ? fopen($uri, $mode, $this->context) : fopen($uri, $mode))
				{
					throw new \RuntimeException("Failed to open a stream [$uri, mode: $mode].");
				}
			}

			$this->size = $size;
			$this->open = true;

			// Prepare the status mask. Might as well give it a value to begin with.
			$this->status = new core\Mask(stream_is_local($this->stream) ? interfaces\Stream::LOCAL : 0);

			$this->refresh();
		}

		/**
		 * {@inheritdoc}
		 */

		public function expose()
		{
			return $this->stream;
		}

		/**
		 * {@inheritdoc}
		 */

		public function read($length)
		{
			if(false === $data = @fread($this->stream, $length))
			{
				throw new exceptions\stream\Read("Unable to read from the stream [{$this->getMetadata('uri')}].");
			}

			return $data;
		}

		/**
		 * {@inheritdoc}
		 */

		public function line($length = null)
		{
			if(false === $data = @fgets($this->stream, $length))
			{
				throw new exceptions\stream\Read("Unable to read from the stream [{$this->getMetadata('uri')}].");
			}

			return $data;
		}

		/**
		 * {@inheritdoc}
		 */

		public function write($string)
		{
			if(false === $bytes = @fwrite($this->stream, $string))
			{
				throw new exceptions\stream\Write("Unable to write to the stream [{$this->getMetadata('uri')}].");
			}

			$this->size += $bytes;

			return $bytes;
		}

		/**
		 * {@inheritdoc}
		 */

		public function seek($offset, $whence = SEEK_SET)
		{
			if(@fseek($this->stream, $offset, $whence) === -1)
			{
				throw new exceptions\stream\Seek("Unable to seek the stream [{$this->getMetadata('uri')}].");
			}

			return true;
		}

		/**
		 * {@inheritdoc}
		 */

		public function rewind()
		{
			if(@rewind($this->stream) === false)
			{
				throw new exceptions\stream\Seek("Unable to rewind the stream [{$this->getMetadata('uri')}].");
			}

			return true;
		}

		/**
		 * {@inheritdoc}
		 */

		public function close()
		{
			if(!$this->open) throw new \LogicException('The stream is already closed.');

			if($ret = fclose($this->stream)) $this->open = false;

			return $ret;
		}

		/**
		 * {@inheritDoc}
		 */

		public function is($status)
		{
			return $this->status->is($status);
		}

		public function isOpen()
		{
			return $this->open;
		}

		/**
		 * {@inheritdoc}
		 */

		public function isConsumed()
		{
			return feof($this->stream);
		}

		/**
		 * {@inheritdoc}
		 */

		public function getPosition()
		{
			return ftell($this->stream);
		}

		/**
		 * {@inheritdoc}
		 */

		public function getMetadata($key = null)
		{
			// Certain data doesn't change in a given stream, so we might just as well return the cached values.
			if($key and $this->metadata)
			{
				switch($key)
				{
					case 'mode':
					case 'stream_type':
					case 'wrapper_type':
					case 'wrapper_data':
					case 'uri':
						return $this->metadata[$key];
				}
			}

			$this->metadata = stream_get_meta_data($this->stream);

			return !$key ? $this->metadata : $this->metadata[$key];
		}

		/**
		 * {@inheritdoc}
		 */

		public function getSize()
		{
			if($this->size !== null) return $this->size;

			// If the stream is a file based stream and local, then check the filesize
			if($this->is(interfaces\Stream::LOCAL) and $this->getMetadata('wrapper') == 'plainfile' and ($uri = $this->getMetadata('uri')) and file_exists($uri))
			{
				return filesize($uri);
			}

			// Only get the size based on the content if the the stream is readable and seekable
			if($this->is(interfaces\Stream::READABLE) and $this->is(interfaces\Stream::SEEKABLE))
			{
				$position = $this->getPosition();
				$this->size = strlen((string) $this);
				$this->seek($position);

				return $this->size;
			}

			return false;
		}

		/**
		 * {@inheritdoc}
		 */

		public function toString()
		{
			if(!$this->is(interfaces\Stream::READABLE) or (!$this->is(interfaces\Stream::SEEKABLE) and $this->isConsumed()))
			{
				return '';
			}

			$position = $this->getPosition();
			$body = stream_get_contents($this->stream, -1, 0);
			$this->seek($position);

			return $body;
		}

		/**
		 * Refreshes the status mask based on the current metadata of the stream.
		 */

		protected function refresh()
		{
			// The call results of metadata() are cached so we can just use the property.
			$this->getMetadata();

			if(isset(static::$rwh['read'][$this->metadata['mode']])) $this->status->set(interfaces\Stream::READABLE);
			if(isset(static::$rwh['write'][$this->metadata['mode']])) $this->status->set(interfaces\Stream::WRITABLE);

			// Those may change, so... besides - fancy syntax, eh chaps?
			$this->status->{($this->metadata['seekable'] ? 'set' : 'remove')}(interfaces\Stream::SEEKABLE);
			$this->status->{($this->metadata['blocked'] ? 'set' : 'remove')}(interfaces\Stream::BLOCKED);
		}

		/**
		 * Ensures the stream gets closed upon object destruction.
		 */

		public function __destruct()
		{
			if(is_resource($this->stream)) fclose($this->stream);
		}

		/**
		 * {@inheritdoc}
		 */

		public function __toString()
		{
			return $this->toString();
		}
	}