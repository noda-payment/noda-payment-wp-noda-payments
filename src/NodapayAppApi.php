<?php

namespace NodaPay\Button;

use NodaPay\Button\Api\Controller\ButtonController;
use NodaPay\Button\Api\Controller\CheckPaymentStatusController;
use NodaPay\Button\Api\Controller\PayCallbackController;
use NodaPay\Button\Api\Controller\PayUrlController;
use NodaPay\Button\Api\Controller\UpdatePaymentStatusController;

/**
 * Class NodapayAppApi
 *
 * @package NodaPay\Button
 */
class NodapayAppApi {

	const API_URL_LIVE    = 'https://api.noda.live';
	const API_URL_SENDBOX = 'https://api.stage.noda.live';

	public function init() {
		add_action( 'rest_api_init', [ $this, 'registerRoutes' ] );
	}

	public function registerRoutes() {
		( new PayUrlController() )->register_routes();
		( new ButtonController() )->register_routes();
		( new PayCallbackController() )->register_routes();
		( new CheckPaymentStatusController() )->register_routes();
		( new UpdatePaymentStatusController() )->register_routes();
	}
}
