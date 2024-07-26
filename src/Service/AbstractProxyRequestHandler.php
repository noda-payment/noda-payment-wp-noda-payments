<?php

declare(strict_types=1);

namespace NodaPay\Button\Service;

use NodaPay\Button\Api\Client\ApiClientInterface;
use NodaPay\Button\Api\Controller\BaseController;
use NodaPay\Button\Api\Exception\InvalidApiResponseException;
use NodaPay\Button\Api\Exception\NodaApiException;
use NodaPay\Button\DTO\NodaApi\GenericApiRequest;
use NodaPay\Button\DTO\NodaApi\GenericApiResponse;
use Throwable;
use WP_Error;
use WP_HTTP_Requests_Response;
use WP_REST_Response;

abstract class AbstractProxyRequestHandler extends AbstractRequestHandler {

	/**
	 * @var ApiClientInterface
	 */
	protected $apiClient;

	/**
	 * AbstractProxyRequestHandler constructor.
	 *
	 * @param Validator          $validator
	 * @param ApiClientInterface $apiClient
	 */
	public function __construct( Validator $validator, ApiClientInterface $apiClient ) {
		parent::__construct( $validator );
		$this->apiClient = $apiClient;
	}

	/**
	 * Map request data to request DTO
	 *
	 * @param array $requestData
	 * @return GenericApiRequest
	 */
	abstract protected function mapApiRequest( array $requestData): GenericApiRequest;

	/**
	 * Maps external API response from array to DTO object
	 *
	 * @param array $responseData
	 * @return GenericApiResponse
	 */
	abstract protected function mapApiResponse( array $responseData): GenericApiResponse;

	/**
	 * Specifies which fields are mandatory in response from remote API
	 *
	 * Example: return ['url', 'id']
	 *
	 * @return array
	 */
	abstract protected function getRequiredAPIResponseFields(): array;


	/**
	 * @param \Throwable $e
	 * @return WP_REST_Response
	 */
	protected function processExceptionResponse( \Throwable $e ): WP_REST_Response {
		return new WP_REST_Response(
			[
				'success' => false,
				'errors'  => [ $e->getMessage() ],
			],
			BaseController::HTTP_INTERNAL_SERVER_ERROR
		);
	}

	/**
	 * @param array $requestData
	 * @return WP_REST_Response
	 */
	protected function processOKResponse( array $requestData ): WP_REST_Response {
		$arrayResponse = $this->getNodaApiResponse( $requestData );

		return new WP_REST_Response( $arrayResponse, BaseController::HTTP_OK );
	}

	protected function doProcessRequest( array $requestData ): WP_REST_Response {
		try {
			return $this->processOKResponse( $requestData );
		} catch ( NodaApiException $e ) {
			return $this->processExceptionResponse( $e );
		} catch ( InvalidApiResponseException $e ) {
			return $this->processExceptionResponse( $e );
		} catch ( Throwable $e ) {
			return $this->processExceptionResponse( $e );
		}
	}

	/**
	 * @param array $requestData
	 * @return array
	 */
	public function getNodaApiResponse( array $requestData ): array {
		$requestDto = $this->mapApiRequest( $requestData );

		$result = $this->apiClient->sendRequest( $requestDto );

		if ( ! $this->isNodaApiResponseSuccess( $result ) ) {
			throw new NodaApiException();
		}

		/** @var WP_HTTP_Requests_Response $response */
		$response           = $result['http_response'];
		$responseDataString = $response->get_data();

		$responseArray = [];

		if ( ! $this->isNodaApiResponseValid( $responseDataString, $responseArray ) ) {
			throw new InvalidApiResponseException();
		}

		return $this->mapApiResponse( $responseArray )->toArray();
	}

	/**
	 * Checks structure of response body
	 *
	 * @param string     $responseDataString
	 * @param array|null $responseArray
	 * @return bool
	 */
	protected function isNodaApiResponseValid( string $responseDataString, array &$responseArray = null ): bool {
		$responseArray = json_decode( $responseDataString, true );

		if ( $responseArray === null ) {
			return false; // Invalid response string
		}

		foreach ( $this->getRequiredAPIResponseFields() as $responseKey ) {
			if ( ! isset( $responseArray[ $responseKey ] ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check the high level response structure
	 *
	 * @param array|null|WP_Error $result
	 * @return bool
	 */
	protected function isNodaApiResponseSuccess( $result ): bool {
		if (
			! is_array( $result )
			|| ! isset( $result['http_response'] )
			|| ! $result['http_response'] instanceof WP_HTTP_Requests_Response
			|| $result['http_response']->get_status() !== BaseController::HTTP_OK
		) {
			return false;
		}

		return true;
	}
}
