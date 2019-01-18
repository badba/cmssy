<?php

namespace Badba\CMSsy;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Badba\CMSsy\Utils\StreamHelper;
use Jasny\HttpMessage\Stream;

class StreamFactory implements StreamFactoryInterface {
	
	public function __construct() {}
	
	public function createStream(string $content = ''): StreamInterface {
		return new Stream(StreamHelper::createStreamFromString($content));
	}
	
	public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface {
		return new Stream(\fopen($filename, $mode));
	}
	
	public function createStreamFromResource($resource): StreamInterface {
		return new Stream($resource);
	}
	
}
