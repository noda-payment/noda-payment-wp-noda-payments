<?php

namespace NodaPay\Button\Api\Client;

use NodaPay\Button\Api\Headers;
use NodaPay\Button\DTO\NodaApi\Button\ButtonRequest;
use NodaPay\Button\DTO\NodaApi\GenericApiRequest;
use NodaPay\Button\Traits\NodaButtonSettings;
use WP_Error;

class ButtonClient implements ApiClientInterface {

	use NodaButtonSettings;

	const ENDPOINT = '/api/payments/logo';

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
	 * @param GenericApiRequest|ButtonRequest $payUrlRequest
	 * @return array|null|WP_Error
	 */
	public function sendRequest( GenericApiRequest $payUrlRequest ) {
		$requestBody = $payUrlRequest->toArray();

		return $this->apiClient->post( self::ENDPOINT, $requestBody, $this->headers->toArray() );
	}
}
