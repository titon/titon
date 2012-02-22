<?php

namespace titon\g11n;

use \titon\g11n\Resolver;

class Message {
	
	public function translate($key, array $params = array()) {	
		$locale = Titon::get('g11n')->current();
		$format = new MessageFormatter($locale['locale'], Resolver::get($key));
		
		return $format->format($params);
	}
	
}