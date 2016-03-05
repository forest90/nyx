<?php namespace nyx\console\dialog\questions;

	// Internal dependencies
	use nyx\console\exceptions;
	use nyx\console\interfaces;
	use nyx\console\input;
	use nyx\console\dialog;
	use nyx\console;

	/**
	 * Validated Question
	 *
	 * A Question the answer for which needs to pass a given truth test within an optional number of attempts.
	 * The validator needs to accept the user's answer and return it when it is valid or throw an exception otherwise.
	 *
	 * When using a default answer for the question keep in mind that is also needs to pass validation. The default will
	 * only be returned by this instance if the user provides no input at all or when the allowed number of attempts
	 * is exceeded, unless the self::$alwaysThrowException flag is set to true {@see self::setAlwaysThrowException()}.
	 *
	 * @package     Nyx\Console\Dialog
	 * @version     0.1.0
	 * @author      Michal Chojnacki <m.chojnacki@muyo.pl>
	 * @copyright   2012-2014 Nyx Dev Team
	 * @link        http://docs.muyo.pl/nyx/console/dialog.html
	 */

	class Validated extends dialog\Question
	{
		/**
		 * @var callable    The truth test which needs to be passed by an answer.
		 */

		private $validator;

		/**
		 * @var int|bool    The number of allowed attempts. False equals unlimited attempts.
		 */

		private $attemptsAllowed;

		/**
		 * @var int     The number of attempts the user has had with the question (gets reset only when the question is
		 *              asked completely anew, ie. not by the automated loop until the allowed attempts are exceeded).
		 */

		private $attemptsCount;

		/**
		 * @var bool    Whether this instance should always throw an exception when the allowed number of attempts is
		 *              exceeded, instead of returning the default answer when it is set (not null).
		 */

		private $alwaysThrowException;

		/**
		 * {@inheritDoc}
		 *
		 * @param   callable        $validator      The truth test which needs to be passed by an answer.
		 * @param   mixed           $default        The default answer (also needs to pass the truth test).
		 * @param   int|bool        $attempts       {@see self::setAllowedAttempts()}.
		 */

		public function __construct($text, callable $validator, $default = null, $attempts = false, $format = '  %text% [%default%] ')
		{
			$this->setValidator($validator);
			$this->setAttemptsAllowed($attempts);

			parent::__construct($text, $default, $format);
		}

		/**
		 * Returns the number of allowed attempts.
		 *
		 * @return  int
		 */

		public function getAttemptsAllowed()
		{
			return $this->attemptsAllowed;
		}

		/**
		 * Sets the number of allowed attempts.
		 *
		 * @param   int|bool    $attempts   The number of allowed attempts. Strictly boolean values and values
		 *                                  evaluating to false will be converted to a boolean false which in turn
		 *                                  means unlimited attempts will be allowed. Everything else will be cast
		 *                                  to absolute integers.
		 */

		public function setAttemptsAllowed($attempts)
		{
			$this->attemptsAllowed = (is_bool($attempts) or !$attempts) ? false : (abs((int) $attempts) ?: 1);
		}

		/**
		 * Returns the number of attempts that have been used to answer (or fail to answer) the last time the question
		 * was asked.
		 *
		 * @return  int
		 */

		public function getAttemptsCount()
		{
			return $this->attemptsCount;
		}

		/**
		 * Returns the truth test which needs to be passed by an answer.
		 *
		 * @return  callable
		 */

		public function getValidator()
		{
			return $this->validator;
		}

		/**
		 * Sets the truth test which needs to be passed by an answer.
		 *
		 * @param   callable    $validator
		 */

		public function setValidator(callable $validator)
		{
			$this->validator = $validator;
		}

		/**
		 * Sets whether this instance should always throw an exception when the allowed number of attempts is exceeded,
		 * instead of returning the default answer when it is set (not null).
		 *
		 * @param   bool    $bool
		 */

		public function setAlwaysThrowException($bool)
		{
			$this->alwaysThrowException = (bool) $bool;
		}

		/**
		 * {@inhertitDoc}
		 *
		 * @throws  exceptions\dialog\AttemptsAllowedExceeded   When no correct answer was given after the allowed number
		 *                                                      of attempts, unless a default answer is set and the
		 *                                                      self::$alwaysThrowException flag is not set to true.
		 */

		public function ask(input\Stream $input, interfaces\Output $output, $force = false)
		{
			// Reset the attempt count.
			$this->attemptsCount = 0;

			// Copy the allowed attempts count into the local scope so we don't overwrite it.
			$allowed = $this->getAttemptsAllowed();

			// Loop until we run out of allowed attempts.
			while($allowed === false or $allowed--)
			{
				$this->attemptsCount++;

				// Manually display the previous error when still within the loop.
				// @todo Consider making the helper optionally injectable.
				isset($error) and $output->writeln((new console\helpers\Formatter)->block($error->getMessage(), 'error', true));

				// Ask the Question and pass the user's answer directly to our validator. The validator shall either
				// throw an exception when it's not satisfied or return the answer otherwise. The input\StreamAborted
				// exception is reserved to handle cases when the input is aborted and completely exits this loop.
				// It may, however, also be utilized by a validator to do just that.
				try
				{
					return call_user_func($this->validator, parent::ask($input, $output, $force));
				}
				// Stream read exception need to be handled differently so we don't end up in an infinite loop when the
				// user aborts the input (^D for instance) but the maximal number of allowed attempts is infinite.
				catch(exceptions\input\StreamAborted $exception)
				{
					throw $exception;
				}
				catch(\Exception $error) { }
			}

			// If we've got a default answer and are not forcing an exception, return the default. Not having a default
			// until now may happen if the user constantly provided answer that did not pass validation instead of just
			// not providing any answer at all (which would invoke the default), or when the default answer itself fails
			// to pass validation.
			if(!$this->alwaysThrowException and null !== $default = $this->getDefault()) return $default;

			// Throw an attempt-specific exception in the end.
			throw new exceptions\dialog\AttemptsAllowedExceeded($this);
		}
	}