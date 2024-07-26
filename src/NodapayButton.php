<?php

declare(strict_types=1);

namespace NodaPay\Button;

use NodaPay\Button\Api\Client\ButtonClient;
use NodaPay\Button\Api\Exception\InvalidApiResponseException;
use NodaPay\Button\Api\Exception\NodaApiException;
use NodaPay\Button\Service\ButtonLogoProxyRequestHandler;
use NodaPay\Button\Service\Validator;
use NodaPay\Button\Traits\NodaButtonSettings;

/**
 * Class NodapayButton
 *
 * @package NodaPay\Button
 */
final class NodapayButton {

	use NodaButtonSettings;

	public function init() {
		$this->init_actions();
	}

	protected function init_actions() {
		add_action( 'init', [ $this, 'payButtonRegisterOnClickScript' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'loadNodaPayButtonBlock' ] );
	}

	public function loadNodaPayButtonBlock() {
		wp_enqueue_script(
			'noda-pay-button-block',
			plugin_dir_url( __FILE__ ) . '../block/noda_pay_button/block.js',
			[ 'wp-blocks', 'wp-editor' ],
			true
		);

		$buttonHandler = new ButtonLogoProxyRequestHandler( new Validator(), new ButtonClient() );

		try {

			$response   = $buttonHandler->getNodaApiResponse( [ 'currency' => $this->getCurrencyCode() ] );
			$buttonLogo = $response['url'];
		} catch ( NodaApiException $e ) {
			$buttonLogo = '';
		} catch ( InvalidApiResponseException $e ) {
			$buttonLogo = '';
		} catch ( \Throwable $e ) {
			$buttonLogo = '';
		} finally {
			$jsArray = [
				'user_id'  => get_current_user_id(),
				'logo_url' => $buttonLogo,
				'nonce'    => wp_create_nonce( 'wp_rest' ),
			];

			wp_localize_script( 'noda-pay-button-block', 'nodapay_button_block_php_vars', $jsArray );
		}
	}

	public function payButtonRegisterOnClickScript() {
		wp_enqueue_script(
			'noda_onclick',
			plugin_dir_url( __FILE__ ) . '../block/noda_pay_button/onclick.js',
			[ 'jquery' ],
			false,
			true
		);

		$jsArray = [
			'user_id'               => get_current_user_id(),
			'nonce'                 => wp_create_nonce( 'wp_rest' ),
			'disable_for_anonymous' => $this->isDisableForAnonymous(),
		];

		wp_localize_script( 'noda_onclick', 'nodapay_button_block_onclick_php_vars', $jsArray );
	}
}
