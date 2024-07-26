<?php

declare(strict_types=1);

namespace NodaPay\Button;

class NodaPaymentNotification {

	const PAYMENT_EXPIRE_IN_DAYS = 7;

	public function __construct() {
		 $this->init_actions();
	}

	public function init_actions() {
		add_action( 'init', [ $this, 'registerNotificationsScript' ] );
	}

	public function registerNotificationsScript() {
		wp_enqueue_script(
			'noda_notification',
			plugin_dir_url( __FILE__ ) . '../block/noda_pay_button/notification.js',
			[ 'jquery' ],
			false,
			true
		);

		$jsArray = [
			'user_id'                => get_current_user_id(),
			'nonce'                  => wp_create_nonce( 'wp_rest' ),
			'payment_expire_in_days' => self::PAYMENT_EXPIRE_IN_DAYS,
		];

		wp_localize_script( 'noda_notification', 'noda_notification_php_vars', $jsArray );
	}
}
