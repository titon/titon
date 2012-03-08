<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

include_once dirname(dirname(__DIR__)) . '/bootstrap.php';

/**
 * Test class for \titon\core\Config.
 */
class ConfigTest extends \PHPUnit_Framework_TestCase {

	public $app = array(
		'name' => 'Titon',
		'salt' => '66c63d989368170aff46040ab2353923',
		'seed' => 'nsdASDn7012dn1dsjSa',
		'encoding' => 'UTF-8'
	);

	public $debug = array(
		'level' => 2,
		'email' => ''
	);

	public $test = array(
		'integer' => 1234567890,
		'number' => '1234567890',
		'character' => 'abcdefg',
		'emptyArray' => array(),
		'array' => array(
			'one' => true,
			'two' => false,
		),
		'false' => false,
		'true' => true,
		'null' => null,
		'zero' => 0
	);

	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = \titon\Titon::config();
		$this->object->set('App', $this->app);
		$this->object->set('Debug', $this->debug);
		$this->object->set('Test', $this->test);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
	}

	/**
	 * Test that the encoding is set and returns the correct value; default UTF-8 if empty.
	 */
	public function testEncoding() {
		$this->assertEquals($this->object->encoding(), 'UTF-8');

		$this->object->set('App.encoding', 'UTF-16');
		$this->assertEquals($this->object->encoding(), 'UTF-16');

		$this->object->set('App.encoding', '');
		$this->assertEquals($this->object->encoding(), 'UTF-8');
	}

	/**
	 * Test that get() returns the correct values and types.
	 */
	public function testGet() {
		$this->assertEquals($this->object->get('App.name'), $this->app['name']);
		$this->assertEquals($this->object->get('App.seed'), $this->app['seed']);

		$this->assertEquals($this->object->get('Debug'), $this->debug);
		$this->assertEquals($this->object->get('Debug.level'), $this->debug['level']);

		$this->assertTrue(is_integer($this->object->get('Test.integer')));
		$this->assertTrue(is_numeric($this->object->get('Test.number')));
		$this->assertTrue(is_string($this->object->get('Test.character')));
		$empty = $this->object->get('Test.emptyArray');
		$this->assertTrue(empty($empty));
		$this->assertTrue(is_array($this->object->get('Test.array')));
		$this->assertTrue($this->object->get('Test.array.one') === true);
		$this->assertTrue($this->object->get('Test.false') === false);
		$this->assertTrue($this->object->get('Test.true') === true);
		$this->assertTrue($this->object->get('Test.zero') === 0);
		$this->assertTrue($this->object->get('Test.fakeKey') === null);

		$this->assertEquals($this->object->get('Test.character'), $this->test['character']);
	}

	/**
	 * Test that has() returns a true or false statement.
	 */
	public function testHas() {
		$this->assertTrue($this->object->has('App.salt'));
		$this->assertTrue($this->object->has('Debug.email'));
		$this->assertTrue($this->object->has('Test.number'));
		$this->assertTrue($this->object->has('Test.true'));
		$this->assertTrue($this->object->has('Test.false'));
		$this->assertTrue($this->object->has('Test.zero'));

		$this->assertFalse($this->object->has('App.id'));
		$this->assertFalse($this->object->has('Debug.id'));
		$this->assertFalse($this->object->has('Test.fakeKey'));
		$this->assertFalse($this->object->has('Test.deep.deep.deep.deep.array'));
	}

	/**
	 * Test that loading a config set works correctly.
	 * Config file php.php is found within the app/config/sets/php.php file and uses the data from $test.
	 */
	public function testLoad() {
		$reader = new \titon\libs\readers\core\PhpReader('php');

		$this->object->load('Php', $reader);
		$this->assertArrayHasKey('Php', $this->object->get());

		$data = $this->object->get('Php');
		unset($data['initialize']);
		$this->assertEquals($data, $this->test);
	}

	/**
	 * Test that name() returns the correct App.name.
	 */
	public function testName() {
		$this->assertEquals($this->object->name(), $this->app['name']);

		$this->object->set('App.name', 'TestName');
		$this->assertEquals($this->object->name(), 'TestName');

		$this->object->set('App.name', '');
		$this->assertEquals($this->object->name(), '');
	}

	/**
	 * Test that salt() returns the correct App.salt.
	 */
	public function testSalt() {
		$this->assertEquals($this->object->salt(), $this->app['salt']);

		$this->object->set('App.salt', md5('TestSalt'));
		$this->assertEquals($this->object->salt(), md5('TestSalt'));

		$this->object->set('App.salt', '');
		$this->assertEquals($this->object->salt(), '');
	}

	/**
	 * Test that set() correctly sets values at the correct depths.
	 */
	public function testSet() {
		$this->object->set('Set.level1', 1);
		$this->assertEquals($this->object->get('Set.level1'), 1);

		$this->object->set('Set.level2.level2', 2);
		$this->assertEquals($this->object->get('Set.level2.level2'), 2);

		$this->object->set('Set.level3.level3.level3', 3);
		$this->assertEquals($this->object->get('Set.level3.level3.level3'), 3);

		$this->object->set('Set.level4.level4.level4.level4', 4);
		$this->assertEquals($this->object->get('Set.level4.level4.level4.level4'), 4);
		$this->assertTrue(is_array($this->object->get('Set.level4.level4.level4')));
		$this->assertFalse($this->object->get('Set.level4.level4') === 'falsey');

		$this->object->set('Set.level4.array', array('key' => 'value'));
		$this->assertEquals($this->object->get('Set.level4.array'), array('key' => 'value'));
		$this->assertEquals($this->object->get('Set.level4.array.key'), 'value');

		$this->object->set('Set.level4', 'Flattened!');
		$this->assertEquals($this->object->get('Set.level4'), 'Flattened!');
		$this->assertEquals($this->object->get('Set.level4.level4.level4.level4'), null);
	}

}