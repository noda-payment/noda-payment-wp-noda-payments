<?php

declare(strict_types=1);

namespace NodaPay\Button\Api;

use NodaPay\Button\Traits\NodaButtonSettings;

class Headers {

	use NodaButtonSettings;

	private $headers = [];

	public function __construct() {
		$this->headers = [
			'Accept'       => 'application/json, text/json, text/plain',
			'Content-Type' => 'application/*+json',
			'x-api-key'    => $this->getApiKey(), // '24d0034-5a83-47d5-afa0-cca47298c516',
			'Plugin-Type' => 'WordPress'
		];
	}

	public function set( string $key, string $value ): self {
		$this->headers[ $key ] = $value;

		return $this;
	}

	/**
	 * @param $key
	 * @return string|null
	 */
	public function get( $key ) {
		return $this->headers[ $key ] ?? null;
	}

	public function toArray(): array {
		return $this->headers;
	}
}
