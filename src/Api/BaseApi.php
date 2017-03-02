<?php

/**
 * @package     Fetch
 * @copyright   2017 Fetch. All rights reserved.
 * @author      Fetch
 * @link        https://usefetch.io
 * @license     MIT http://opensource.org/licenses/MIT
 */
namespace Fetch\Api;

abstract class Api
{
    /**
     * @var \Fetch\Client
     */
    private $client;

    /**
     * The URI endpoint for this resource
     *
     * @var string
     */
    protected $endpoint;

    /**
     * @param \Fetch\Client $client
     */
    public function __construct(\Fetch\Client $client)
    {
        $this->client = $client;
    }

    /**
     * Given the ID, retrieve a single item
     *
     * @param int $id
     * @param array $queryParameters Optional filters to pass through
     *
     * @return array|mixed
     */
    public function getItem($id, array $queryParameters = [])
    {
        return $this->sendRequest($this->endpoint.'/'.$id, $queryParameters);
    }

    /**
     * Get all the data for this endpoint
     *
     * @param array $queryParameters Optional filters to pass through
     *
     * @return array|mixed
     */
    public function get(array $queryParameters = [])
    {
        return $this->sendRequest($this->endpoint, $queryParameters);
    }

    /**
     * Send the signed request for any given endpoint
     *
     * @param string 	$endpoint
     * @param array 	$requestData
     * @param string 	$method
     *
     * @return array
     * @throws \Exception
     */
    public function sendRequest($endpoint, array $requestData = [], $method = 'GET')
    {
        $httpClient = $this->client->getHttpClient();

        // By default, put it in the data body
        $dataKey = 'form_params';

        // GET requests put them in the query
        if ($method === 'GET') {
            $dataKey = 'query';
        }

        // Build the auth signature
        $signature = $this->buildSignature($jsonData);

        // Add our key + signature globally
        $requestData['apiKey'] = $this->client->getApiKey();
        $requestData['signature'] = $signature;

        // Data holders for later
        $jsonData = [];
        $error = [];

        // Send the request
        try {
            $response = $httpClient->request($method, $this->client->getEndpoint().$endpoint, [
                $dataKey => $data
            ]);

            // Read the string
            $responseBody = $response->getBody();

            // Convert it into JSON
            $jsonData = json_decode($responseBody, true);

            // An error occurred (text from server, NOT JSON)
            if (!is_array($jsonData)) {
                $error = [
                    'code' => 500,
                    'message' => $responseBody
                ];
            }

            // JSON error object containing: code, message
            if (isset($jsonData['error'])) {
                $error = $jsonData['error'];
            }
        } catch (\Exception $e) {
            $error = [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }

        // We had an error, so return it back instead
        if (!empty($error)) {
            return [
                'error' => $error
            ];
        }

        return $jsonData;
    }

    /**
     * Build the signature from the given JSON data
     *
     * @param array  $requestData Payload object to send
     *
     * @return string
     */
    private function buildSignature(array $requestData)
    {
        $jsonData = str_replace('\\/', '/', json_encode($requestData));
        return hash_hmac('sha256', $jsonData, $this->client->getSecretKey());
    }
}
