<?php

namespace NodaPay\Button\Api\Exception;

use RuntimeException;

class NodaApiException extends RuntimeException {

	protected $message = 'Error accrued when getting response from remote API';
}
