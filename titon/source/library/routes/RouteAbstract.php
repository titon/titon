<?php

namespace titon\source\core\routes;

use \titon\source\core\routes\RouteInterface;

abstract class RouteAbstract implements RouteInterface {

	private $__path;

	private $__route;
	
	public function __construct($path, array $route) {

	}

	public function match() {

	}

	public function path() {
		
	}

}