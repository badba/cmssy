<?php

namespace Badba\CMSsy;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class FrontController {
	
	static public function run() {
		$config = new Configuration(\json_decode(\file_get_contents('../.custom/config.json'), true));
		$factoryLocator = new FactoryLocator([
			StreamInterface::class => function() {
				return new StreamFactory();
			},
			UriInterface::class => function() {
				return new UriFactory();
			},
			ServerRequestInterface::class => function() {
				return new ServerRequestFactory();
			},
			ResponseInterface::class => function() {
				return new ResponseFactory();
			},
			IServerResponseEmitter::class => function() {
				return new ServerResponseEmitter();
			}
		]);
		$factoryLocator->get(IServerResponseEmitter::class)->emit(
			(new ServerRequestHandler($config, $factoryLocator))->handle(
				$factoryLocator->get(ServerRequestInterface::class)->createServerRequest('', '')));
	}
}
