<?php

namespace NodaPay\Button;

use NodaPay\Button\Contracts\Plugin;
use NodaPay\Button\Traits\NodaButtonSettings;

class Instance implements Plugin {

	/**
	 * Minimum required PHP version for the plugin.
	 *
	 * @var string
	 */
	const PHP_MIN_VERSION = '7.0.3';

	/**
	 * Minimum required WordPress version for the plugin.
	 *
	 * @var string
	 */
	const WORDPRESS_MIN_VERSION = '5.3';

	/**
	 * Base name of the plugin.
	 *
	 * @var string
	 */
	private $basename;

	/**
	 * Admin notice handler.
	 *
	 * @var AdminNotice
	 */
	private $admin_notice;

	/**
	 * Verificator instance for various checks.
	 *
	 * @var Verificator
	 */
	private $verificator;

	/**
	 * @var NodapayButton
	 */
	private $nodapayButton;


	/**
	 * @var NodaButtonSettings
	 */
	private $nodapayButtonSettings;

	/**
	 * @var NodapayAppApi
	 */
	private $nodapayApi;

	/**
	 * @var NodaPaymentNotification
	 */
	private $nodaPayNotification;

	/**
	 * Instance constructor.
	 *
	 * @param string      $basename Base name of the plugin.
	 * @param AdminNotice $admin_notice Admin notice handler.
	 * @param Verificator $verificator Verificator instance for various checks.
	 */
	public function __construct(
		string $basename,
		AdminNotice $admin_notice,
		Verificator $verificator,
		NodapayButton $nodapayButton,
		NodapaySettings $nodapayButtonSettings,
		NodapayAppApi $nodapayApi,
		NodaPaymentNotification $nodaPaymentNotification
	) {
		 $this->basename             = $basename;
		$this->admin_notice          = $admin_notice;
		$this->verificator           = $verificator;
		$this->nodapayButton         = $nodapayButton;
		$this->nodapayButtonSettings = $nodapayButtonSettings;
		$this->nodapayApi            = $nodapayApi;
		$this->nodaPayNotification   = $nodaPaymentNotification;
	}

	public function initButton() {
		$this->nodapayButton->init();
		$this->nodapayButtonSettings->init();
	}

	public function initAdminSettings() {
		$this->nodapayButtonSettings->init();
	}

	public function initWPApi() {
		$this->nodapayApi->init();
	}

	/**
	 * Activation hook callback.
	 */
	public function activate() {
		$this->createTables();

		$errors = $this->verify();

		if ( ! $errors ) {
			return;
		}

		wp_die( wp_kses( implode( '<br>', $errors ), [ '<br>' ] ) );
	}

	/**
	 * Deactivation hook callback.
	 */
	public function deactivate() {
		// Code to run during plugin deactivation.
	}

	/**
	 * Uninstall hook callback.
	 */
	public static function uninstall() {
		global $wpdb;

		$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'noda_payments' );
	}

	/**
	 * Initialize the plugin hooks.
	 *
	 * @action plugins_loaded
	 */
	public function init() {
		$errors = $this->verify();

		if ( ! $errors ) {
			return;
		}

		foreach ( $errors as $error ) {
			add_action(
				'admin_notices',
				function () use ( $error ) {
					$this->admin_notice->print_notice( $error, 'error' );
				}
			);
		}

		deactivate_plugins( $this->basename );
	}

	/**
	 * Verifies various conditions and returns an array of error messages for any failed verifications.
	 *
	 * @return string[] Array of error messages.
	 */
	protected function verify(): array {
		$errors = [];

		switch ( true ) {
			case ( ! $this->verificator->is_php_version_compatible( self::PHP_MIN_VERSION ) ):
				// Translators: %s: Minimum required PHP version.
				$errors[] = sprintf( __( 'The PHP version is below the minimum required of %s.', 'noda' ), self::PHP_MIN_VERSION );
				// Fall through.
			case ( ! $this->verificator->is_cms_version_compatible( self::WORDPRESS_MIN_VERSION ) ):
				// Translators: %s: Minimum required WordPress version.
				$errors[] = sprintf( __( 'The WordPress version is below the minimum required of %s.', 'noda' ), self::WORDPRESS_MIN_VERSION );
				// Fall through.
		}

		return $errors;
	}

	private function createTables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table_name = $wpdb->prefix . 'noda_payments';

		$sql = [
			'CREATE TABLE ' . $table_name . " (
	id bigint(20) unsigned NOT NULL auto_increment,
	user_id bigint(20) unsigned NOT NULL,
	session_key varchar(128) NULL DEFAULT NULL,
	amount double DEFAULT 0 NOT NULL,
	payment_id varchar(100) NULL DEFAULT NULL,
	payment_status int(2) NULL DEFAULT NULL COMMENT \"0 - Processing, 1 - Done, 2 - Failed\",
	description varchar(255) NULL DEFAULT NULL,
	notified int(1) NOT NULL DEFAULT 0,
	created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY  (id)
    ) $charset_collate",
		];

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $sql );

		// add indexes to table
		$wpdb->query( 'create index wp_noda_session_key_updated_at_payment_status_notified_idx on ' . $table_name . ' (session_key, updated_at, payment_status, notified, created_at)' );
		$wpdb->query( 'create index wp_noda_user_id_updated_at_payment_status_notified_idx on ' . $table_name . ' (user_id, updated_at, payment_status, notified, created_at)' );
	}
}
