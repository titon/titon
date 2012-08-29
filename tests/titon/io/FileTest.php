<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\io;

use titon\io\File;
use titon\tests\TestCase;
use \Exception;

/**
 * Test class for titon\io\File.
 */
class FileTest extends TestCase {

	/**
	 * Temp file for testing.
	 */
	public $temp;

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		parent::setUp();

		$this->object = new File(APP_TEMP . 'io/file', true, 0777);
		$this->temp = new File(APP_TEMP . 'io/temp', false);
	}

	/**
	 * Reset file state after each test.
	 */
	protected function tearDown() {
		$this->object->close();
		$this->object->delete();
		$this->temp->close();
		$this->temp->delete();
	}

	/**
	 * Test that __construct() throws exceptions.
	 */
	public function testConstruct() {
		try {
			new File(APP_TEMP);
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
	 * Test that append() adds content to the end of a file, while prepend() adds content to the beginnig of a file.
	 */
	public function testAppendPrepend() {
		$this->object->write('Content');
		$this->assertEquals('Content', $this->object->read());

		$this->object->append('Append');
		$this->assertEquals('ContentAppend', $this->object->read());

		$this->object->prepend('Prepend');
		$this->assertEquals('PrependContentAppend', $this->object->read());
	}

	/**
	 * Test that changeTime() returns the last change time.
	 */
	public function testChangeTime() {
		$this->assertTrue(is_int($this->object->changeTime()));
		$this->assertTrue(is_null($this->temp->changeTime()));
	}

	/**
	 * Test that create() creates the file if it doesn't exist.
	 */
	public function testCreate() {
		$this->assertTrue($this->object->exists());
		$this->assertFalse($this->object->create());

		$this->assertFalse($this->temp->exists());
		$this->assertTrue($this->temp->create());
		$this->assertTrue($this->temp->exists());
	}

	/**
	 * Test that copy() will copy the file to the new location, and delete() will delete it.
	 */
	public function testCopyAndDelete() {
		$target = APP_TEMP . 'io/file_copy';

		$this->assertFalse(file_exists($target));

		// copy over stuff
		$file = $this->object->copy($target);

		$this->assertInstanceOf('titon\io\File', $file);
		$this->assertTrue(file_exists($target));

		// try again without overwrite
		try {
			$this->object->copy($target, false);
			$this->assertTrue(false);
		} catch (Exception $e) {
			$this->assertTrue(true);
		}

		// cleanup
		$file->delete();
	}

	/**
	 * Test exists() returns true if the file exists.
	 */
	public function testExists() {
		$this->assertTrue($this->object->exists());
		$this->assertFalse($this->temp->exists());

		$this->temp->create();
		$this->assertTrue($this->temp->exists());
	}

	/**
	 * Test that ext() returns the file extension.
	 */
	public function testExt() {
		$this->assertEquals('', $this->object->ext());

		$file = new File(APP_TEMP . 'io/file.HTML', true);
		$this->assertEquals('html', $file->ext());

		$file->delete();
	}

	/**
	 * Test folder() will return the parent folder as a Folder object.
	 */
	public function testFolder() {
		$parent =& $this->object->folder();
		$this->assertInstanceOf('titon\io\Folder', $parent);
		$this->assertEquals(APP_TEMP . 'io/', $parent->path());
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

		$folder = new File('C:/app/');
		$this->assertTrue($folder->isAbsolute());

		$folder = new File('../../some/folder/');
		$this->assertFalse($folder->isAbsolute());
	}

	/**
	 * Test isRelative() returns true if the path is relative.
	 */
	public function testIsRelative() {
		$this->assertFalse($this->object->isRelative());
		$this->assertFalse($this->temp->isRelative());

		$folder = new File('C:/app/');
		$this->assertFalse($folder->isRelative());

		$folder = new File('../../some/folder/');
		$this->assertTrue($folder->isRelative());
	}

	/**
	 * Test isWindows() returns true if the path is Windows file system.
	 */
	public function testisWindows() {
		$this->assertFalse($this->object->isWindows());
		$this->assertFalse($this->temp->isWindows());

		$folder = new File('C:/app/');
		$this->assertTrue($folder->isWindows());

		$folder = new File('//network/drive/');
		$this->assertTrue($folder->isWindows());

		$folder = new File('../../some/folder/');
		$this->assertFalse($folder->isWindows());
	}

	/**
	 * Test that lock() locks a file and unlock() unlocks it.
	 */
	public function testLockAndUnlock() {
		$this->object->open('r');

		$this->assertTrue($this->object->lock());
		$this->assertFalse($this->temp->lock());

		$this->assertTrue($this->object->unlock());
		$this->assertFalse($this->temp->unlock());

		$this->object->close();
	}

	/**
	 * Test that md5() returns an MD5 hash of the file.
	 */
	public function testMd5() {
		$this->assertEquals('d41d8cd98f00b204e9800998ecf8427e', $this->object->md5());
		$this->assertEquals(null, $this->temp->md5());

		$this->object->write('Change to content to produce a different hash.');
		$this->assertEquals('92dbea223f446b8d480a8fd4984232e4', $this->object->md5());
	}

	/**
	 * Test that modifiedTime() returns the last modified time.
	 */
	public function testModifiedTime() {
		$this->assertTrue(is_int($this->object->modifiedTime()));
		$this->assertTrue(is_null($this->temp->modifiedTime()));
	}

	/**
	 * Test that move() renames the file.
	 */
	public function testMove() {
		$base = APP_TEMP . 'io/file';
		$target = APP_TEMP . 'io/file_move';

		$this->assertTrue(file_exists($base));
		$this->assertFalse(file_exists($target));

		$this->object->move($target);

		$this->assertFalse(file_exists($base));
		$this->assertTrue(file_exists($target));

		// move it back
		$this->object->move($base);
	}

	/**
	 * Test that name() returns the base file name.
	 */
	public function testName() {
		$this->assertEquals('file', $this->object->name());
		$this->assertEquals('temp', $this->temp->name());
	}

	/**
	 * Test that open() and close() create and destroy resource handles.
	 */
	public function testOpenClose() {
		$this->assertFalse($this->object->close());
		$this->assertFalse($this->temp->close());

		$this->assertTrue($this->object->open('w'));
		$this->assertFalse($this->temp->open('w'));

		$this->assertTrue($this->object->close());
		$this->assertFalse($this->temp->close());
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
		$this->assertEquals(APP_TEMP . 'io/file', $this->object->path());
		$this->assertEquals(APP_TEMP . 'io/temp', $this->temp->path());
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
		$this->assertEquals(APP_TEMP .'io/file', $this->object->pwd());
		$this->assertEquals(APP_TEMP .'io/temp', $this->temp->pwd());
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
	 * Test that read() returns the contents of a file, and write() overwrites the contents.
	 */
	public function testReadWrite() {
		$content = 'Lets add a bit of fat to you before reading.';
		$this->object->write($content);

		$this->assertEquals($content, $this->object->read());
		$this->assertEquals('Lets add', $this->object->read(8));

		$this->object->write('');
		$this->assertEquals('', $this->object->read());
	}

	/**
	 * Test that size() returns the folder size.
	 */
	public function testSize() {
		$this->assertEquals(0, $this->object->size());
		$this->assertEquals(null, $this->temp->size());

		$this->object->write('You must weigh a ton.');

		$this->assertEquals(21, $this->object->size());
	}

}