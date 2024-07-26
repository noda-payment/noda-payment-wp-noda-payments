<?php

namespace NodaPay\Button;

final class NodapaySettings {

	const KEY_IS_TEST               = 'noda_pay_button_is_test';
	const KEY_DISABLE_FOR_ANONYMOUS = 'noda_pay_button_disable_for_anonymous';
	const KEY_CURRENCY_CODE         = 'noda_pay_button_currency';
	const KEY_API_KEY               = 'noda_pay_button_api_key';
	const KEY_SIGNATURE_KEY         = 'noda_pay_button_signature_key';
	const KEY_SHOP_ID               = 'noda_pay_button_shop_id';

	const VALUE_DEV_API_KEY       = '24d0034-5a83-47d5-afa0-cca47298c516';
	const VALUE_DEV_SIGNATURE_KEY = '028b9b98-f250-492c-a63a-dfd7c112cc0a';
	const VALUE_DEV_SHOP_ID       = 'd0c3ccd9-162c-497e-808b-e769aea89c58';

	const AVAILABLE_CURRENCIES = [ 'EUR', 'GBP', 'PLN', 'CAD', 'BRL', 'BGN', 'RON'];

	/**
	 * @var string
	 */
	protected $pluginName = 'noda-pay-button';

	public function init() {
		$this->init_actions();
	}

	protected function init_actions() {
		add_action( 'admin_menu', [ $this, 'addPluginSettingsMenu' ], 9 );
		add_action( 'admin_init', [ $this, 'registerAndBuildFields' ] );
	}

	public function addPluginSettingsMenu() {
		add_menu_page( $this->pluginName, 'Noda settings', 'administrator', $this->pluginName, [ $this, 'displayPluginAdminSettings' ], 'dashicons-admin-settings', 26 );
	}

	public function displayPluginAdminSettings() {
		if ( isset( $_GET['error_message'] ) ) {
			add_action( 'admin_notices', [ $this, 'nodaPayButtonSettingsMessages' ] );
			do_action( 'admin_notices', $_GET['error_message'] );
		}

		require_once __DIR__ . '/../include/admin/class-' . $this->pluginName . '-admin-settings-display.php';
	}

	public function nodaPayButtonSettingsMessages( $error_message ) {
		switch ( $error_message ) {
			case '1':
				$message       = __( 'There was an error adding this setting. Please try again.  If this persists, shoot us an email.', 'my-text-domain' );
				$err_code      = esc_attr( 'noda_pay_button_api_key' );
				$setting_field = 'noda_pay_button_api_key';
				break;
		}
		$type = 'error';
		add_settings_error(
			$setting_field,
			$err_code,
			$message,
			$type
		);
	}

	public function registerAndBuildFields() {
		/**
		 * add_settings_section. adds settings section, where all further settings will belong.
		 */
		add_settings_section(
		// ID used to identify this section and with which to register options
			'noda_pay_button_general_section',
			// Title to be displayed on the administration page
			'',
			// Callback used to render the description of the section
			[ $this, 'noda_pay_button_display_general_description' ],
			// Page on which to add this section of options
			'noda_pay_button_general_settings'
		);

		/**
		 * add_settings_field build the output of separate field
		 */

		$argsIsTest = [
			'type'       => 'input',
			'subtype'    => 'checkbox',
			'id'         => self::KEY_IS_TEST,
			'name'       => self::KEY_IS_TEST,
			'required'   => 'false',
			'value_type' => 'normal',
			'wp_data'    => 'option',
			'help_text'  => 'If option is checked, all API requests will be directed to test server, where no real payments are done',
		];

		add_settings_field(
			self::KEY_IS_TEST,
			'Is Test Mode',
			[ $this, 'noda_pay_button_render_settings_field' ],
			'noda_pay_button_general_settings',
			'noda_pay_button_general_section',
			$argsIsTest
		);

		$argsDisableForAnonymous = [
			'type'       => 'input',
			'subtype'    => 'checkbox',
			'id'         => self::KEY_DISABLE_FOR_ANONYMOUS,
			'name'       => self::KEY_DISABLE_FOR_ANONYMOUS,
			'required'   => 'false',
			'value_type' => 'normal',
			'wp_data'    => 'option',
			'help_text'  => 'If option is checked, all Noda pay buttons will not be available for anonymous users',
		];

		add_settings_field(
			self::KEY_DISABLE_FOR_ANONYMOUS,
			'Hide for anonymous',
			[ $this, 'noda_pay_button_render_settings_field' ],
			'noda_pay_button_general_settings',
			'noda_pay_button_general_section',
			$argsDisableForAnonymous
		);

		$argsCurrencySelect = [
			'type'      => 'select',
			'id'        => self::KEY_CURRENCY_CODE,
			'required'  => true,
			'name'      => self::KEY_CURRENCY_CODE,
			'wp_data'   => 'option',
			'options'   => self::AVAILABLE_CURRENCIES,
			'help_text' => 'Select a currency of payment among the list of supported currencies',
		];

		add_settings_field(
			self::KEY_CURRENCY_CODE,
			'Currency',
			[ $this, 'noda_pay_button_render_settings_field' ],
			'noda_pay_button_general_settings',
			'noda_pay_button_general_section',
			$argsCurrencySelect
		);

		$argsApiKey = [
			'type'             => 'input',
			'subtype'          => 'password',
			'id'               => self::KEY_API_KEY,
			'name'             => self::KEY_API_KEY,
			'required'         => 'true',
			'get_options_list' => '',
			'value_type'       => 'normal',
			'wp_data'          => 'option',
			'default'          => self::VALUE_DEV_API_KEY,
			'help_text'        => 'Complete the onboarding at <a href="https://ui.noda.live/hub" rel="nofollow" target="_blank">https://ui.noda.live/hub</a> to get your production API key',
		];

		add_settings_field(
			self::KEY_API_KEY,
			'API Key',
			[ $this, 'noda_pay_button_render_settings_field' ],
			'noda_pay_button_general_settings',
			'noda_pay_button_general_section',
			$argsApiKey
		);

		$argsSignatureKey = [
			'type'             => 'input',
			'subtype'          => 'password',
			'id'               => self::KEY_SIGNATURE_KEY,
			'name'             => self::KEY_SIGNATURE_KEY,
			'required'         => 'true',
			'get_options_list' => '',
			'value_type'       => 'normal',
			'wp_data'          => 'option',
			'default'          => self::VALUE_DEV_SIGNATURE_KEY,
			'help_text'        => 'Complete the onboarding at <a href="https://ui.noda.live/hub" rel="nofollow" target="_blank">https://ui.noda.live/hub</a> to get your production signature key',
		];

		add_settings_field(
			self::KEY_SIGNATURE_KEY,
			'API Signature Key',
			[ $this, 'noda_pay_button_render_settings_field' ],
			'noda_pay_button_general_settings',
			'noda_pay_button_general_section',
			$argsSignatureKey
		);

		$argsShopId = [
			'type'             => 'input',
			'subtype'          => 'password',
			'id'               => self::KEY_SHOP_ID,
			'name'             => self::KEY_SHOP_ID,
			'required'         => 'true',
			'get_options_list' => '',
			'value_type'       => 'normal',
			'wp_data'          => 'option',
			'default'          => self::VALUE_DEV_SHOP_ID,
			'help_text'        => 'Complete the onboarding at <a href="https://ui.noda.live/hub" rel="nofollow" target="_blank">https://ui.noda.live/hub</a> to get your production shop id',
		];

		add_settings_field(
			self::KEY_SHOP_ID,
			'Shop Id',
			[ $this, 'noda_pay_button_render_settings_field' ],
			'noda_pay_button_general_settings',
			'noda_pay_button_general_section',
			$argsShopId
		);

		/**
		 *  register_setting adds field to the
		 */
		register_setting(
			'noda_pay_button_general_settings',
			'noda_pay_button_api_key',
			[
				'type' => 'array',
			]
		);

		register_setting(
			'noda_pay_button_general_settings',
			self::KEY_IS_TEST
		);

		register_setting(
			'noda_pay_button_general_settings',
			self::KEY_DISABLE_FOR_ANONYMOUS
		);

		register_setting(
			'noda_pay_button_general_settings',
			self::KEY_CURRENCY_CODE,
			[
				'type'              => 'array',
				'sanitize_callback' => [ $this, 'validate_currency_value' ],
			]
		);

		register_setting(
			'noda_pay_button_general_settings',
			self::KEY_SIGNATURE_KEY
		);

		register_setting(
			'noda_pay_button_general_settings',
			self::KEY_SHOP_ID
		);
	}

	public function validate_currency_value( $value ) {
		$oldValue   = get_option( 'noda_pay_button_currency' );
		$has_errors = false;

		if ( empty( trim( $value ) ) ) {
			$has_errors = true;
		}

		if ( $has_errors ) {
			$value = $oldValue;
		}

		return $value;
	}

	public function noda_pay_button_display_general_description() {
		echo '<p>These settings control Noda pay button functional and look.</p>';
	}


	/**
	 * @param $args
	 *
	 * EXAMPLE INPUT
	 * 'type'      => 'input',
	 * 'subtype'   => '',
	 * 'id'    => $this->noda_pay_button.'_example_setting',
	 * 'name'      => $this->noda_pay_button.'_example_setting',
	 * 'required' => 'required="required"',
	 * 'get_option_list' => "",
	 * 'value_type' = serialized OR normal,
	 * 'help_text' => 'S text describing the current input',
	 * 'wp_data'=>(option or post_meta),
	 */
	public function noda_pay_button_render_settings_field( array $args ) {

		if ( isset( $args['wp_data'] ) && $args['wp_data'] === 'option' ) {
			$wp_data_value = get_option( $args['name'] );
		} elseif ( isset( $args['wp_data'] ) && $args['wp_data'] === 'post_meta' ) {
			$wp_data_value = get_post_meta( $args['post_id'], $args['name'], true );
		}

		if ( ! isset( $args['type'] ) ) {
			return;
		}

		switch ( $args['type'] ) {
			case 'input':
				$value = ( $args['value_type'] == 'serialized' ) ? serialize( $wp_data_value ) : $wp_data_value;

				if ( $args['subtype'] != 'checkbox' ) {

					if ( empty( $value ) && isset( $args['default'] ) ) {
						$value = $args['default'];
					}

					$prependStart = ( isset( $args['prepend_value'] ) ) ? '<div class="input-prepend"> <span class="add-on">' . $args['prepend_value'] . '</span>' : '';
					$prependEnd   = ( isset( $args['prepend_value'] ) ) ? '</div>' : '';
					$step         = ( isset( $args['step'] ) ) ? 'step="' . $args['step'] . '"' : '';
					$min          = ( isset( $args['min'] ) ) ? 'min="' . $args['min'] . '"' : '';
					$max          = ( isset( $args['max'] ) ) ? 'max="' . $args['max'] . '"' : '';

					if ( isset( $args['disabled'] ) ) {
						echo $prependStart . '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '_disabled" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '_disabled" size="40" disabled value="' . esc_attr( $value ) . '" /><input type="hidden" id="' . $args['id'] . '" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '" size="40" value="' . esc_attr( $value ) . '" />' . $prependEnd;
					} else {
						echo $prependStart . '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '" "' . $args['required'] . '" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '" size="40" value="' . esc_attr( $value ) . '" />' . $prependEnd;
					}
				} else {
					$checked = ( $value ) ? 'checked' : '';
					echo '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '" "' . $args['required'] . '" name="' . $args['name'] . '" size="40" value="1" ' . $checked . ' />';
				}

				if ( isset( $args['help_text'] ) ) {
					$helpText = $args['help_text'];
					echo PHP_EOL . "<p>$helpText</p>";
				}

				break;
			case 'select':
				if ( ! isset( $args['options'] ) && ! is_array( $args['options'] && empty( $args['options'] ) ) ) {
					return; // do not create options without selects
				}

				$required = '';
				if ( isset( $args['required'] ) && $args['required'] === true ) {
					$required = ' required';
				}

				$selectOptionHtml = '<select id="' . $args['id'] . '" name="' . $args['name'] . '" ' . $required . '>';

				$selectOptionHtml .= '<option></option>';

				$storedVal = get_option( 'noda_pay_button_currency' );

				foreach ( $args['options'] as $option ) {
					$selected          = $option === $storedVal ? ' selected' : '';
					$selectOptionHtml .= '<option value="' . $option . '"' . $selected . '>' . $option . '</option>';
				}

				$selectOptionHtml .= '</select>';

				if ( isset( $args['help_text'] ) ) {
					$selectOptionHtml .= PHP_EOL . '<p>' . $args['help_text'] . '</p>';
				}

				echo $selectOptionHtml;

				break;
			default:
				break;
		}
	}
}
