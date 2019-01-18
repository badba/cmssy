<?php

namespace Badba\CMSsy;

use Psr\Container\ContainerInterface;

class FactoryLocator implements ContainerInterface {
	
	/** @var callable[] $constructor */
	private $constructors;
	
	/** @var array $instances */
	private $instances;
	
	public function __construct($constructors) {
		$this->constructors = $constructors;
		$this->instances = [];
	}
	
	public function __destruct() {}
	
	public function get($id) {
		if (! $this->has($id)) {
			throw new FactoryNotFoundException();
		}
		if (! isset($this->instances[$id])) {
			$this->instances[$id] = ($this->constructors[$id])($id);
		}
		return $this->instances[$id];
	}
	
	public function has($id) {
		return isset($this->constructors[$id]);
	}
	
}
