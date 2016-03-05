<?php namespace nyx\connect\mail\handlers\mailgun;

	// Internal dependencies
	use nyx\connect\mail;

	/**
	 * Mailgun Message
	 *
	 * If more than 3 tags get set, only the first 3 tags will be used by Mailgun.
	 *
	 * @package     Nyx\Connect\Mail
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/connect/mail.html
	 */

	class Message extends mail\Message
	{
		/**
		 * @var array   The tags of the Message.
		 */

		private $tags = [];

		/**
		 * @var bool    Whether this Message should be tracked or not.
		 */

		private $track;

		/**
		 * @var bool    Whether clicks on links in this Message should be tracked or not.
		 */

		private $trackClicks;

		/**
		 * @var bool    Whether the opening of this Message should be tracked or not.
		 */

		private $trackOpens;

		/**
		 * @var string  The campaign id of the message.
		 */

		private $campaign;

		/**
		 * @var array   The custom data of the Message.
		 */

		private $data = [];

		/**
		 * Returns the campaign id of the Message.
		 *
		 * @return  string
		 */

		public function getCampaign()
		{
			return $this->campaign;
		}

		/**
		 * Sets the campaign id of the Message.
		 *
		 * @param   string  $id     The campaign id to set.
		 * @return  $this
		 */

		public function setCampaign($id)
		{
			$this->campaign = $id;

			return $this;
		}

		/**
		 * Returns the custom data of the Message.
		 *
		 * @return  string
		 */

		public function getData()
		{
			return $this->data;
		}

		/**
		 * Sets custom data on the Message.
		 *
		 * @param   array   $data   The data to set.
		 * @return  $this
		 */

		public function setData(array $data)
		{
			$this->data = $data;

			return $this;
		}

		/**
		 * Adds custom data to the Message. Existing keys will be overwritten.
		 *
		 * @param   string  $key    The key the data should be set as.
		 * @param   mixed   $value  The data to add.
		 * @return  $this
		 */

		public function addData($key, $value)
		{
			$this->data[$key] = $value;

			return $this;
		}

		/**
		 * Sets the tags of the Message.
		 *
		 * @param   array   $tags   The tags to set.
		 * @return  $this
		 */

		public function setTags(array $tags)
		{
			$this->tags = $tags;

			return $this;
		}

		/**
		 * Tags the Message.
		 *
		 * @param   string  $tag    The tag to add.
		 * @return  $this
		 */

		public function tag($tag)
		{
			$this->tags[] = $tag;

			return $this;
		}

		/**
		 * Sets whether this Message should be tracked or not. Takes precedence over domain settings and when set to
		 * false will disable both click and opening tracking.
		 *
		 * @param   bool    $bool   True to enable tracking, false to disable it.
		 * @return  $this
		 */

		public function setTracking($bool = null)
		{
			$this->track = (bool) $bool;

			return $this;
		}

		/**
		 * Sets whether clicks on links in this Message should be tracked or not. Takes precedence over domain settings.
		 *
		 * @param   bool    $bool   True to enable click tracking, false to disable it.
		 * @return  $this
		 */

		public function setClickTracking($bool = null)
		{
			$this->trackClicks = (bool) $bool;

			return $this;
		}

		/**
		 * Sets whether the opening of this Message should be tracked or not. Takes precedence over domain settings.
		 *
		 * @param   bool    $bool   True to enable opening tracking, false to disable it.
		 * @return  $this
		 */

		public function setOpeningTracking($bool = null)
		{
			$this->trackOpens = (bool) $bool;

			return $this;
		}
	}