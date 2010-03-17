<?php

namespace titon\data;

class Database {

    private $__connections = array();

    private $__driver;

    public static function getDriver() {

    }

    public static function setDriver() {
        
    }

    /**
     * Setup a database connection configuration.
     *
     * @access public
     * @param string $database
     * @param array $config
     *      - driver: Which database driver to use
     *      - persist: Should the database connection be persistent
     *      - host: The domain host for the database
     *      - user: The user to connect to the database
     *      - pass: The password for the user
     *      - database: The name of the database
     *      - encoding: The charset encoding to use
     * @return void
     * @static
     */
    public static function setup($database, array $config = array()) {
        $defaults = array(
            'driver'    => 'mysql',
            'persist'   => true,
            'encoding'  => 'UTF-8'
        );

        self::$__connections[(string)$database] = array_merge($defaults, $config);
    }


}