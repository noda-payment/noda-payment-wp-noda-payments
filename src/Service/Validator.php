<?php

declare(strict_types=1);

namespace NodaPay\Button\Service;

use NodaPay\Button\DTO\ValidationError;
use ReflectionFunction;
use ReflectionMethod;

/**
 * Class Validator
 *
 * Validates array data in accordance with the specified rules
 *
 * @package NodaPay\Button\Service
 */
class Validator {

	/**
	 * @var array
	 */
	private $rules = [];

	/**
	 * @param array $validationRules
	 * @return Validator
	 */
	public function setValidationRules( array $validationRules ): Validator {
		$this->rules = $validationRules;

		return $this;
	}

	public function validate( array $data ): array {
		$validationErrors = [];

		foreach ( $this->rules as $key => $rules ) {
			if (
				! isset( $data[ $key ] ) && (
					$rules === 'required'
					|| (
						is_array( $rules )
						&& in_array( 'required', $rules, true )
					)
				)
			) {
				$validationErrors[] = new ValidationError( $key, 'Value is required' );
			}
		}

		foreach ( $data as $key => $value ) {
			if ( isset( $this->rules[ $key ] ) ) {
				if ( is_array( $this->rules[ $key ] ) ) {
					foreach ( $this->rules[ $key ] as $rule ) {
						if ( is_callable( $rule ) ) {
							$validationError = $this->validateRule( $rule, $key, $value, $data );
							if ( $validationError ) {
								$validationErrors[] = $validationError;
							}
						}
					}

					continue;
				}

				if ( is_callable( $this->rules[ $key ] ) ) {
					$validationError = $this->validateRule( $this->rules[ $key ], $key, $value, $data );

					if ( $validationError ) {
						$validationErrors[] = $validationError;
					}
				}
			}
		}

		return $validationErrors;
	}

	/**
	 * @param callable $validator
	 * @param string   $key
	 * @param $value
	 * @param array    $data
	 * @return ValidationError|null
	 * @throws \ReflectionException
	 */
	private function validateRule( callable $validator, string $key, $value, array $data ) {

		$arguments = [ $value ];
		if ( $this->getNumberOfCallbackParams( $validator ) === 2 ) {
			$arguments[] = $data;
		}

		$errorMsg = call_user_func_array( $validator, $arguments );

		if ( $errorMsg ) {
			return new ValidationError( $key, $errorMsg );
		}

		return null;
	}

	/**
	 * @param $callable
	 * @return int
	 * @throws \ReflectionException
	 */
	private function getNumberOfCallbackParams( $callable ): int {
		$callbackReflection = is_array( $callable ) ?
			new ReflectionMethod( $callable[0], $callable[1] ) :
			new ReflectionFunction( $callable );

		return $callbackReflection->getNumberOfParameters();
	}
}
