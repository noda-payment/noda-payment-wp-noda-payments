<?php

namespace NodaPay\Button\Repository;

use NodaPay\Button\NodaPaymentNotification;
use NodaPay\Button\Repository\Exception\DBException;
use NodaPay\Button\Service\CheckPaymentStatusRequestHandler;
use wpdb;

final class NodaPaymentsRepository {

	const ORDER_STATUS_PROCESSING = 0;
	const ORDER_STATUS_DONE       = 1;
	const ORDER_STATUS_FAILED     = 2;

	/**
	 * @var wpdb
	 */
	private $wpdb;

	public function __construct() {
		 global $wpdb;

		$this->wpdb = $wpdb;
	}

	private function getTableName(): string {
		return $this->wpdb->prefix . 'noda_payments';
	}

	public function createNewPayment( int $userId, string $sessionId, string $amount, string $description ): int {
		$result = $this->wpdb->insert(
			$this->getTableName(),
			[
				'user_id'     => $userId,
				'session_key' => $sessionId,
				'amount'      => $amount,
				'description' => $description,
			]
		);

		if ( $result === false ) {
			throw new DBException( $this->wpdb->last_error );
		}

		return $this->wpdb->insert_id;
	}

	public function updatePayment( int $orderId, array $data ) {
		$result = $this->wpdb->update( $this->getTableName(), $data, [ 'id' => $orderId ] );

		if ( $result === false ) {
			throw new DBException( $this->wpdb->last_error );
		}
	}

	public function beginTransaction() {
		$this->wpdb->query( 'START TRANSACTION' );
	}

	public function commit() {
		$this->wpdb->query( 'COMMIT;' );
	}

	public function rollBack() {
		$this->wpdb->query( 'ROLLBACK;' );
	}

	public function getPaymentForNotification( int $userId, string $sessionKey ): array {
		$query  = 'SELECT id, user_id, payment_id, description, payment_status';
		$query .= ' FROM ' . $this->getTableName();
		$query .= ' WHERE';

		$params = [];

		// in case user is anonymous we are searching by noda session key
		if ( $userId === 0 ) {
			$query   .= ' session_key=%s';
			$params[] = $sessionKey;
		} else {
			$query   .= ' user_id=%d';
			$params[] = $userId;
		}

		$query .= ' and updated_at > DATE(NOW() - INTERVAL ' . NodaPaymentNotification::PAYMENT_EXPIRE_IN_DAYS;
		$query .= ' DAY) and payment_status != 0 and notified = 0';
		$query .= ' ORDER BY created_at ASC LIMIT 1';

		$payments = $this->wpdb->get_results(
			$this->wpdb->prepare( $query, ...$params )
		);

		$result = [];

		if ( ! empty( $payments ) ) {
			$result = (array) current( $payments );
		}

		return $result;
	}
}
