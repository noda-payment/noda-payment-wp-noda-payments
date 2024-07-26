<?php

declare(strict_types=1);

namespace NodaPay\Button\DTO\NodaApi\Button;

use NodaPay\Button\DTO\NodaApi\GenericApiResponse;

class ButtonResponse implements GenericApiResponse {

	/**
	 * @var string
	 */
	private $url;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string
	 */
	private $displayName;

	/**
	 * @var string
	 */
	private $country;


	/**
	 * ButtonResponse constructor.
	 *
	 * @param string $url
	 * @param string $type
	 * @param string $id
	 * @param string $displayName
	 * @param string $country
	 */
	public function __construct( string $url, string $type, string $id, string $displayName, string $country = null ) {
		$this->url         = $url;
		$this->type        = $type;
		$this->id          = $id;
		$this->displayName = $displayName;
		$this->country     = $country;
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
	public function getType(): string {
		return $this->type;
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
	public function getDisplayName(): string {
		return $this->displayName;
	}

	/**
	 * @return string
	 */
	public function getCountry(): string {
		return $this->country;
	}

	public function toArray(): array {
		return [
			'url'         => $this->url,
			'type'        => $this->type,
			'id'          => $this->id,
			'displayName' => $this->displayName,
			'country'     => $this->country,
		];
	}
}
