<?php

declare(strict_types=1);

namespace NodaPay\Button\DTO\NodaApi\Button;

use NodaPay\Button\DTO\NodaApi\GenericApiRequest;

class ButtonRequest implements GenericApiRequest {

	/**
	 * @var string
	 */
	private $currency;

	/**
	 * ButtonRequest constructor.
	 *
	 * @param string $currency
	 */
	public function __construct( string $currency ) {
		$this->currency = $currency;
	}

	/**
	 * @return string
	 */
	public function getCurrency(): string {
		return $this->currency;
	}

	/**
	 * @return string[]
	 */
	public function toArray(): array {
		return [
			'currency' => $this->currency,
		];
	}
}
