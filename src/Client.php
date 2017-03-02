<?php

namespace Fetch;

class Client
{
	const LIBRARY_VERSION = '0.1';
	const LIBRARY_DATE = '2017-03-1';

	/**
	 * @var string API endpoint to trigger
	 */
	private $endpoint = 'https://api.usefetch.io/';

	/**
	 * @param string $apiKey API token to make all requests against
	 * @param string $secretKey API token secret counterpart
	 */
	public function __construct($apiKey, $secretKey)
	{
		$this->apiKey = $apiKey;
		$this->secretKey = $secretKey;

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
	 * Build the signature from the given JSON data
	 *
	 * @param array  $requestData Payload object to send
	 * 
	 * @return string
	 */
	private function buildSignature(array $requestData)
	{
		$jsonData = str_replace('\\/', '/', json_encode($requestData));
		return hash_hmac('sha256', $jsonData, $this->secretKey);
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
            $userAgent[] = 'curl/' . \$curlinfo['version'];
            $userAgent[] = 'curl/' . \$curlinfo['host'];
        }

        return implode(' ', $userAgent);
    }
}
