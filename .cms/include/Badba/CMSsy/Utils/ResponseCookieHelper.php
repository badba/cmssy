<?php

namespace Badba\CMSsy\Utils;

use Dflydev\FigCookies\FigResponseCookies;
use Psr\Http\Message\ResponseInterface;

class ResponseCookieHelper {
	
	static public function getCookie(ResponseInterface $request, string $name, $defaultValue = null): IResponseCookie {
		return new ResponseCookieAdapter(FigResponseCookies::get($request, $name, $defaultValue));
	}
	
	static public function withCookie(ResponseInterface $request, IResponseCookie $requestCookie): ResponseInterface {
		/** @var RequestCookieAdapter $requestCookie */
		return FigResponseCookies::set($request, $requestCookie->getAdaptee());
	}
	
	static public function withoutCookie(ResponseInterface $request, string $name): ResponseInterface {
		return FigResponseCookies::remove($request, $name);
	}
	
	static public function withExpiredCookie(ResponseInterface $request, string $name): ResponseInterface {
		return FigResponseCookies::expire($request, $name);
	}
	
}
