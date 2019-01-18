<?php

namespace Badba\CMSsy\Utils;

use Dflydev\FigCookies\FigRequestCookies;
use Psr\Http\Message\RequestInterface;

class RequestCookieHelper {
	
	static public function getCookie(RequestInterface $request, string $name, $defaultValue = null): IRequestCookie {
		return new RequestCookieAdapter(FigRequestCookies::get($request, $name, $defaultValue));
	}
	
	static public function withCookie(RequestInterface $request, IRequestCookie $requestCookie): RequestInterface {
		/** @var RequestCookieAdapter $requestCookie */
		return FigRequestCookies::set($request, $requestCookie->getAdaptee());
	}
	
	static public function withoutCookie(RequestInterface $request, string $name): RequestInterface {
		return FigRequestCookies::remove($request, $name);
	}
	
}
