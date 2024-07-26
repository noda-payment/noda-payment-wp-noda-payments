<?php

namespace NodaPay\Button\Api\Exception;

use RuntimeException;

class InvalidApiResponseException extends RuntimeException {

	protected $message = 'Invalid response from remote API';
}
