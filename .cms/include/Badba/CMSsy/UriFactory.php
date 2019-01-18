<?php

namespace Badba\CMSsy;

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Jasny\HttpMessage\Uri;

class UriFactory implements UriFactoryInterface {
	
	public function __construct() {}
	
	public function __destruct() {}
	
	public function createUri(string $uri = ''): UriInterface {
		return new Uri($uri);
	}
	
}
