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
 * Enum for months of the year.
 *
 * @package	titon.libs.enums
 */
class Month extends Enum {

	/**
	 * Constants.
	 */
	const JANUARY = 0;
	const FEBRUARY = 1;
	const MARCH = 2;
	const APRIL = 3;
	const MAY = 4;
	const JUNE = 5;
	const JULY = 6;
	const AUGUST = 7;
	const SEPTEMBER = 8;
	const OCTOBER = 9;
	const NOVEMBER = 10;
	const DECEMBER = 11;

	/**
	 * Initialize mappings.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_enums = [
		self::JANUARY	=> ['january', 31],
		self::FEBRUARY	=> ['february', 28],
		self::MARCH		=> ['march', 31],
		self::APRIL 	=> ['april', 30],
		self::MAY		=> ['may', 31],
		self::JUNE		=> ['june', 30],
		self::JULY		=> ['july', 31],
		self::AUGUST	=> ['august', 31],
		self::SEPTEMBER => ['september', 30],
		self::OCTOBER	=> ['october', 31],
		self::NOVEMBER	=> ['november', 30],
		self::DECEMBER	=> ['december', 31]
	];

	/**
	 * Month of the year.
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
	 * Month as a slug.
	 *
	 * @access public
	 * @var string
	 */
	public $slug;

	/**
	 * How many days in the month.
	 *
	 * @access public
	 * @var int
	 */
	public $daysInMonth;

	/**
	 * Set variables.
	 *
	 * @access public
	 * @param string $slug
	 * @return void
	 */
	public function initialize($slug) {
		$month = $this->value() + 1;
		$time = mktime(0, 0, 0, $month, 1);

		$this->order = $month;
		$this->slug = $slug;
		$this->name = strftime('%B', $time);
		$this->shortName = strftime('%b', $time);
		$this->daysInMonth = date('t', $time);
	}

}