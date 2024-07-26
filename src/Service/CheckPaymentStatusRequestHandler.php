<?php

declare(strict_types=1);

namespace NodaPay\Button\Service;

use NodaPay\Button\Api\Controller\BaseController;
use NodaPay\Button\Repository\NodaPaymentsRepository;
use WP_REST_Response;

class CheckPaymentStatusRequestHandler extends AbstractRequestHandler {

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
			'noda_session_id' => [
				'required',
			],
			'user_id'         => [
				'required',
			],
		];
	}

	protected function doProcessRequest( array $requestData ): WP_REST_Response {
		$paymentToNotify = $this->paymentsRepository
			->getPaymentForNotification( (int) $requestData['user_id'], $requestData['noda_session_id'] );

		$response = [ 'success' => true ];

		if ( ! empty( $paymentToNotify ) ) {
			$response['payment'] = $paymentToNotify;
		}

		return new WP_REST_Response( $response, BaseController::HTTP_OK );
	}
}
