<?php namespace nyx\connect\mail\handlers;

	// Internal dependencies
	use nyx\connect\mail\interfaces;

	/**
	 * Mailgun Handler
	 *
	 * @package     Nyx\Connect\Mail
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/connect/mail.html
	 */

	class Mailgun implements interfaces\Handler
	{
		/**
		 * @var string  The default domain to send messages from.
		 */

		private $domain;

		/**
		 * @var string  The API key.
		 */

		private $key;

		/**
		 * @var \Mailgun\Mailgun    The underlying Mailgun SDK instance.
		 */

		private $mailgun;

		/**
		 * Creates a new Mailgun Handler instance.
		 *
		 * @param   string  $key        The API key.
		 * @param   string  $domain     The default domain to send messages from.
		 */

		public function __construct($key, $domain = null)
		{
			$this->key    = $key;
			$this->domain = $domain;
		}

		/**
		 * {@inheritDoc}
		 *
		 * @param   string  $domain     Optional domain to send the message from (if different than the default set
		 *                              in the instance; does not override the default).
		 *
		 * @todo    Attachments.
		 */

		public function send(interfaces\Message $message, $domain = null)
		{
			/* @var  mailgun\Message $message */
			return $this->getMailgun()->sendMessage($domain ?: $this->domain, $message->toArray());
		}

		/**
		 * {@inheritDoc}
		 *
		 * @return  mailgun\Message
		 */

		public function createMessage()
		{
			return new mailgun\Message();
		}

		/**
		 * Returns a lazily-instantiated Mailgun SDK instance.
		 *
		 * @return  \Mailgun\Mailgun
		 */

		protected function getMailgun()
		{
			return $this->mailgun ?: $this->mailgun = new \Mailgun\Mailgun($this->key);
		}
	}