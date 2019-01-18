<?php

namespace Badba\CMSsy\PluginSystem;

interface IPlugin {
	
	public function initialize($owner): void;
	
	public function finalize(): void;
	
}
