<?php namespace nyx\connect\mail\interfaces;

	/**
	 * Mail Message Interface
	 *
	 * @package     Nyx\Connect\Mail
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/connect/mail.html
	 */

	interface Message extends \Swift_Mime_Message
	{
		/**
		 * Returns the priority level of the message.
		 *
		 * @return  int
		 */

		public function getPriority();

		/**
		 * Sets the priority level of the message.
		 *
		 * @param   int     $level
		 * @return  $this
		 */

		public function setPriority($level);

		/**
		 * Adds a message part to the message.
		 *
		 * @param   string  $content
		 * @param   string  $mime
		 * @return  $this
		 */

		public function addPart($content, $mime = 'text/plain');

		/**
		 * Attaches a file to the message.
		 *
		 * @param   string  $file   The path of the file to attach.
		 * @param   string  $name   The name the file should be attached as.
		 * @param   string  $mime   The MIME content type of the attachment.
		 * @return  $this
		 */

		public function attach($file, $name = null, $mime = null);

		/**
		 * Attaches in-memory data to the message.
		 *
		 * @param   string  $data   The data to attach.
		 * @param   string  $name   The name the file should be attached as.
		 * @param   string  $mime   The MIME content type of the attachment.
		 * @return  $this
		 */

		public function attachData($data, $name, $mime = null);

		/**
		 * Embeds a file in the message and returns the CID of the file.
		 *
		 * @param   string  $file
		 * @return  string
		 */

		public function embed($file);

		/**
		 * Embeds in-memory data in the message and returns the CID of the data.
		 *
		 * @param  string  $data
		 * @param  string  $name
		 * @param  string  $contentType
		 * @return string
		 */

		public function embedData($data, $name, $contentType = null);
	}