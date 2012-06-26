<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

use titon\Titon;
use titon\libs\translators\messages\MessageTranslator;
use titon\libs\storage\cache\MemoryStorage;
use titon\libs\readers\core\PhpReader;
use titon\libs\readers\core\IniReader;
use titon\libs\readers\core\XmlReader;
use titon\libs\readers\core\JsonReader;
use titon\libs\readers\gettext\PoReader;

/**
 * Test class for titon\libs\translators\messages\MessageTranslator.
 */
class MessageTranslatorTest extends \PHPUnit_Framework_TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'ex-no,ex;q=0.5';

		Titon::g11n()->setup('ex')->setup('en');
	}

	/**
	 * Test reading keys from php message bundles.
	 */
	public function testPhpMessages() {
		$object = new MessageTranslator();
		$object->setReader(new PhpReader());
		$object->setStorage(new MemoryStorage());

		Titon::g11n()->setTranslator($object)->set('ex');

		$this->assertEquals('Titon', $object->getMessage('default.titon'));
		$this->assertEquals('Test', $object->getMessage('default.test'));
		$this->assertEquals('php', $object->getMessage('default.type'));

		Titon::g11n()->set('en');

		$this->assertEquals('Titon', $object->translate('default.titon'));
		$this->assertEquals('Test', $object->translate('default.test'));
		$this->assertEquals('php', $object->translate('default.type'));
		$this->assertEquals('1,337 health, 666 energy, 255 damage', $object->translate('default.format', array(1337, 666, 255)));
	}

	/**
	 * Test reading keys from ini message bundles.
	 */
	public function testIniMessages() {
		$object = new MessageTranslator();
		$object->setReader(new IniReader());
		$object->setStorage(new MemoryStorage());

		Titon::g11n()->setTranslator($object)->set('ex');

		$this->assertEquals('Titon', $object->getMessage('default.titon'));
		$this->assertEquals('Test', $object->getMessage('default.test'));
		$this->assertEquals('ini', $object->getMessage('default.type'));

		Titon::g11n()->set('en');

		$this->assertEquals('Titon', $object->translate('default.titon'));
		$this->assertEquals('Test', $object->translate('default.test'));
		$this->assertEquals('ini', $object->translate('default.type'));
		$this->assertEquals('1,337 health, 666 energy, 255 damage', $object->translate('default.format', array(1337, 666, 255)));
	}

	/**
	 * Test reading keys from xml message bundles.
	 */
	public function testXmlMessages() {
		$object = new MessageTranslator();
		$object->setReader(new XmlReader());
		$object->setStorage(new MemoryStorage());

		Titon::g11n()->setTranslator($object)->set('ex');

		$this->assertEquals('Titon', $object->getMessage('default.titon'));
		$this->assertEquals('Test', $object->getMessage('default.test'));
		$this->assertEquals('xml', $object->getMessage('default.type'));

		Titon::g11n()->set('en');

		$this->assertEquals('Titon', $object->translate('default.titon'));
		$this->assertEquals('Test', $object->translate('default.test'));
		$this->assertEquals('xml', $object->translate('default.type'));
		$this->assertEquals('1,337 health, 666 energy, 255 damage', $object->translate('default.format', array(1337, 666, 255)));
	}

	/**
	 * Test reading keys from json message bundles.
	 */
	public function testJsonMessages() {
		$object = new MessageTranslator();
		$object->setReader(new JsonReader());
		$object->setStorage(new MemoryStorage());

		Titon::g11n()->setTranslator($object)->set('ex');

		$this->assertEquals('Titon', $object->getMessage('default.titon'));
		$this->assertEquals('Test', $object->getMessage('default.test'));
		$this->assertEquals('json', $object->getMessage('default.type'));

		Titon::g11n()->set('en');

		$this->assertEquals('Titon', $object->translate('default.titon'));
		$this->assertEquals('Test', $object->translate('default.test'));
		$this->assertEquals('json', $object->translate('default.type'));
		$this->assertEquals('1,337 health, 666 energy, 255 damage', $object->translate('default.format', array(1337, 666, 255)));
	}

	/**
	 * Test reading keys from po message bundles.
	 */
	public function testPoMessages() {
		$object = new MessageTranslator();
		$object->setReader(new PoReader());
		$object->setStorage(new MemoryStorage());

		Titon::g11n()->setTranslator($object)->set('ex');

		$this->assertEquals('Basic message', $object->getMessage('default.basic'));
		$this->assertEquals('Context message', $object->getMessage('default.context'));
		$this->assertEquals("Multiline message\nMore message here\nAnd more message again", $object->getMessage('default.multiline'));

		Titon::g11n()->set('en');

		$this->assertEquals('1,337 health, 666 energy, 255 damage', $object->translate('default.format', array(1337, 666, 255)));
	}

}