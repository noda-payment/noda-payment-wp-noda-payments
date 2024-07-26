<?php

namespace NodaPay\Button\Api\Controller;

use WP_REST_Controller;

abstract class BaseController extends WP_REST_Controller {

	const METHOD_GET   = 'GET';
	const METHOD_POST  = 'POST';
	const METHOD_PATCH = 'PATCH';

	const HTTP_BAD_REQUEST           = 400;
	const HTTP_OK                    = 200;
	const HTTP_INTERNAL_SERVER_ERROR = 500;

	const HEADERS_ACCEPT       = 'application/json, text/json, text/plain';
	const HEADERS_CONTENT_TYPE = 'application/*+json';

	const NAMESPACE = 'noda-button';
}
