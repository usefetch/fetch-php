<?php

/**
 * @package     Fetch
 * @copyright   2017 Fetch. All rights reserved.
 * @author      Fetch
 * @link        https://usefetch.io
 * @license     MIT http://opensource.org/licenses/MIT
 */
namespace Fetch\Exception;

/**
 * Abstract Exception handling class with all common functionality
 */
abstract class BaseApiException extends \Exception
{
    /**
     * The default message to be thrown if a custom is not provided
     */
    const EXCEPTION_MESSAGE = 'An unknown exception occurred.';

    /**
     * {@inheritdoc}
     */
    public function __construct($message = '', $code = 500, \Exception $previous = null)
    {
        // No message was passed through, just trigger the default
        if (empty($message)) {
            $message = static::EXCEPTION_MESSAGE;
        }

        parent::__construct($message, $code, $previous);
    }
}
