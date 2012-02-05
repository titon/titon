<?php

namespace titon\data;

use \titon\base\Base;
use \titon\libs\traits\Prototype;

class Model extends Base {
	use Prototype;

	/*public $oneToOne = array(); // has one, a user has one profile

	public $oneToMany = array(); // has many, a user has many comments

	public $manyToOne = array(); // belongs to, a user belongs to a country

	public $manyToMany = array(); // HABTM, a user belongs to many groups*/

    protected $_config = array(
        'name'      => '',
        'table'     => '',
        'prefix'    => '',
        'database'  => 'default',
        'cache'     => false
    );

    protected $_db;

    protected $_schema = array();

    public function __construct(array $config = array()) {
        parent::__construct($config);

        if (empty($this->_config['name'])) {
            $this->_config['name'] = __CLASS__;
        }

        if (empty($this->_config['table'])) {
            $this->_config['table'] = Inflector::tableize(__CLASS__);
        }

        $this->_db = Database::getDriver($this->_config['database']);
    }

    protected function _create(array $options = array()) {
        return $this->_db->query($sql);
    }

    protected function _delete($id, array $options = array()) {
        return $this->_db->query($sql);
    }

    protected function _read($id, array $options = array()) {
        return $this->_db->query($sql);
    }

    protected function _update($id, array $options = array()) {
        return $this->_db->query($sql);
    }

}
