<?php namespace nyx\framework\definitions;

	/**
	 * Framework Events Definition
	 *
	 * @package     Nyx\Framework\Definitions
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/framework/index.html
	 */

	final class Events
	{
		/**
		 *
		 */

		const REQUEST_RECEIVED  = 'request.received';

		/**
		 *
		 */

		const RESPONSE_PREPARED = 'response.prepared';

		/**
		 *
		 */

		const RESPONSE_SENT     = 'response.sent';
	}
