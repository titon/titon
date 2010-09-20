<?php
/**
 * A required interface for all Helpers to implement.
 * Defines the callbacks and the arguments that are available to the Helper.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\modules\helpers;

use \titon\system\View;

/**
 * Helper Interface
 *
 * @package		Titon
 * @subpackage	Titon.Modules
 */
interface HelperInterface {

    /**
	 * Triggered upon the view class instantiation, following __construct().
	 *
	 * @access public
     * @param View $View
	 * @return void
	 */
    public function initialize(View $View);

    /**
	 * Triggered before a template is rendered by the engine.
	 *
	 * @access public
     * @param View $View
	 * @return void
	 */
    public function preRender(View $View);

    /**
	 * Triggered after a template is rendered by the engine.
	 *
	 * @access public
     * @param View $View
	 * @return void
	 */
    public function postRender(View $View);

}