<?php

namespace Badba\CMSsy\Utils;

use Dflydev\FigCookies\SetCookie;

/**
 * @method string getName()
 * @method ?string getValue()
 * @method ?string getPath()
 * @method int getExpires()
 * @method int getMaxAge()
 * @method ?string getPath()
 * @method ?string getDomain()
 * @method bool getSecure()
 * @method bool getHttpOnly()
 * @method ?object getSameSite()
 * @method IResponseCookie withValue(?string $value = null)
 * @method int resolveExpires(int|\DateTimeInterface|string|null $expires)
 * @method IResponseCookie withExpires(int|string|\DateTimeInterface|null $expires)
 * @method IResponseCookie withMaxAge(?int $maxAge = null)
 * @method IResponseCookie withDomain(?string $domain = null)
 * @method IResponseCookie withPath(?string $path = null)
 * @method IResponseCookie withSecure(bool $secure = true)
 * @method IResponseCookie withHttpOnly(bool $httpOnly = true)
 * @method IResponseCookie withSameSite(SameSite $sameSite)
 * @method IResponseCookie withHttpOnly(bool $httpOnly = true)
 * @method IResponseCookie withSameSite(object $sameSite)
 * @method IResponseCookie withoutSameSite()
 */
class ResponseCookieAdapter implements IResponseCookie {
	
	/** @var SetCookie $adaptee; */
	private $adaptee;
	
	public function __construct(SetCookie $adaptee) {
		$this->adaptee = $adaptee;
	}
	
	public function __call($name, $args) {
		$result = $this->adaptee->{$name}(... $args);
		if ($result instanceof SetCookie) {
			$result = new self($result);
		}
		return $result;
	}
	
	public function __destruct() {}
	
	public function getAdaptee() {
		return $this->adaptee;
	}
	
}
