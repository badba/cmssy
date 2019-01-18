<?php

namespace Badba\CMSsy;

use Psr\Http\Message\ResponseInterface;
use Jasny\HttpMessage\Emitter;

class ServerResponseEmitter implements IServerResponseEmitter {
	
	public function emit(ResponseInterface $response): void {
		(new Emitter())->emit($response);
	}
}
