<?php

declare(strict_types=1);

namespace NodaPay\Button\Service;

use NodaPay\Button\Api\Controller\BaseController;
use NodaPay\Button\DTO\ValidationError;
use WP_REST_Request;
use WP_REST_Response;

abstract class AbstractRequestHandler {

	/**
	 * @var Validator
	 */
	protected $validator;

	/**
	 * @var WP_REST_Request
	 */
	protected $request;

	/**
	 * AbstractRequestHandler constructor.
	 *
	 * @param Validator $validator
	 */
	public function __construct( Validator $validator ) {
		$this->validator = $validator;
	}

	/**
	 * Provides an array of validation rules.
	 * Validation rule callback should return the error message
	 *
	 * Example:
	 *  return [
	 *    'url' => [
	 *      'required',
	 *       function ($value) {
	 *          return is_valid_url($value) ? null : 'Value should be a valid url' : null;
	 *       }
	 *    ]
	 * ];
	 *
	 * Here in the example above "is_valid_url" is a custom function for checking url validity
	 *
	 * @param array $requestData
	 * @return array
	 */
	abstract protected function getValidationRules( array $requestData): array;

	/**
	 * @param array $requestData
	 * @return WP_REST_Response
	 */
	abstract protected function doProcessRequest( array $requestData): WP_REST_Response;

	public function processRequest( WP_REST_Request $request ): WP_REST_Response {
		$this->request = $request;
		$requestData   = $this->getRequestData( $request );

		$validationErrors = $this->validator
			->setValidationRules( $this->getValidationRules( $requestData ) )
			->validate( $requestData );

		if ( count( $validationErrors ) > 0 ) {
			return new WP_REST_Response(
				[
					'success' => false,
					'errors'  => array_map(
						function( ValidationError $error ) {
							return [ $error->getPath() => $error->getErrorMessage() ];
						},
						$validationErrors
					),
				],
				BaseController::HTTP_BAD_REQUEST
			);
		}

		return $this->doProcessRequest( $requestData );
	}

	/**
	 * Collects data from both GET and POST requests into array
	 *
	 * @param WP_REST_Request $request
	 * @return array
	 */
	protected function getRequestData( WP_REST_Request $request ): array {
		$requestBody = $request->get_body() ? json_decode( $request->get_body(), true ) : [];
		$queryData   = $request->get_params() ? $request->get_params() : [];

		return array_merge( $requestBody, $queryData );
	}
}
