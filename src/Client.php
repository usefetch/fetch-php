<?php

/**
 * @package     Fetch
 * @copyright   2017 Fetch. All rights reserved.
 * @author      Fetch
 * @link        https://usefetch.io
 * @license     MIT http://opensource.org/licenses/MIT
 */
namespace Fetch;

class Client
{
    const LIBRARY_VERSION = '0.1';
    const LIBRARY_DATE = '2017-03-1';

    /**
     * @var string API endpoints per environment
     */
    private $endpointUrls = [
        'live' => 'https://api.usefetch.io/',
        'stage' => 'https://dev.api.usefetch.io/'
    ];

    /**
     * @var string Public API key
     */
    private $apiKey;

    /**
     * @var string Secret API key
     */
    private $secretKey;

    /**
     * @var string API endpoint to trigger
     */
    private $endpoint;

    /**
     * @var \GuzzleHttp\Client
     */
    private $httpClient;

    /**
     * @param string $apiKey API token to make all requests against
     * @param string $secretKey API token secret counterpart
     */
    public function __construct($apiKey, $secretKey, $environment = 'live')
    {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;

        // Store the endpoint URL for the environment
        $this->setEnvironment($environment);

        // Create the Guzzle client
        $this->httpClient = new \GuzzleHttp\Client([
            'base_uri' => $this->endpoint,
            'headers' => [
                'API-Version' => self::LIBRARY_DATE,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$apiKey,
                'User-Agent' => $this->getUserAgent()
            ],
            'http_errors' => true,
            'verify' => true
        ]);
    }

    /**
     * Get the API endpoint for stage vs. live
     *
     * @param string $environment
     *
     * @return self
     * @throws InvalidArgumentException
     */
    public function setEnvironment($environment)
    {
        if (!array_key_exists($environment, $this->endpointUrls)) {
            throw new \InvalidArgumentException($environment.' is not a valid environment. Please use one of: '.implode(array_keys($this->endpointUrls), ', '));
        }
        $this->endpoint = $this->endpointUrls[$environment];
        return $this;
    }


    /**
     * Get an API resource object
     *
     * @param string 	$resource API endpoint (account, feeds, posts)
     *
     * @return \Fetch\Api\BaseApi
     * @throws \Fetch\Exception\ResourceNotFoundException
     */
    public function apiResource($resource)
    {
        $resource = ucfirst($resource);

        // Class name to isntantiate
        $class = 'Fetch\\Api\\'.$resource;

        if (!class_exists($class)) {
            throw new \Fetch\Exception\ResourceNotFoundException('The API resource '.$resource.' was not found.');
        }

        // Instantiate the class, pass through our client
        return new $class($this);
    }

    /**
     * Build the user agent to log library, API, guzzle, PHP and cURL versions
     *
     * @return string
     */
    private function getUserAgent()
    {
        // Build the user agent
        $userAgent = [];
        $userAgent[] = 'Fetch-PHP/'.self::LIBRARY_VERSION;
        $userAgent[] = 'Fetch-API/'.self::LIBRARY_DATE;
        $userAgent[] = 'GuzzleHttp/'.\GuzzleHttp\Client::VERSION;
        $userAgent[] = 'php/'.phpversion();

        // cURL is loaded, append the information
        if (extension_loaded('curl') && function_exists('curl_version')) {
            $curlinfo = curl_version();
            $userAgent[] = 'curl/'.$curlinfo['version'];
            $userAgent[] = 'curl/'.$curlinfo['host'];
        }

        return implode(' ', $userAgent);
    }

    /**
     * Return the API public kek
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Return the API secret key for signature building
     *
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * Return the API endpoint to call
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Return the Guzzle client instance
     *
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }
}
