<?php

declare(strict_types=1);

namespace NodaPay\Button\DTO\NodaApi\PayUrl;

use NodaPay\Button\DTO\NodaApi\GenericApiRequest;

class PayUrlRequest implements GenericApiRequest {

	/**
	 * @var string
	 */
	private $amount;

	/**
	 * @var string
	 */
	private $currency;

	/**
	 * @var string
	 */
	private $customerId;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var string
	 */
	private $shopId;

	/**
	 * @var string
	 */
	private $paymentId;

	/**
	 * @var string
	 */
	private $returnUrl;

	/**
	 * @var string
	 */
	private $webhookUrl;

	/**
	 * @var string|null
	 */
	private $ipAddress;

	/**
	 * @var string
	 */
	private $email;

	/**
	 * @var string
	 */
	private $providerId;

	/**
	 * @param string $amount
	 * @return self
	 */
	public function setAmount( string $amount ): self {
		$this->amount = $amount;

		return $this;
	}

	public function getAmount(): string {
		return $this->amount;
	}

	/**
	 * @param string $currency
	 * @return self
	 */
	public function setCurrency( string $currency ): self {
		$this->currency = $currency;

		return $this;
	}

	public function getCurrency(): string {
		return $this->currency;
	}

	/**
	 * @param string $customerId
	 * @return self
	 */
	public function setCustomerId( string $customerId ): self {
		$this->customerId = $customerId;

		return $this;
	}


	/**
	 * @return mixed
	 */
	public function getCustomerId(): string {
		return $this->customerId;
	}
	/**
	 * @param string $description
	 * @return self
	 */
	public function setDescription( string $description ): self {
		$this->description = $description;

		return $this;
	}

	public function getDescription(): string {
		return $this->description;
	}

	/**
	 * @param string $shopId
	 * @return self
	 */
	public function setShopId( string $shopId ): self {
		$this->shopId = $shopId;

		return $this;
	}

	public function getShopId(): string {
		return $this->shopId;
	}

	/**
	 * @param string $paymentId
	 * @return self
	 */
	public function setPaymentId( string $paymentId ): self {
		$this->paymentId = $paymentId;

		return $this;
	}

	public function getPaymentId(): string {
		return $this->paymentId;
	}

	/**
	 * @param string $returnUrl
	 * @return self
	 */
	public function setReturnUrl( string $returnUrl ): self {
		$this->returnUrl = $returnUrl;

		return $this;
	}

	public function getReturnUrl(): string {
		return $this->returnUrl;
	}

	/**
	 * @param string $webhookUrl
	 * @return self
	 */
	public function setWebhookUrl( string $webhookUrl ): self {
		$this->webhookUrl = $webhookUrl;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getWebhookUrl(): string {
		return $this->webhookUrl;
	}

	/**
	 * @param string $ipAddress
	 * @return self
	 */
	public function setIpAddress( string $ipAddress ): self {
		$this->ipAddress = $ipAddress;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getIpAddress(): string {
		return $this->ipAddress;
	}

	/**
	 * @param string $email
	 * @return self
	 */
	public function setEmail( string $email ): self {
		$this->email = $email;

		return $this;
	}

	public function getEmail(): string {
		return $this->email;
	}

	/**
	 * @param string $providerId
	 * @return self
	 */
	public function setProviderId( string $providerId ): self {
		$this->providerId = $providerId;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getProviderId(): string {
		return $this->providerId;
	}

	public function toArray(): array {
		return [
			'amount'      => $this->getAmount(),
			'currency'    => $this->getCurrency(),
			'customerId'  => $this->getCustomerId(),
			'description' => $this->getDescription(),
			'shopId'      => $this->getShopId(),
			'paymentId'   => $this->getPaymentId(),
			'returnUrl'   => $this->getReturnUrl(),
			'webhookUrl'  => $this->getWebhookUrl(), // get_site_url() . '/wp-json/' . BaseNodaController::NAMESPACE . '/' . PayCallbackController::ENDPOINT,
		];
	}
}
