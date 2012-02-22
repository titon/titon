<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\enums;

use \titon\base\types\Enum;

/**
 * Enum for primary and secondary colors.
 * 
 * @package	titon.libs.enums
 */
class ColorEnum extends Enum {
	
	/**
	 * Constants.
	 */
	const BLACK = 0;
	const WHITE = 1;
	const YELLOW = 2;
	const GREEN = 3;
	const BLUE = 4;
	const RED = 5;
	const ORANGE = 6;
	const PURPLE = 7;
	const PINK = 8;
	const GRAY = 9;
	const BROWN = 10;
	const TEAL = 10;
	
	/**
	 * Initialize mappings.
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_enums = array(
		self::BLACK		=> array('000000', 0, 0, 0),
		self::WHITE		=> array('FFFFFF', 255, 255, 255),
		self::YELLOW	=> array('FFFF00', 255, 255, 0),
		self::GREEN		=> array('00FF00', 0, 255, 0),
		self::BLUE		=> array('0000FF', 0, 0, 255),
		self::RED		=> array('FF0000', 255, 0, 0),
		self::ORANGE	=> array('FF4500', 255, 69, 0),
		self::PURPLE	=> array('800080', 128, 0, 128),
		self::PINK		=> array('FF1493', 255, 20, 147),
		self::GRAY		=> array('808080', 128, 128, 128),
		self::BROWN		=> array('D2B48C', 210, 180, 140),
		self::TEAL		=> array('008080', 0, 128, 128)
	);
	
	/**
	 * Hex code.
	 * 
	 * @access public
	 * @var int
	 */
	public $hex;
	
	/**
	 * Red value.
	 * 
	 * @access public
	 * @var string
	 */
	public $r;
	
	/**
	 * Green value.
	 * 
	 * @access public
	 * @var string
	 */
	public $g;
	
	/**
	 * Blue value.
	 * 
	 * @access public
	 * @var string
	 */
	public $b;
	
	/**
	 * Set variables.
	 * 
	 * @access public
	 * @param string $hex
	 * @param int $r
	 * @param int $g
	 * @param int $b
	 * @return void
	 */
	public function initialize($hex, $r, $g, $b) {
		$this->hex = $hex;
		$this->r = $r;
		$this->g = $g;
		$this->b = $b;
	}
	
}