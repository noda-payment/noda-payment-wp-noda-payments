<?php

declare(strict_types=1);

namespace NodaPay\Button\Api\Controller;

use NodaPay\Button\Repository\NodaPaymentsRepository;
use NodaPay\Button\Service\PayCallbackRequestHandler;
use NodaPay\Button\Service\Validator;
use WP_REST_Request;
use WP_REST_Response;

class PayCallbackController extends BaseController {

	const ENDPOINT = 'webhook';

	/**
	 * @var PayCallbackRequestHandler
	 */
	protected $payCallbackRequestHandler;

	/**
	 * Configure REST API routes.
	 */
	public function register_routes() {
		register_rest_route(
			self::NAMESPACE,
			'/' . self::ENDPOINT,
			[
				'methods'             => BaseController::METHOD_POST,
				'callback'            => [ $this, 'updateOrder' ],
				'permission_callback' => '__return_true',
			]
		);
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function updateOrder( WP_REST_Request $request ): WP_REST_Response {
		return $this->getPayCallbackRequestHandler()->processRequest( $request );
	}

	/**
	 * @return PayCallbackRequestHandler
	 */
	public function getPayCallbackRequestHandler(): PayCallbackRequestHandler {
		if ( $this->payCallbackRequestHandler ) {
			return $this->payCallbackRequestHandler;
		}

		$this->payCallbackRequestHandler = new PayCallbackRequestHandler( new Validator(), new NodaPaymentsRepository() );

		return $this->payCallbackRequestHandler;
	}
}
