<?php

namespace Badba\CMSsy\Exceptions;

class MustRedirectException extends \RuntimeException {
	
	private $redirectLocation;
	private $redirectCode;
	
	public function __construct($location, $code = null) {
		$code = $code ?? 303;
		$this->redirectLocation = $location;
		$this->redirectCode = $code;
		parent::__construct();
	}
	
	public function getRedirectLocation() {
		return $this->redirectLocation;
	}
	
	public function getRedirectCode() {
		return $this->redirectCode;
	}
	
}

