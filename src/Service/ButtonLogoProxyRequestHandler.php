<?php

declare(strict_types=1);

namespace NodaPay\Button\Service;

use NodaPay\Button\DTO\NodaApi\Button\ButtonRequest;
use NodaPay\Button\DTO\NodaApi\Button\ButtonResponse;
use NodaPay\Button\DTO\NodaApi\GenericApiRequest;
use NodaPay\Button\DTO\NodaApi\GenericApiResponse;
use NodaPay\Button\Traits\NodaButtonSettings;

class ButtonLogoProxyRequestHandler extends AbstractProxyRequestHandler {

	use NodaButtonSettings;

	protected function getValidationRules( array $requestData ): array {
		return [];
	}

	/**
	 * @param array $requestData
	 * @return GenericApiRequest|ButtonRequest
	 */
	protected function mapApiRequest( array $requestData ): GenericApiRequest {
		return new ButtonRequest( $this->getCurrencyCode() );
	}

	protected function mapApiResponse( array $responseData ): GenericApiResponse {
		return new ButtonResponse(
			$responseData['url'],
			$responseData['type'],
			$responseData['id'],
			$responseData['displayName']
		);
	}

	protected function getRequiredAPIResponseFields(): array {
		return [ 'url', 'type', 'displayName', 'id' ];
	}
}
