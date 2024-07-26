<?php

declare(strict_types=1);

namespace NodaPay\Button\Api\Controller;

use NodaPay\Button\Repository\NodaPaymentsRepository;
use NodaPay\Button\Service\PaymentStatusNotifiedRequestHandler;
use NodaPay\Button\Service\Validator;
use WP_REST_Request;
use WP_REST_Response;

class UpdatePaymentStatusController extends BaseController {

	/**
	 * @var string
	 */
	protected $route = 'payment-notification';

	/**
	 * @var PaymentStatusNotifiedRequestHandler|null
	 */
	private $paymentStatusNotifiedRequestHandler;

	/**
	 * Configure REST API routes.
	 */
	public function register_routes() {
		register_rest_route(
			self::NAMESPACE,
			'/' . $this->route,
			[
				'methods'             => BaseController::METHOD_PATCH,
				'callback'            => [ $this, 'setPaymentStatusNotified' ],
				'permission_callback' => '__return_true',
			]
		);
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function setPaymentStatusNotified( WP_REST_Request $request ): WP_REST_Response {
		return $this->getPaymentStatusNotifiedRequestHandler()->processRequest( $request );
	}

	/**
	 * @return PaymentStatusNotifiedRequestHandler
	 */
	public function getPaymentStatusNotifiedRequestHandler(): PaymentStatusNotifiedRequestHandler {
		if ( $this->paymentStatusNotifiedRequestHandler ) {
			return $this->paymentStatusNotifiedRequestHandler;
		}

		$this->paymentStatusNotifiedRequestHandler = new PaymentStatusNotifiedRequestHandler( new Validator(), new NodaPaymentsRepository() );

		return $this->paymentStatusNotifiedRequestHandler;
	}
}
