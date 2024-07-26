<?php

declare(strict_types=1);

namespace NodaPay\Button\Service;

use NodaPay\Button\Api\Client\ApiClientInterface;
use NodaPay\Button\Api\Controller\BaseController;
use NodaPay\Button\Api\Controller\PayCallbackController;
use NodaPay\Button\DTO\NodaApi\GenericApiRequest;
use NodaPay\Button\DTO\NodaApi\GenericApiResponse;
use NodaPay\Button\DTO\NodaApi\PayUrl\PayUrlRequest;
use NodaPay\Button\DTO\NodaApi\PayUrl\PayUrlResponse;
use NodaPay\Button\Repository\NodaPaymentsRepository;
use NodaPay\Button\Traits\NodaButtonSettings;
use WP_REST_Response;

final class PayUrlProxyRequestHandler extends AbstractProxyRequestHandler {

	use NodaButtonSettings;

	/**
	 * @var mixed
	 */
	private $sessionId = null;

	/**
	 * @var PayUrlRequest
	 */
	private $payUrlRequest = null;
	/**
	 * @var NodaPaymentsRepository
	 */
	private $paymentsRepository;

	/**
	 * @var int
	 */
	private $orderId;

	public function __construct( Validator $validator, ApiClientInterface $apiClient, NodaPaymentsRepository $paymentsRepository ) {
		parent::__construct( $validator, $apiClient );

		$this->paymentsRepository = $paymentsRepository;
	}

	/**
	 * Provides an array of validation rules.
	 * Validation rule callback should
	 *
	 * @param array $requestData
	 * @return array
	 */
	protected function getValidationRules( array $requestData ): array {
		return [
			'amount'      => [
				'required',
				function ( $value ) {
					return ! is_numeric( $value ) ? 'Value should be a valid number' : null;
				},
				function ( $value ) {
					return ! (float) $value > 0 ? 'Value should be greater then 0' : null;
				},
			],
			'redirectUrl' => 'required',
			'description' => [
				'required',
				function ( $value ) {
					return ! is_string( $value ) ? 'Description is not a string' : null;
				},
			],
			'userId'      => [
				function ( $value ) {
					// if value = 0 user is not logged in
					return ! is_numeric( $value ) || ! (string) intval( $value ) === $value || (int) $value < 0
						? 'Invalid user id value. User id should be a positive integer'
						: null;
				},
			],
		];
	}

	protected function mapApiRequest( array $requestData ): GenericApiRequest {
		$amount = (float) $requestData['amount'];
		$amount = (string) ( intval( round( $amount * 100 ) ) / 100 );

		$sessionId   = $this->getNodaSessionId( $requestData['session_id'] ?? null );
		$userId      = (int) $requestData['userId'];
		$description = $requestData['description'];

		$this->paymentsRepository->beginTransaction();
		$this->orderId = $this->paymentsRepository->createNewPayment( $userId, $sessionId, $amount, $description );

		$customerId = (string) $userId;

		if ( $customerId === '0' || empty($customerId) ) {
			$customerId = $sessionId;
		}

		$this->payUrlRequest = ( new PayUrlRequest() )
			->setAmount( $amount )
			->setCurrency( $this->getCurrencyCode() )
			->setShopId( $this->getShopId() )
			->setCustomerId( $customerId )
			->setPaymentId( (string) $this->orderId )
			->setReturnUrl( $requestData['redirectUrl'] )
			->setDescription( $description )
			->setWebhookUrl( get_site_url() . '/wp-json/' . BaseController::NAMESPACE . '/' . PayCallbackController::ENDPOINT );

		return $this->payUrlRequest;
	}

	protected function mapApiResponse( array $responseData ): GenericApiResponse {
		$paymentId = $responseData['id'];
		$this->paymentsRepository->updatePayment(
			$this->orderId,
			[
				'payment_id'     => $paymentId,
				'payment_status' => NodaPaymentsRepository::ORDER_STATUS_PROCESSING,
			]
		);

		return new PayUrlResponse(
			$paymentId,
			$responseData['url'],
			$this->sessionId
		);
	}

	/**
	 * @param \Throwable $e
	 * @return WP_REST_Response
	 */
	protected function processExceptionResponse( \Throwable $e ): WP_REST_Response {
		$this->paymentsRepository->rollBack();

		return parent::processExceptionResponse( $e );
	}

	/**
	 * @param array $requestData
	 * @return WP_REST_Response
	 */
	protected function processOKResponse( array $requestData ): WP_REST_Response {
		$arrayResponse = $this->getNodaApiResponse( $requestData );
		$this->paymentsRepository->commit();

		return new WP_REST_Response( $arrayResponse, BaseController::HTTP_OK );
	}

	/**
	 * Specifies which fields are expected in response from remote API
	 *
	 * @return array
	 */
	public function getRequiredAPIResponseFields(): array {
		return [ 'url', 'id' ];
	}

	/**
	 * @throws \Exception
	 */
	private function getNodaSessionId( $sessionId ): string {
		if ( $sessionId ) {
			$this->sessionId = $sessionId;
		} else {
			$this->sessionId = bin2hex( random_bytes( 32 ) );
		}

		return $this->sessionId;
	}
}
