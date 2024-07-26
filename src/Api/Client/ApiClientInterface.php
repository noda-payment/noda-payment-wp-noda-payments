<?php


namespace NodaPay\Button\Api\Client;

use NodaPay\Button\DTO\NodaApi\GenericApiRequest;
use NodaPay\Button\DTO\NodaApi\PayUrl\PayUrlRequest;
use WP_Error;

interface ApiClientInterface {

	/**
	 * @param PayUrlRequest $payUrlRequest
	 * @return array|null|WP_Error
	 */
	public function sendRequest( GenericApiRequest $payUrlRequest);
}
