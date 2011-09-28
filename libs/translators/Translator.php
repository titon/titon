<?php

namespace titon\libs\translators;

interface Translator {
	
	public function getMessage($key);
	
	public function hasMessage($key);
	
	public function loadFile($module, $domain);
	
	public function parseKey($key);
	
	public function translate($key, array $params);
	
}