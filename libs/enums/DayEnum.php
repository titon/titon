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
 * Enum for days of the week.
 * 
 * @package	titon.libs.enums
 */
class DayEnum extends Enum {
	
	/**
	 * Constants.
	 */
	const SUNDAY = 0;
	const MONDAY = 1;
	const TUESDAY = 2;
	const WEDNESDAY = 3;
	const THURSDAY = 4;
	const FRIDAY = 5;
	const SATURDAY = 6;
	
	/**
	 * Initialize mappings.
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_enums = array(
		self::SUNDAY	=> array(1, 'sunday'),
		self::MONDAY	=> array(2, 'monday'),
		self::TUESDAY	=> array(3, 'tuesday'),
		self::WEDNESDAY => array(4, 'wednesday'),
		self::THURSDAY	=> array(5, 'thursday'),
		self::FRIDAY	=> array(6, 'friday'),	
		self::SATURDAY	=> array(7, 'saturday')
	);
	
	/**
	 * Day of the week; Sunday is first.
	 * 
	 * @access public
	 * @var int
	 */
	public $day;
	
	/**
	 * Localized name.
	 * 
	 * @access public
	 * @var string
	 */
	public $name;

	/**
	 * Day as a slug.
	 *
	 * @access public
	 * @var string
	 */
	public $slug;
	
	/**
	 * Set variables.
	 * 
	 * @access public
	 * @param int $day
	 * @param string $slug 
	 * @return void
	 */
	public function initialize($day, $slug) {
		$this->day = $day;
		$this->slug = $slug;
		$this->name = __('common.' . $slug);
	}
	
}