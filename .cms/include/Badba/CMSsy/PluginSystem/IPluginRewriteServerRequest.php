<?php

namespace Badba\CMSsy\PluginSystem;

use Psr\Http\Message\ServerRequestInterface;

interface IPluginRewriteServerRequest {
	
	public function rewriteServerRequest($prevResult, ServerRequestInterface $request): ServerRequestInterface;
	
}
