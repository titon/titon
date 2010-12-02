<?php
/**
 * The Action is a sub-routine of the Controller parent and is packaged as a stand-alone object instead of a method.
 * An Action object gives you the flexibility of re-using actions and specific logic across multiple
 * Controllers, encapsulating additional methods within the Action process, and defining its own attachments.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\system;

use \titon\core\Prototype;
use \titon\log\Exception;

/**
 * Action Class
 *
 * @package     Titon
 * @subpackage  Titon.System
 */
class Action extends Prototype {

    /**
     * Controller object.
     *
     * @access protected
     * @var Controller
     */
    protected $Controller;

    /**
     * Store the parent Controller.
     *
     * @access public
     * @param Controller $Controller
     * @return void
     */
    public function setController(Controller $Controller) {
        $this->Controller = $Controller;
    }

    /**
     * The primary method that is executed for an Action.
     *
     * @access public
     * @return void
     */
    public function run() {
        throw new Exception('You must define the run() method within your Action.');
    }

}