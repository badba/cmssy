<?php

namespace Badba\CMSsy;

use Psr\Http\Message\ResponseInterface;

interface IServerResponseEmitter {
	
	public function emit(ResponseInterface $response): void;
	
}
