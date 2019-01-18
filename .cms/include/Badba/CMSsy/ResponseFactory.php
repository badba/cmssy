<?php

namespace Badba\CMSsy;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Jasny\HttpMessage\Response;

class ResponseFactory implements ResponseFactoryInterface {
	
	public function __construct() {}
	
	public function __destruct() {}
	
	public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface {
		return (new Response())->withStatus($code, $reasonPhrase);
	}
	
}
