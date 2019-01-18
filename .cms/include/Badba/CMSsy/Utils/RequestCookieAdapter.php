<?php

namespace Badba\CMSsy\Utils;

use Dflydev\FigCookies\Cookie;

/**
 * @method string getName();
 * @method ?string getValue();
 * @method IRequestCookie withValue(?string $value = null)
 */
class RequestCookieAdapter implements IRequestCookie {
	
	/** @var Cookie $adaptee; */
	private $adaptee;
	
	public function __construct(Cookie $adaptee) {
		$this->adaptee = $adaptee;
	}
	
	public function __call($name, $args) {
		$result = $this->adaptee->{$name}(... $args);
		if ($result instanceof Cookie) {
			$result = new self($result);
		}
		return $result;
	}
	
	public function __destruct() {}
	
	public function getAdaptee() {
		return $this->adaptee;
	}
}
