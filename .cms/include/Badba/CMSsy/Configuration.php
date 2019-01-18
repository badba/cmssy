<?php
namespace Badba\CMSsy;

use Psr\Container\ContainerInterface;

class Configuration implements ContainerInterface {
	
	private $values;
	
	public function __construct($source) {
		$this->values = $source;
	}
	
	function __destruct() {
		$this->values = null;
	}
	
	public function get($id) {
		return $this->values[$id] ?? null;
	}

	public function has($id) {
		return isset($this->values[$id]);
	}
	
	public function set($id, $value) {
		$this->values[$id] = $value;
	}

}
