<?php namespace nyx\connect\stores\connectors;

	/**
	 * Memcached Connector
	 *
	 * @package     Nyx\Connect\Stores
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/connect/stores.html
	 */

	class Memcached
	{
		/**
		 * Creates a new Memcached connection.
		 *
		 * @param   array               $servers    The servers to connect to. An array of arrays containing at least
		 *                                          2 keys - host, port and an optional weight.
		 * @param   string              $pool       The name of the pool for persistent connections. If given, the
		 *                                          servers defined in the previous parameter will only get added
		 *                                          if the pool does not yet contain at least one connection.
		 * @param   array               $options    Connection options.
		 * @return  \Memcached                      A Memcached instance.
		 * @throws  \RuntimeException               When unable to establish at least one connection to a Memcache
		 *                                          server.
		 */

		public function connect(array $servers, $pool = null, array $options = null)
		{
			$instance = new \Memcached($pool);

			// Default options.
			$defaults = [
				\Memcached::OPT_LIBKETAMA_COMPATIBLE => true,
				\Memcached::OPT_DISTRIBUTION         => \Memcached::DISTRIBUTION_CONSISTENT,
				\Memcached::OPT_NO_BLOCK             => true,
				\Memcached::OPT_TCP_NODELAY          => true,
				\Memcached::OPT_COMPRESSION          => true,
				\Memcached::OPT_CONNECT_TIMEOUT      => 2
			];

			// Prefer IGBinary over PHP's native serializer if it's available.
			if(\Memcached::HAVE_IGBINARY) $defaults[\Memcached::OPT_SERIALIZER] = \Memcached::SERIALIZER_IGBINARY;

			// Merge the defaults with the user defined options if given and set them.
			$instance->setOptions(null !== $options ? $options + $defaults : $defaults);

			// When a pool is defined we are only going to add the servers if they aren't set already.
			if(null === $pool or !count($instance->getServerList()))
			{
				$instance->addServers($servers);
			}

			// Make sure at least one connection got established.
			if(empty($instance->getVersion()))
			{
				throw new \RuntimeException("Could not establish a Memcached connection.");
			}

			return $instance;
		}
	}