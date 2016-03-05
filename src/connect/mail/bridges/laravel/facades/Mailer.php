<?php namespace nyx\connect\mail\bridges\laravel\facades;

	// External dependencies
	use Illuminate;

	/**
	 * Mail Service Facade
	 *
	 * @package     Nyx\Connect\Mail\Bridges
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/connect/mail.html
	 */

	class Mailer extends Illuminate\Support\Facades\Facade
	{
		/**
		 * {@inheritDoc}
		 */

		protected static function getFacadeAccessor()
		{
			return 'mailer';
		}
	}