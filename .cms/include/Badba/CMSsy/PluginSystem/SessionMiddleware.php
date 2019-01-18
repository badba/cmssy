<?php

namespace Badba\CMSsy\PluginSystem;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionMiddleware implements MiddlewareInterface {
	
	public function __construct() {}
	
	public function __destruct() {}
	
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
		$getSessionId = $request->getAttribute('getSessionId', null);
		$sessionId = null;
		if ($getSessionId !== null) {
			$request = $request->withAttribute('closeSession', function() {
				\session_destroy();
			});
			$sessionId = $getSessionId();
			if ($sessionId !== null) {
				\session_id($sessionId);
			}
			\session_start(array(
				'use_cookies' => '0',
				'use_only_cookies' => '1',
				'cache_limiter' => ''
			));
			if ($sessionId === null) {
				$sessionId = \session_id();
			}
		}
		$result = $handler->handle($request);
		if ($sessionId !== null) {
			\session_write_close();
			//$result = $result->with
			
		}
		return $result;
	}
}
