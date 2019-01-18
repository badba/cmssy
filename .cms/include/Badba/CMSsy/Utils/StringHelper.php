<?php

namespace Badba\CMSsy\Utils;

class StringHelper {
	
	static public function strEsc($value) {
		return $value === null ? null : \htmlspecialchars($value ?? '', \ENT_COMPAT, 'UTF-8', true);
	}
	
}
