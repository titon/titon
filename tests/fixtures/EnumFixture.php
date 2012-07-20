<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\fixtures;

use titon\base\Enum;

/**
 * Fixture for titon\base\Enum.
 *
 * @package	titon.tests.fixtures
 */
class EnumFixture extends Enum {

	const WARRIOR = 0;
	const RANGER = 1;
	const MAGE = 2;

	public $name;
	public $melee;
	public $health;
	public $energy;

	protected $_enums = [
		self::WARRIOR => ['Warrior', true, 1000],
		self::RANGER => ['Ranger', false, 500, 250],
		self::MAGE => ['Mage', false, 300, 600]
	];

	public function initialize($name, $melee, $health, $energy = 0) {
		$this->name = $name;
		$this->melee = $melee;
		$this->health = $health;
		$this->energy = $energy;
	}

}