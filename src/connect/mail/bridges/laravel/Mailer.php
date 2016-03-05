<?php namespace nyx\connect\mail\bridges\laravel;

	// External dependencies
	use Illuminate;

	// Internal dependencies
	use nyx\connect\mail;

	/**
	 * Laravel Mail Service
	 *
	 * @package     Nyx\Connect\Mail\Bridges
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/connect/mail.html
	 */

	class Mailer extends mail\Mailer
	{
		/**
		 * @var Illuminate\View\Environment         The View Environment in use.
		 */

		private $views;

		/**
		 * @var \Illuminate\Container\Container     The Dependency Injection Container in use.
		 */

		private $container;

		/**
		 * Create a new Mailer instance.
		 *
		 * @param   Illuminate\View\Environment     $view       The View Environment to use.
		 * @param   Illuminate\Container\Container  $container  The Dependency Injection Container to use.
		 */

		public function __construct(Illuminate\View\Environment $view, Illuminate\Container\Container $container)
		{
			$this->views = $view;
			$this->container = $container;
		}

		/**
		 * {@inheritDoc}
		 */

		protected function renderView($view, $data)
		{
			return $this->views->make($view, $data)->render();
		}

		/**
		 * {@inheritDoc}
		 */

		protected function getHandler()
		{
			return $this->container['mail.handler'];
		}
	}