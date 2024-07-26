<?php

declare(strict_types=1);

namespace NodaPay\Button\Api\Controller;

use NodaPay\Button\Repository\NodaPaymentsRepository;
use NodaPay\Button\Service\CheckPaymentStatusRequestHandler;
use NodaPay\Button\Service\Validator;
use WP_REST_Request;
use WP_REST_Response;

class CheckPaymentStatusController extends BaseController {

	/**
	 * @var string
	 */
	protected $route = 'payment-notification';

	/**
	 * @var CheckPaymentStatusRequestHandler|null
	 */
	private $checkPaymentStatusRequestHandler;

	/**
	 * Configure REST API routes.
	 */
	public function register_routes() {
		register_rest_route(
			self::NAMESPACE,
			'/' . $this->route,
			[
				'methods'             => BaseController::METHOD_GET,
				'callback'            => [ $this, 'getPaymentForNotification' ],
				'permission_callback' => '__return_true',
			]
		);
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function getPaymentForNotification( WP_REST_Request $request ): WP_REST_Response {
		return $this->getCheckPaymentStatusRequestHandler()->processRequest( $request );
	}

	/**
	 * @return CheckPaymentStatusRequestHandler
	 */
	public function getCheckPaymentStatusRequestHandler(): CheckPaymentStatusRequestHandler {
		if ( $this->checkPaymentStatusRequestHandler ) {
			return $this->checkPaymentStatusRequestHandler;
		}

		$this->checkPaymentStatusRequestHandler = new CheckPaymentStatusRequestHandler( new Validator(), new NodaPaymentsRepository() );

		return $this->checkPaymentStatusRequestHandler;
	}
}
