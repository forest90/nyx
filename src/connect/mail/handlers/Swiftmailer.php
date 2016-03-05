<?php namespace nyx\connect\mail\handlers;

	// Internal dependencies
	use nyx\connect\mail;

	/**
	 * Swift Mailer Handler
	 *
	 * @package     Nyx\Connect\Mail
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/connect/mail.html
	 */

	class Swiftmailer implements mail\interfaces\Handler
	{
		/**
		 * @var \Swift_Mailer   The underlying Swift Mailer instance.
		 */

		private $swift;

		/**
		 * Constructs a new Swift Mailer Handler instance.
		 *
		 * @param   \Swift_Mailer   $swift  The actual Swift Mailer instance to wrap.
		 */

		public function __construct(\Swift_Mailer $swift)
		{
			$this->swift = $swift;
		}

		/**
		 * {@inheritDoc}
		 */

		public function send(mail\interfaces\Message $message)
		{
			return $this->swift->send($message);
		}

		/**
		 * {@inheritDoc}
		 */

		public function createMessage()
		{
			return new mail\Message;
		}
	}