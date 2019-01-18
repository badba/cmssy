<?php

namespace Badba\CMSsy;

interface IPageContext {
	
	public function getVars();
	
	public function getOwner();
	
	public function varGet(string $name);
	
	public function varSet(string $name, $value, $mode = null);
	
	public function varIsSet(string $name);
	
	public function valueCapture(callable $handler);
	
	public function callBlock(string $blockName, ?array $blockArgs = []);
	
	public function strEsc($value);
	
}
