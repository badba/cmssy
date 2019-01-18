<?php

namespace Badba\CMSsy\Utils;

class StreamHelper {
	
	public static function createStreamFromString(string $source) {
		$stream = \fopen('php://memory','r+');
		\fwrite($stream, $source);
		\rewind($stream);
		return $stream;
	}
	
}
