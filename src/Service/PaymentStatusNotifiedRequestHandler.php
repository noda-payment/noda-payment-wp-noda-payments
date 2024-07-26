<?php

declare(strict_types=1);

namespace NodaPay\Button\Service;

use NodaPay\Button\Api\Controller\BaseController;
use NodaPay\Button\Repository\Exception\DBException;
use NodaPay\Button\Repository\NodaPaymentsRepository;
use WP_REST_Response;

class PaymentStatusNotifiedRequestHandler extends AbstractRequestHandler {

	/**
	 * @var NodaPaymentsRepository
	 */
	private $paymentsRepository;

	public function __construct( Validator $validator, NodaPaymentsRepository $paymentsRepository ) {
		parent::__construct( $validator );

		$this->paymentsRepository = $paymentsRepository;
	}

	protected function getValidationRules( array $requestData ): array {
		return [
			'order_id' => [
				'required',
				function ( $value ) {
					return ! is_numeric( $value ) || (string) intval( $value ) !== (string) $value ? 'Value should be an integer number' : null;
				},
				function ( $value ) {
					return intval( $value ) < 1 ? 'Value should be greater then zero' : null;
				},
			],
		];
	}

	protected function doProcessRequest( array $requestData ): WP_REST_Response {
		try {
			$this->paymentsRepository->updatePayment( (int) $requestData['order_id'], [ 'notified' => 1 ] );
		} catch ( DBException $e ) {
			return new WP_REST_Response(
				[
					'success' => false,
					'errors'  => [ 'message' => $e->getMessage() ],
				],
				BaseController::HTTP_INTERNAL_SERVER_ERROR
			);
		}

		return new WP_REST_Response( [ 'success' => true ], BaseController::HTTP_OK );
	}
}
