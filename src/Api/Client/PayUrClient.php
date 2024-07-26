<?php

namespace NodaPay\Button\Api\Client;

use NodaPay\Button\Api\Headers;
use NodaPay\Button\DTO\NodaApi\GenericApiRequest;
use NodaPay\Button\DTO\NodaApi\PayUrl\PayUrlRequest;
use NodaPay\Button\Traits\NodaButtonSettings;
use WP_Error;

class PayUrClient implements ApiClientInterface {

	use NodaButtonSettings;

	const ENDPOINT = '/api/payments';

	/**
	 * @var ApiClient
	 */
	private $apiClient;

	/**
	 * @var Headers
	 */
	private $headers;

	public function __construct() {
		 $this->apiClient = new ApiClient( $this->getApiBaseUrl() );
		$this->headers    = new Headers();
	}

	/**
	 * @param GenericApiRequest|PayUrlRequest $payUrlRequest
	 * @return array|null|WP_Error
	 */
	public function sendRequest( GenericApiRequest $payUrlRequest ) {
		$requestBody = $payUrlRequest->toArray();

		return $this->apiClient->post( self::ENDPOINT, $requestBody, $this->headers->toArray() );
	}
}
