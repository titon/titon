<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon;

use titon\io\Folder;
use titon\tests\TestCase;
use \Exception;

/**
 * Test class for titon\io\Folder.
 */
class FolderTest extends TestCase {

	/**
	 * Temp folder for testing.
	 */
	public $temp;

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$this->object = new Folder(APP_TEMP . 'io/');
		$this->temp = new Folder(APP_TEMP . 'io_temp/', false);
	}

	/**
	 * Reset folder state after each test.
	 */
	protected function tearDown() {
		$this->object->chmod(0777);
		$this->temp->delete();

		if (file_exists(APP_TEMP . 'io/.DS_Store')) {
			@unlink(APP_TEMP . 'io/.DS_Store');
		}
	}

	/**
	 * Test that __construct() throws exceptions.
	 */
	public function testConstruct() {
		try {
			$folder = new Folder(APP_TEMP . 'io/foo.php');
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}
	}

	/**
	 * Test that accessTime() returns the last access time.
	 */
	public function testAccessTime() {
		$this->assertTrue(is_int($this->object->accessTime()));
		$this->assertTrue(is_null($this->temp->accessTime()));
	}

	/**
	 * Test that changeTime() returns the last change time.
	 */
	public function testChangeTime() {
		$this->assertTrue(is_int($this->object->changeTime()));
		$this->assertTrue(is_null($this->temp->changeTime()));
	}

	/**
	 * Test that create() creates the folder if it doesn't exist.
	 */
	public function testCreate() {
		$this->assertTrue($this->object->exists());
		$this->assertFalse($this->object->create());

		$this->assertFalse($this->temp->exists());
		$this->assertTrue($this->temp->create());
		$this->assertTrue($this->temp->exists());
	}

	/**
	 * Test that copy() will copy all the contents to a new location, and delete() will recursively delete the folder and contents.
	 */
	public function testCopyAndDelete() {
		$target = APP_TEMP . 'io_copy/';

		$this->assertFalse(file_exists($target));
		$this->assertFalse(file_exists($target . 'sub/bar.php'));

		// copy over stuff
		$folder = $this->object->copy($target);

		$this->assertInstanceOf('titon\io\Folder', $folder);
		$this->assertTrue(file_exists($target));
		$this->assertTrue(file_exists($target . 'sub/bar.php'));

		// try again without overwrite
		try {
			$this->object->copy($target, false);
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		// cleanup
		$folder->delete();
	}

	/**
	 * Test exists() returns true if the folder exists.
	 */
	public function testExists() {
		$this->assertTrue($this->object->exists());
		$this->assertFalse($this->temp->exists());

		$this->temp->create();
		$this->assertTrue($this->temp->exists());
	}

	/**
	 * Test find() will glob files and folders within the folder.
	 */
	public function testFind() {
		$files = $this->object->find('*.php');
		$this->assertTrue(count($files) > 0);

		$files = $this->object->find('*.ini');
		$this->assertTrue(count($files) <= 0);

		$files = $this->object->find('sub/*');
		$this->assertTrue(count($files) > 0);
	}

	/**
	 * Test folder() will return the parent folder as a Folder object.
	 */
	public function testFolder() {
		$parent =& $this->object->folder();
		$this->assertInstanceOf('titon\io\Folder', $parent);
		$this->assertEquals(APP_TEMP, $parent->path());
	}

	/**
	 * Test group() returns the group.
	 */
	public function testGroup() {
		$this->assertNotEquals(null, $this->object->group());
		$this->assertEquals(null, $this->temp->group());
	}

	/**
	 * Test isAbsolute() returns true if the path is absolute.
	 */
	public function testIsAbsolute() {
		$this->assertTrue($this->object->isAbsolute());
		$this->assertTrue($this->temp->isAbsolute());

		$folder = new Folder('C:/app/');
		$this->assertTrue($folder->isAbsolute());

		$folder = new Folder('../../some/folder/');
		$this->assertFalse($folder->isAbsolute());
	}

	/**
	 * Test isRelative() returns true if the path is relative.
	 */
	public function testIsRelative() {
		$this->assertFalse($this->object->isRelative());
		$this->assertFalse($this->temp->isRelative());

		$folder = new Folder('C:/app/');
		$this->assertFalse($folder->isRelative());

		$folder = new Folder('../../some/folder/');
		$this->assertTrue($folder->isRelative());
	}

	/**
	 * Test isWindows() returns true if the path is Windows file system.
	 */
	public function testisWindows() {
		$this->assertFalse($this->object->isWindows());
		$this->assertFalse($this->temp->isWindows());

		$folder = new Folder('C:/app/');
		$this->assertTrue($folder->isWindows());

		$folder = new Folder('//network/drive/');
		$this->assertTrue($folder->isWindows());

		$folder = new Folder('../../some/folder/');
		$this->assertFalse($folder->isWindows());
	}

	/**
	 * Test that modifiedTime() returns the last modified time.
	 */
	public function testModifiedTime() {
		$this->assertTrue(is_int($this->object->modifiedTime()));
		$this->assertTrue(is_null($this->temp->modifiedTime()));
	}

	/**
	 * Test that move() renames the directory.
	 */
	public function testMove() {
		$base = APP_TEMP . 'io/';
		$target = APP_TEMP . 'io_move/';

		$this->assertTrue(file_exists($base));
		$this->assertFalse(file_exists($target));

		$this->object->move($target);

		$this->assertFalse(file_exists($base));
		$this->assertTrue(file_exists($target));

		// move it back
		$this->object->move($base);
	}

	/**
	 * Test that name() returns the top folder name.
	 */
	public function testName() {
		$this->assertEquals('io', $this->object->name());
		$this->assertEquals('io_temp', $this->temp->name());
	}

	/**
	 * Test that owner() returns the owner.
	 */
	public function testOwner() {
		$this->assertNotEquals(null, $this->object->owner());
		$this->assertEquals(null, $this->temp->owner());
	}

	/**
	 * Test that path() returns the current path.
	 */
	public function testPath() {
		$this->assertEquals(APP_TEMP .'io/', $this->object->path());
		$this->assertEquals(APP_TEMP .'io_temp/', $this->temp->path());
	}

	/**
	 * Test that permissions() returns the current read, write and execute.
	 */
	public function testPermissions() {
		$this->assertEquals('0777', $this->object->permissions());
		$this->assertEquals(null, $this->temp->permissions());

		$this->object->chmod(0444);
		$this->temp->chmod(0444);

		$this->assertEquals('0444', $this->object->permissions());
		$this->assertEquals(null, $this->temp->permissions());
	}

	/**
	 * Test that path() returns the current path.
	 */
	public function testPwd() {
		$this->assertEquals(APP_TEMP .'io/', $this->object->pwd());
		$this->assertEquals(APP_TEMP .'io_temp/', $this->temp->pwd());
	}

	/**
	 * Test that executable(), readable() and writable() return a boolean dependent on state.
	 */
	public function testPermsAndChmod() {
		$this->assertTrue($this->object->executable());
		$this->assertTrue($this->object->readable());
		$this->assertTrue($this->object->writable());

		$this->assertFalse($this->temp->executable());
		$this->assertFalse($this->temp->readable());
		$this->assertFalse($this->temp->writable());

		$this->object->chmod(0);
		$this->temp->chmod(0);

		$this->assertFalse($this->object->executable());
		$this->assertFalse($this->object->readable());
		$this->assertFalse($this->object->writable());

		$this->assertFalse($this->temp->executable());
		$this->assertFalse($this->temp->readable());
		$this->assertFalse($this->temp->writable());
	}

	/**
	 * Test that read() returns the contents of a folder.
	 */
	public function testRead() {
		$contents = $this->object->read();

		$this->assertTrue($contents['count'] == 2);
		$this->assertTrue(count($contents['all']) == 2);
		$this->assertTrue(count($contents['folders']) == 1);
		$this->assertTrue(count($contents['files']) == 1);

		$this->assertEquals(null, $this->temp->read());
	}

	/**
	 * Test that size() returns the folder size.
	 */
	public function testSize() {
		$this->assertEquals(136, $this->object->size());
		$this->assertEquals(null, $this->temp->size());
	}

}