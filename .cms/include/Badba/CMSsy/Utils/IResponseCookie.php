<?php

namespace Badba\CMSsy\Utils;

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
 * @method self withValue(?string $value = null)
 * @method int resolveExpires(int|\DateTimeInterface|string|null $expires)
 * @method self withExpires(int|string|\DateTimeInterface|null $expires)
 * @method self withMaxAge(?int $maxAge = null)
 * @method self withDomain(?string $domain = null)
 * @method self withPath(?string $path = null)
 * @method self withSecure(bool $secure = true)
 * @method self withHttpOnly(bool $httpOnly = true)
 * @method self withSameSite(SameSite $sameSite)
 * @method self withHttpOnly(bool $httpOnly = true)
 * @method self withSameSite(object $sameSite)
 * @method self withoutSameSite()
 */
interface IResponseCookie {
	
}
