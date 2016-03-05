<?php namespace nyx\utils;

	// External dependencies
	use nyx\core\promises;
	use nyx\core;

	/**
	 * When
	 *
	 * Utilities related to the nyx\core\promises subcomponent.
	 *
	 * @package     Nyx\Utils\Promises
	 * @version     0.0.1
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/utils/when.html
	 */

	class When
	{
		/**
		 * The traits of the When class.
		 */

		use core\traits\StaticallyExtendable;

		/**
		 * Resolves the given value.
		 *
		 * @param   mixed   $value                  Either already a Promise, which will be returned straight away,
		 *                                          or any other value which will be wrapped in a Fulfilled Promise
		 *                                          instance and returned.
		 * @return  promises\interfaces\Promise     The resolved Promise.
		 */

		public static function resolve($value)
		{
			return $value instanceof promises\interfaces\Promise ? $value : new promises\types\Fulfilled($value);
		}

		/**
		 * Creates a Rejected Promise for the given reason or ensures the given Promise (when $reason is a Promise)
		 * gets rejected with the given reason.
		 *
		 * @param   mixed   $reason                 Either already a Promise, which will be forcefully rejected with
		 *                                          the given reason regardless of how the actual resolution goes, or
		 *                                          any other value which will be wrapped in a Rejected Promise
		 *                                          instance and used as the reason.
		 * @return  promises\interfaces\Promise     A Promise that the given value will be/is rejected.
		 */

		public static function reject($reason)
		{
			if($reason instanceof promises\interfaces\Promise)
			{
				return $reason->then(function ($reason) {
					return new promises\types\Rejected($reason);
				});
			}

			return new promises\types\Rejected($reason);
		}

		/**
		 * Returns a Promise that will resolve only once all the items in the $values array (or Promise of an array)
		 * have resolved. The resolution value of the returned Promise will be an array containing the resolution
		 * values of each of the items in $values.
		 *
		 * If any of the Promises gets rejected, the returned Promise will be rejected with the rejection reason of
		 * the first Promise that was rejected.
		 *
		 * @param   array       $values             An array of the values/Promises to resolve or a Promise of an array.
		 * @param   callable    $fulfilled          {@see promises\interfaces\Promise::then()}
		 * @param   callable    $error              {@see promises\interfaces\Promise::then()}
		 * @param   callable    $progress           {@see promises\interfaces\Promise::then()}
		 * @return  promises\interfaces\Promise
		 */

		public static function all($values, callable $fulfilled = null, callable $error = null, callable $progress = null)
		{
			return static::map($values, function ($value) {

				return $value;

			})->then($fulfilled, $error, $progress);
		}

		/**
		 * Returns a Promise that will resolve when any of the items in the $values array (or Promise of an array)
		 * have resolved. The resolution value of the returned Promise will be the fulfillment value of the
		 * first resolved Promise.
		 *
		 * The returned promise will only reject if all items in array are rejected. The rejection value will be an
		 * array of all rejection reasons.
		 *
		 * @param   array       $values             An array of the values/Promises to resolve or a Promise of an array.
		 * @param   callable    $fulfilled          {@see promises\interfaces\Promise::then()}
		 * @param   callable    $error              {@see promises\interfaces\Promise::then()}
		 * @param   callable    $progress           {@see promises\interfaces\Promise::then()}
		 * @return  promises\interfaces\Promise
		 */

		public static function any($values, callable $fulfilled = null, callable $error = null, callable $progress = null)
		{
			$unwrapSingleResult = function($val) use ($fulfilled) {

				$val = array_shift($val);

				return $fulfilled ? $fulfilled($val) : $val;
			};

			return static::some($values, 1, $unwrapSingleResult, $error, $progress);
		}

		/**
		 * Returns a Promise that will resolve when $howMany of the items in the $values array (or Promise of an array)
		 * have resolved. The returned Promise will reject if it becomes impossible for $howMany items to resolve,
		 * ie. when when (count($values) - $howMany) + 1 items reject. The resolution value of the returned Promise
		 * will be an array of howMany winning Promise fulfillment values. The rejection value will be an array
		 * of (count($values) - $howMany) + 1 rejection reasons.
		 *
		 * @param   array       $values             An array of the values/Promises to resolve or a Promise of an array.
		 * @param   int         $howMany            The number of Promises from $values that must fulfill.
		 * @param   callable    $fulfilled          {@see promises\interfaces\Promise::then()}
		 * @param   callable    $error              {@see promises\interfaces\Promise::then()}
		 * @param   callable    $progress           {@see promises\interfaces\Promise::then()}
		 * @return  promises\interfaces\Promise
		 */

		public static function some($values, $howMany, $fulfilled = null, $error = null, $progress = null)
		{
			return static::resolve($values)->then(function($array) use ($howMany, $fulfilled, $error, $progress) {

				if(!is_array($array)) $array = [];

				$count     = count($array);
				$toResolve = max(0, min($howMany, $count));
				$values    = [];
				$deferred  = new promises\Deferred();

				if(!$toResolve)
				{
					$deferred->resolve($values);
				}
				else
				{
					$toReject = ($count - $toResolve) + 1;
					$reasons  = [];
					$progress = [$deferred, 'progress'];

					$fulfillOne = function($value, $i) use (&$values, &$toResolve, $deferred) {

						$values[$i] = $value;

						if(0 === --$toResolve)
						{
							$deferred->resolve($values);

							return true;
						}
					};

					$rejectOne = function($reason, $i) use (&$reasons, &$toReject, $deferred) {

						$reasons[$i] = $reason;

						if(0 === --$toReject)
						{
							$deferred->reject($reasons);

							return true;
						}
					};

					foreach($array as $i => $value)
					{
						$fulfiller = function($value) use ($i, &$fulfillOne, &$rejectOne) {

							if(true === $fulfillOne($value, $i)) $fulfillOne = $rejectOne = function () {};
						};

						$rejecter = function($value) use ($i, &$fulfillOne, &$rejectOne) {

							if(true === $rejectOne($value, $i)) $fulfillOne = $rejectOne = function () {};
						};

						static::resolve($value)->then($fulfiller, $rejecter, $progress);
					}
				}

				return $deferred->then($fulfilled, $error, $progress);
			});
		}

		/**
		 * Executes a callback over each of the items in the array of $values (or Promise of an array) given, where
		 * $values may contain Promises which will get resolved/rejected, and returns a Promise that the given
		 * $values are/will be resolved.
		 *
		 * If any of the Promises gets rejected, the returned Promise will be rejected with the rejection reason of
		 * the first Promise that was rejected.
		 *
		 * @param   array       $values             An array of the values/Promises to resolve or a Promise of an array.
		 * @param   callable    $callback           The callable to execute over each item.
		 * @return  promises\interfaces\Promise
		 */

		public static function map($values, callable $callback)
		{
			return static::resolve($values)->then(function($array) use ($callback) {

				if(!is_array($array)) $array = [];

				$results  = [];
				$left     = count($array);
				$deferred = new promises\Deferred();

				if(!$left)
				{
					$deferred->resolve($results);
				}
				else
				{
					$resolve = function($item, $i) use ($callback, &$results, &$left, $deferred) {

						static::resolve($item)
							->then($callback)
							->then(
								function($mapped) use (&$results, $i, &$toResolve, $deferred) {

									$results[$i] = $mapped;

									if(0 === --$toResolve) $deferred->resolve($results);
								},
								[$deferred, 'reject']
							);
					};

					foreach($array as $i => $item) $resolve($item, $i);
				}

				return $deferred->getPromise();
			});
		}
	}