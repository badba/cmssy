<?php

namespace Badba\CMSsy;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Jasny\HttpMessage\ServerRequest;
use Psr\Http\Message\UriInterface;
use Jasny\HttpMessage\Uri;

class ServerRequestFactory implements ServerRequestFactoryInterface {
	
	public function __construct() {}
	
	public function __destruct() {}
	
	public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface {
		if (($method === '') && ($uri === '')) {
			return (new ServerRequest())->withGlobalEnvironment();
		} else {
			return (new ServerRequest())->withServerParams($serverParams)->withMethod($method)->withUri($uri instanceof UriInterface ? $uri : new Uri($uri));
		}
	}
	
}
