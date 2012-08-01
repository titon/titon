<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\enums;

use titon\base\Enum;

/**
 * Enum for days of the week.
 *
 * @package	titon.libs.enums
 */
class Day extends Enum {

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
	protected $_enums = [
		self::SUNDAY	=> ['sunday'],
		self::MONDAY	=> ['monday'],
		self::TUESDAY	=> ['tuesday'],
		self::WEDNESDAY => ['wednesday'],
		self::THURSDAY	=> ['thursday'],
		self::FRIDAY	=> ['friday'],
		self::SATURDAY	=> ['saturday']
	];

	/**
	 * Day of the week; Sunday is first.
	 *
	 * @access public
	 * @var int
	 */
	public $order;

	/**
	 * Localized name.
	 *
	 * @access public
	 * @var string
	 */
	public $name;

	/**
	 * Localized shorthand name.
	 *
	 * @access public
	 * @var string
	 */
	public $shortName;

	/**
	 * Day as a slug.
	 *
	 * @access public
	 * @var string
	 */
	public $slug;

	/**
	 * Current day of the year.
	 *
	 * @access public
	 * @var int
	 */
	public $dayOfYear;

	/**
	 * Set variables.
	 *
	 * @access public
	 * @param string $slug
	 * @return void
	 */
	public function initialize($slug) {
		$day = $this->value() + 1;
		$time = mktime(0, 0, 0, date('n'), $day);

		$this->order = $this->value();
		$this->slug = $slug;
		$this->name = strftime('%A', $time);
		$this->shortName = strftime('%a', $time);
		$this->dayOfYear = date('z', $time);
	}

}