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
 * Exception class representing a requested API resource that does not exist
 */
class ContextNotFoundException extends BaseApiException
{
    /**
     * {@inheritdoc}
     */
    const EXCEPTION_MESSAGE = 'API resource not found.';
}
