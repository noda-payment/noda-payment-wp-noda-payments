<?php

declare(strict_types=1);

namespace NodaPay\Button\DTO\NodaApi\PayUrl;

use NodaPay\Button\DTO\NodaApi\GenericApiResponse;

class PayUrlResponse implements GenericApiResponse {

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string
	 */
	private $url;

	/**
	 * @var string
	 */
	private $sessionId;


	public function __construct( string $id, string $url, string $sessionId ) {
		$this->id        = $id;
		$this->url       = $url;
		$this->sessionId = $sessionId;
	}

	/**
	 * @return string
	 */
	public function getId(): string {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getUrl(): string {
		return $this->url;
	}

	/**
	 * @return string
	 */
	public function getSessionId(): string {
		return $this->sessionId;
	}

	public function toArray(): array {
		return [
			'id'         => $this->id,
			'url'        => $this->url,
			'session_id' => $this->sessionId,
		];
	}
}
