<?php

/**
 * @package     Fetch
 * @copyright   2017 Fetch. All rights reserved.
 * @author      Fetch
 * @link        https://usefetch.io
 * @license     MIT http://opensource.org/licenses/MIT
 */
namespace Fetch\Api;

class Feed extends BaseApi
{
    /**
     * Endpoint to call /feed
     * @var string
     */
    protected $endpoint = 'feed';

    /**
     * Alias for getItem with additional hooks. Return all the recent posts against the feed.
     *
     * @param int $id
     * @param array $queryParameters Optional filters to pass through
     *
     * @return array|mixed
     */
    public function getPosts($id, array $queryParameters = [])
    {
        return $this->sendRequest($this->endpoint.'/'.$id.'/posts', $queryParameters);
    }

    /**
     * Alias for getItem with additional hooks. Return all a date range tally of post counts.
     *
     * @param int $id
     * @param array $queryParameters Optional filters to pass through
     *
     * @return array|mixed
     */
    public function getPostStats($id, array $queryParameters = [])
    {
        return $this->sendRequest($this->endpoint.'/'.$id.'/chart/posts', $queryParameters);
    }
}
