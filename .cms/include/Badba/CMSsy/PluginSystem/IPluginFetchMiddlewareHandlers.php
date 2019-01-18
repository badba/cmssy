<?php

namespace Badba\CMSsy\PluginSystem;

interface IPluginFetchMiddlewareHandlers {
	
	public function fetchMiddlewareHandlers($prevResult): array;
	
}
