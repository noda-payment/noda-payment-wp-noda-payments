<?php

declare(strict_types=1);

namespace NodaPay\Button\Api\Controller;

use NodaPay\Button\Api\Client\PayUrClient;
use NodaPay\Button\Repository\NodaPaymentsRepository;
use NodaPay\Button\Service\PayUrlProxyRequestHandler;
use NodaPay\Button\Service\Validator;
use NodaPay\Button\Traits\NodaButtonSettings;
use WP_REST_Request;
use WP_REST_Response;

class PayUrlController extends BaseController {

	use NodaButtonSettings;

	/**
	 * @var string
	 */
	protected $route = 'pay-url';

	/**
	 * @var PayUrlProxyRequestHandler|null
	 */
	private $payUrlService;


	/**
	 * Configure REST API routes.
	 */
	public function register_routes() {
		register_rest_route(
			self::NAMESPACE,
			'/' . $this->route,
			[
				'methods'             => BaseController::METHOD_POST,
				'callback'            => [ $this, 'getPaymentUrl' ],
				'permission_callback' => '__return_true',
			]
		);
	}

	/**
	 * Required GET params:
	 *  - amount; example: "5.59"; string
	 *  - redirectUrl; example: "http://wordpress.local/sample-page" - a page, where the button was pressed
	 *  - description; example: "Donation for WordPress.local website" - this is a destination of payment
	 *  - userId; example 34 (integer)
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function getPaymentUrl( WP_REST_Request $request ): WP_REST_Response {
		return $this->getPayUrRequestHandler()->processRequest( $request );
	}

	private function getPayUrRequestHandler(): PayUrlProxyRequestHandler {
		if ( $this->payUrlService ) {
			return $this->payUrlService;
		}

		$this->payUrlService = new PayUrlProxyRequestHandler( new Validator(), new PayUrClient(), new NodaPaymentsRepository() );

		return $this->payUrlService;
	}
}
