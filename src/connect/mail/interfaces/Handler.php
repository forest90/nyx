<?php namespace nyx\connect\mail\interfaces;

	/**
	 * Mail Handler Interface
	 *
	 * @package     Nyx\Connect\Mail
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/connect/mail.html
	 */

	interface Handler
	{
		/**
		 * Sends the given Message.
		 *
		 * @param   Message     $message    The message to send.
		 * @return  $this
		 */

		public function send(Message $message);

		/**
		 * Creates a new Message instance.
		 *
		 * @return  Message
		 */

		public function createMessage();
	}