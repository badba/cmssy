<?php

namespace Badba\CMSsy\PluginSystem;

use Badba\CMSsy\ServerRequestHandler;

class BasePlugin implements IPlugin {
	
	private $owner;
	
	public function finalize(): void {}

	public function initialize($owner): void {
		$this->owner = $owner;
	}
	
	public function getOwner(): ServerRequestHandler {
		return $this->owner;
	}

}
