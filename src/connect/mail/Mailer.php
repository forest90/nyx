<?php namespace nyx\connect\mail;

	/**
	 * Mail Service
	 *
	 * @package     Nyx\Connect\Mail
	 * @version     0.0.5
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/connect/mail.html
	 */

	abstract class Mailer
	{
		/**
		 * @var array   The global from address and name.
		 */

		private $from;

		/**
		 * Sets the global from address and name.
		 *
		 * @param  string  $address
		 * @param  string  $name
		 */

		public function alwaysFrom($address, $name = null)
		{
			$this->from = ['address' => $address, 'name' => $name];
		}

		/**
		 * Send a new message when only a plain part.
		 *
		 * @param   string|array        $view
		 * @param   array               $data
		 * @param   callable|string     $callback
		 * @return  int
		 */

		public function plain($view, array $data, $callback)
		{
			return $this->send(['text' => $view], $data, $callback);
		}

		/**
		 * Send a new message using a view.
		 *
		 * @param   string|array        $view
		 * @param   array               $data
		 * @param   callable|string     $callback
		 * @return  int
		 */

		public function send($view, array $data, callable $callback)
		{
			$handler = $this->getHandler();
			$view    = $this->parseView($view);
			$message = $data['message'] = $handler->createMessage();

			// Autopopulate the from address from our global config. Can be overridden by the builder later on
			// but eases the process of sending various mails from a base address perspective.
			if(isset($this->from['address']))
			{
				$message->setFrom($this->from['address'], $this->from['name']);
			}

			call_user_func($callback, $message);

			// Once we have retrieved the view content for the e-mail we will set the body
			// of this message using the HTML type, which will provide a simple wrapper
			// to creating view based emails that are able to receive arrays of data.
			$this->addContent($message, $view[0], $view[1], $data);

			return $handler->send($message);
		}

		/**
		 * Adds the rendered content to a given message.
		 *
		 * @param   interfaces\Message  $message    The Message the content should be added to.
		 * @param   string              $view       The name of the HTML view.
		 * @param   string              $plain      The name of the plaintext view.
		 * @param   array               $data       The data to pass to the view(s).
		 */

		protected function addContent(interfaces\Message $message, $view, $plain, $data)
		{
			if(isset($view))  $message->setBody($this->renderView($view, $data),  'text/html');
			if(isset($plain)) $message->addPart($this->renderView($plain, $data), 'text/plain');
		}

		/**
		 * Parses the given view name or array.
		 *
		 * @param   string|array  $view
		 * @return  array
		 * @throws  \InvalidArgumentException   When an invalid view has been given.
		 */

		protected function parseView($view)
		{
			if(is_string($view)) return [$view, null];

			if(is_array($view))
			{
				// If the given view is an array with numeric keys, we will just assume that
				// both a "pretty" and "plain" view were provided, so we will return this
				// array as is, since must should contain both views with numeric keys.
				if(isset($view[0])) return $view;

				// If the view is an array, but doesn't contain numeric keys, we will assume
				// the the views are being explicitly specified and will extract them via
				// named keys instead, allowing the developers to use one or the other.
				return [array_get($view, 'html'), array_get($view, 'text')];
			}

			throw new \InvalidArgumentException("Invalid view.");
		}

		/**
		 * Renders the given view.
		 *
		 * @param   string  $view           The name of the View.
		 * @param   array   $data           The data to pass to the View.
		 * @return  mixed                   The rendered View.
		 */

		abstract protected function renderView($view, $data);

		/**
		 * Returns the Mail Handler in use.
		 *
		 * @return  interfaces\Handler
		 */

		abstract protected function getHandler();
	}