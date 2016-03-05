<?php namespace nyx\connect\mail;

	// External dependencies
	use nyx\core;

	/**
	 * Mail Message
	 *
	 * Generic mail message based on Swift Mailer.
	 *
	 * @package     Nyx\Connect\Mail
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/connect/mail.html
	 */

	class Message extends \Swift_Message implements interfaces\Message, core\interfaces\Serializable
	{
		/**
		 * The traits of a Message instance.
		 */

		use core\traits\Serializable;

		/**
		 * {@inheritDoc}
		 */

		public function attach($file, $name = null, $mime = null)
		{
			$attachment = \Swift_Attachment::fromPath($file, $mime);

			if(null !== $name) $attachment->setFilename($name);

			return parent::attach($attachment);
		}

		/**
		 * {@inheritDoc}
		 */

		public function attachData($data, $name, $mime = null)
		{
			return parent::attach(\Swift_Attachment::newInstance($data, $name, $mime));
		}

		/**
		 * {@inheritDoc}
		 */

		public function embed($file)
		{
			return parent::embed(\Swift_Image::fromPath($file));
		}

		/**
		 * {@inheritDoc}
		 */

		public function embedData($data, $name, $contentType = null)
		{
			return parent::embed(\Swift_Image::newInstance($data, $name, $contentType));
		}

		/**
		 * {@inheritDoc}
		 */

		public function unserialize($data)
		{
			$data = unserialize($data);

			$this->setFrom($data['from']);
			$this->setTo($data['to']);
			$this->setCc($data['cc']);
			$this->setBcc($data['bcc']);
			$this->setSubject($data['subject']);
			$this->setBody($data['body']);
			$this->setChildren($data['children']);
		}

		/**
		 * {@inheritDoc}
		 */

		public function toArray()
		{
			return
			[
				'from'     => $this->getFrom(),
				'to'       => $this->getTo(),
				'cc'       => $this->getCc(),
				'bcc'      => $this->getBcc(),
				'subject'  => $this->getSubject(),
				'body'     => $this->getBody(),
				'children' => $this->getChildren()
			];
		}
	}