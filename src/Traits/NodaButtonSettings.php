<?php

declare(strict_types=1);

namespace NodaPay\Button\Traits;

use NodaPay\Button\NodapayAppApi;
use NodaPay\Button\NodapaySettings;

trait NodaButtonSettings {

	public function getIsTest(): bool {
		return get_option( NodapaySettings::KEY_IS_TEST ) === '1';
	}

	public function isDisableForAnonymous(): bool {
		return get_option( NodapaySettings::KEY_DISABLE_FOR_ANONYMOUS ) === '1';
	}

	public function getCurrencyCode(): string {
		return get_option( NodapaySettings::KEY_CURRENCY_CODE );
	}

	public function getApiKey(): string {
		return get_option( NodapaySettings::KEY_API_KEY )
			? get_option( NodapaySettings::KEY_API_KEY )
			: NodapaySettings::VALUE_DEV_API_KEY;
	}

	public function getSignatureKey(): string {
		return get_option( NodapaySettings::KEY_SIGNATURE_KEY )
			? get_option( NodapaySettings::KEY_SIGNATURE_KEY )
			: NodapaySettings::VALUE_DEV_SIGNATURE_KEY;
	}

	public function getShopId(): string {
		return get_option( NodapaySettings::KEY_SHOP_ID )
			? get_option( NodapaySettings::KEY_SHOP_ID )
			: NodapaySettings::VALUE_DEV_API_KEY;
	}

	public function getApiBaseUrl(): string {
		return $this->getIsTest() ? NodapayAppApi::API_URL_SENDBOX : NodapayAppApi::API_URL_LIVE;
	}
}
