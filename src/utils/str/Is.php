<?php namespace nyx\utils;

	// External dependencies
	use nyx\core;

	/**
	 * Is
	 *
	 * Helper methods for detecting the format of a string.
	 *
	 * Requires:
	 * - Extension: libxml (detecting XML strings)
	 *
	 * @package     Nyx\Utils\Strings
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/utils/strings.html
	 */

	class Is
	{
		/**
		 * The traits of the Is class.
		 */

		use core\traits\StaticallyExtendable;

		/**
		 * Determines whether the given string is a valid email address.
		 *
		 * @param   string  $str    The string to check.
		 * @return  bool
		 */

		public static function email($str)
		{
			return filter_var($str, FILTER_VALIDATE_EMAIL) !== false;
		}

		/**
		 * Determines whether the given string is a HTML string.
		 *
		 * @param   string  $str    The string to check.
		 * @return  bool
		 */

		public static function html($str)
		{
			return strlen(strip_tags($str)) < strlen($str);
		}

		/**
		 * Determines whether the given string is a valid IP address.
		 *
		 * @param   string  $str    The string to check.
		 * @return  bool
		 */

		public static function ip($str)
		{
			return filter_var($str, FILTER_VALIDATE_IP) !== false;
		}

		/**
		 * Determines whether the given string is JSON-encoded.
		 *
		 * @param   string  $str    The string to check.
		 * @return  bool
		 */

		public static function json($str)
		{
			json_decode($str);

			return json_last_error() === JSON_ERROR_NONE;
		}

		/**
		 * Determines whether the given string is a serialized representation of a value.
		 *
		 * @param   string  $str    The string to check.
		 * @return  bool
		 */

		public static function serialized($str)
		{
			$value = @unserialize($str);

			return !($value === false and $str !== 'b:0;');
		}

		/**
		 * Determines whether the given string is a valid URL address.
		 *
		 * @param   string  $str    The string to check.
		 * @return  bool
		 */

		public static function url($str)
		{
			return filter_var($str, FILTER_VALIDATE_URL) !== false;
		}

		/**
		 * Determines whether the given string is in a valid XML format.
		 *
		 * @param   string  $str    The string to check.
		 * @return  bool
		 */

		public static function xml($str)
		{
			$initialSetting = libxml_use_internal_errors();

			libxml_use_internal_errors(true);
			$result = simplexml_load_string($str) !== false;
			libxml_use_internal_errors($initialSetting);

			return $result;
		}
	}