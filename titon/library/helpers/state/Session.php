<?php
/**
 * A Session helper that utilizes only basic functionality from the Session package.
 * Is primarily used for outputting data from the session into the view.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\modules\helpers\state;

use \titon\modules\helpers\HelperAbstract;

/**
 * Session Helper
 *
 * @package		Titon
 * @subpackage	Titon.Modules.Helpers
 */
class Session extends HelperAbstract {

    /**
	 * Classes important for this object to operate properly.
	 *
	 * @access protected
	 * @var array
	 */
    protected $_classes = array(
        'State.Session' => '\titon\state\Session'
    );
    
    /**
	 * Initialize the core Session class, if it was not within the Controller.
	 *
	 * @access public
     * @param obj $Engine
	 * @return void
	 */
    public function initialize($Engine) {
        parent::initialize($Engine);

        $this->Session->initialize();
    }

    /**
	 * Check to see if a certain key/path exist in the session.
	 *
	 * @access public
	 * @param string $key
	 * @return bool
	 */
	public function check($key) {
        return $this->Session->check($key);
    }

    /**
	 * Get a certain value from the session based on the key/path.
	 *
	 * @access public
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
        return $this->Session->get($key);
    }

    /**
	 * Returns the current session ID. If no ID is found, regenerate one.
	 *
	 * @access public
	 * @return int
	 */
	public function id() {
        return $this->Session->id();
    }

}