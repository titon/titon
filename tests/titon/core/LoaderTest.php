<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

/**
 * Test class for titon\core\Loader.
 */
class LoaderTest extends \PHPUnit_Framework_TestCase {

	protected $object;

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = titon\Titon::loader();
	}

	/**
	 * Test that the class name is returned without the namespace or extension.
	 */
	public function testBaseClass() {
		$this->assertEquals('ClassName', $this->object->baseClass('\test\namespace\ClassName'));
		$this->assertEquals('ClassName', $this->object->baseClass('\test\namespace\ClassName.ext'));

		$this->assertEquals('ClassName', $this->object->baseClass('test:namespace:ClassName', ':'));
		$this->assertEquals('ClassName', $this->object->baseClass('test:namespace:ClassName.ext', ':'));

		$this->assertEquals('ClassName', $this->object->baseClass('test/namespace/ClassName', '/'));
		$this->assertEquals('ClassName', $this->object->baseClass('test/namespace/ClassName.ext', '/'));

		$this->assertEquals('ClassName', $this->object->baseClass('test.namespace.ClassName', '.'));
		$this->assertEquals('ext', $this->object->baseClass('test.namespace.ClassName.ext', '.'));
	}

	/**
	 * Test that only the namespace package is returned when a fully qualified class name is returned.
	 */
	public function testBaseNamespace() {
		$this->assertEquals('test\namespace', $this->object->baseNamespace('\test\namespace\ClassName'));
		$this->assertEquals('test\namespace', $this->object->baseNamespace('\test\namespace\ClassName.ext'));

		$this->assertEquals('test\namespace', $this->object->baseNamespace('/test/namespace/ClassName'));
		$this->assertEquals('test\namespace', $this->object->baseNamespace('/test/namespace/ClassName.ext'));
	}

	/**
	 * Test that all slashes are converted to forward slashes (works for linux and windows).
	 */
	public function testDs() {
		// linux
		$this->assertEquals('/some/fake/folder/path/fileName.php', $this->object->ds('/some/fake/folder/path/fileName.php'));
		$this->assertEquals('/some/fake/folder/path/fileName.php', $this->object->ds('/some\fake/folder\path/fileName.php'));

		// windows
		$this->assertEquals('C:/some/fake/folder/path/fileName.php', $this->object->ds('C:\some\fake\folder\path\fileName.php'));
		$this->assertEquals('C:/some/fake/folder/path/fileName.php', $this->object->ds('C:\some/fake\folder/path\fileName.php'));
	}

	/**
	 * Test that file importing returns true or false if file inclusion works.
	 */
	public function testImport() {
		$this->assertTrue($this->object->import('titon\base\types\Enum'));
		$this->assertTrue($this->object->import('/titon/base/types/Float.php'));
		$this->assertFalse($this->object->import('\some\fake\ClassName'));
		$this->assertFalse($this->object->import('/some/fake/filePath.php'));
	}

	/**
	 * Test that defining new include paths registers correctly.
	 */
	public function testIncludePath() {
		$baseIncludePath = get_include_path();
		$selfPath1 = '/fake/test/1';
		$selfPath2 = '/fake/test/2';
		$selfPath3 = '/fake/test/3';

		$this->assertEquals($baseIncludePath, get_include_path());

		$this->object->includePath($selfPath1);
		$this->assertEquals($baseIncludePath . PATH_SEPARATOR . $selfPath1, get_include_path());

		$this->object->includePath([$selfPath2, $selfPath3]);
		$this->assertEquals($baseIncludePath . PATH_SEPARATOR . $selfPath1 . PATH_SEPARATOR . $selfPath2 . PATH_SEPARATOR . $selfPath3, get_include_path());
	}

	/**
	 * Test that removing an extension from a file path works correctly.
	 */
	public function testStripExt() {
		$this->assertEquals('NoExt', $this->object->stripExt('NoExt'));
		$this->assertEquals('ClassName', $this->object->stripExt('ClassName.php'));
		$this->assertEquals('File_Name', $this->object->stripExt('File_Name.php'));

		$this->assertEquals('\test\namespace\ClassName', $this->object->stripExt('\test\namespace\ClassName.php'));
		$this->assertEquals('\test\namespace\Class_Name', $this->object->stripExt('\test\namespace\Class_Name.php'));

		$this->assertEquals('/test/file/path/FileName', $this->object->stripExt('/test/file/path/FileName.php'));
		$this->assertEquals('/test/file/path/File/Name', $this->object->stripExt('/test/file/path/File/Name.php'));
	}

	/**
	 * Test that converting a path to a namespace package works correctly.
	 */
	public function testToNamespace() {
		$this->assertEquals('test\file\path\FileName', $this->object->toNamespace('/test/file/path/FileName.php'));
		$this->assertEquals('test\file\path\File\Name', $this->object->toNamespace('/test/file/path/File/Name.php'));

		$this->assertEquals('test\file\path\FileName', $this->object->toNamespace(VENDORS . 'test/file/path/FileName.php'));
		$this->assertEquals('titon\test\file\path\File\Name', $this->object->toNamespace(TITON . 'test/file/path/File/Name.php'));
	}

	/**
	 * Test that converting a namespace to a path works correctly.
	 */
	public function testToPath() {
		$this->assertEquals('/test/namespace/ClassName.php', $this->object->toPath('\test\namespace\ClassName'));
		$this->assertEquals('/test/namespace/Class/Name.php', $this->object->toPath('\test\namespace\Class_Name'));

		$this->assertEquals('/Test/NameSpace/ClassName.php', $this->object->toPath('\Test\NameSpace\ClassName'));
		$this->assertEquals('/Test/NameSpace/Class/Name.php', $this->object->toPath('\Test\NameSpace\Class_Name'));

		$this->assertEquals('/test/namespace/ClassName.PHP', $this->object->toPath('\test\namespace\ClassName', 'PHP'));
		$this->assertEquals('/test/namespace/Class/Name.PHP', $this->object->toPath('\test\namespace\Class_Name', 'PHP'));

		$this->assertEquals(TITON . '/test/namespace/ClassName.php', $this->object->toPath('\test\namespace\ClassName', 'php', TITON));
		$this->assertEquals(TITON . '/test/namespace/Class/Name.php', $this->object->toPath('\test\namespace\Class_Name', 'php', TITON));

		$this->assertEquals(TITON_APP . '/test/namespace/ClassName.php', $this->object->toPath('\test\namespace\ClassName', 'php', TITON_APP));
		$this->assertEquals(TITON_APP . '/test/namespace/Class/Name.php', $this->object->toPath('\test\namespace\Class_Name', 'php', TITON_APP));
	}

}