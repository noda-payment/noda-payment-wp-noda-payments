<?php

declare(strict_types=1);

namespace NodaPay\Button\DTO;

class ValidationError {

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var string
	 */
	private $errorMessage;


	/**
	 * ValidationError constructor.
	 *
	 * @param string $path
	 * @param string $errorMessage
	 */
	public function __construct( string $path, string $errorMessage ) {
		$this->path         = $path;
		$this->errorMessage = $errorMessage;
	}

	public function getPath(): string {
		return $this->path;
	}

	public function getErrorMessage(): string {
		return $this->errorMessage;
	}
}
