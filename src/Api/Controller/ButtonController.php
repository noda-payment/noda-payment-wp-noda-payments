<?php

namespace NodaPay\Button\Api\Controller;

use NodaPay\Button\Api\Client\ButtonClient;
use NodaPay\Button\Service\ButtonLogoProxyRequestHandler;
use NodaPay\Button\Service\Validator;
use NodaPay\Button\Traits\NodaButtonSettings;
use WP_REST_Request;
use WP_REST_Response;

class ButtonController extends BaseController {

	use NodaButtonSettings;

	/**
	 * @var string
	 */
	protected $route = 'logo';

	/**
	 * @var ButtonLogoProxyRequestHandler|null
	 */
	private $buttonLogoRequestHandler;


	/**
	 * Configure REST API routes.
	 */
	public function register_routes() {
		register_rest_route(
			self::NAMESPACE,
			'/' . $this->route,
			[
				'methods'             => BaseController::METHOD_POST,
				'callback'            => [ $this, 'getButtonLogo' ],
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
	public function getButtonLogo( WP_REST_Request $request ): WP_REST_Response {
		return $this->getButtonLogoRequestHandler()->processRequest( $request );
	}

	private function getButtonLogoRequestHandler(): ButtonLogoProxyRequestHandler {
		if ( $this->buttonLogoRequestHandler ) {
			return $this->buttonLogoRequestHandler;
		}

		$this->buttonLogoRequestHandler = new ButtonLogoProxyRequestHandler( new Validator(), new ButtonClient() );

		return $this->buttonLogoRequestHandler;
	}
}
