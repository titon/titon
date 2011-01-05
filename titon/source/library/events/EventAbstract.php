<?php
/**
 * A skeleton abstract for all Events to implement to inherit the prototype and config functionality.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\modules\events;

use \titon\core\Prototype;
use \titon\modules\events\EventInterface;

/**
 * Event Abstract
 *
 * @package		Titon
 * @subpackage	Titon.Modules.Events
 */
class EventAbstract extends Prototype implements EventInterface {
	
}