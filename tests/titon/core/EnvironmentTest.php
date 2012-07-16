<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\core;

use titon\Titon;
use titon\tests\TestCase;
use titon\core\Environment;

/**
 * Test class for titon\core\Environment.
 */
class EnvironmentTest extends TestCase {

	/**
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		$this->object = Titon::env();
		$this->object->setup('dev', Environment::DEVELOPMENT, ['dev', '123.0.0.0']);
		$this->object->setup('prod', Environment::PRODUCTION, ['prod', '123.456.0.0']);
		$this->object->setup('staging', Environment::STAGING, ['staging', '123.456.789.0']);
	}

	/**
	 * Test that the correct environment is returned based on server IP or host name.
	 */
	public function testCurrent() {
		// dev
		$_SERVER['HTTP_HOST'] = 'dev';
		$this->object->initialize();
		$this->assertEquals('dev', $this->object->current('name'));
		$this->assertEquals(Environment::DEVELOPMENT, $this->object->current('type'));
		$this->assertEquals([
			 'name' => 'dev',
			 'type' => Environment::DEVELOPMENT,
			 'hosts' => ['dev', '123.0.0.0']
		], $this->object->current());

		// dev ip
		$_SERVER['HTTP_HOST'] = '123.0.0.0';
		$this->object->initialize();
		$this->assertEquals('dev', $this->object->current('name'));

		// prod
		$_SERVER['HTTP_HOST'] = 'prod';
		$this->object->initialize();
		$this->assertEquals('prod', $this->object->current('name'));
		$this->assertEquals(Environment::PRODUCTION, $this->object->current('type'));
		$this->assertEquals([
			 'name' => 'prod',
			 'type' => Environment::PRODUCTION,
			 'hosts' => ['prod', '123.456.0.0']
		], $this->object->current());

		// prod ip
		$_SERVER['HTTP_HOST'] = '123.456.0.0';
		$this->object->initialize();
		$this->assertEquals('prod', $this->object->current('name'));

		// staging
		$_SERVER['HTTP_HOST'] = 'staging';
		$this->object->initialize();
		$this->assertEquals('staging', $this->object->current('name'));
		$this->assertEquals(Environment::STAGING, $this->object->current('type'));
		$this->assertEquals([
			 'name' => 'staging',
			 'type' => Environment::STAGING,
			 'hosts' => ['staging', '123.456.789.0']
		], $this->object->current());

		// staging ip
		$_SERVER['HTTP_HOST'] = '123.456.789.0';
		$this->object->initialize();
		$this->assertEquals('staging', $this->object->current('name'));

		// test SERVER_ADDR over HTTP_HOST
		$_SERVER['HTTP_HOST'] = '';
		$_SERVER['SERVER_ADDR'] = '123.0.0.0';
		$this->object->initialize();
		$this->assertEquals('dev', $this->object->current('name'));
	}

	/**
	 * Test that the fallback is returned correctly if no environment is found.
	 */
	public function testFallbackAs() {
		try {
			$this->object->fallbackAs('fakeEnv');
		} catch (\Exception $e) {
			$this->assertTrue(true);
		}

		$this->object->fallbackAs('dev');

		$_SERVER['HTTP_HOST'] = 'fake_environment';
		$_SERVER['SERVER_ADDR'] = '0.0.0.0';

		$this->object->initialize();
		$this->assertEquals('dev', $this->object->current('name'));
	}

	/**
	 * Test that the correct environment config is loaded.
	 * Testing that the correction environment is found is tested in testCurrent().
	 */
	public function testInitialize() {
		$config = Titon::config();

		$_SERVER['HTTP_HOST'] = 'dev';
		$this->object->initialize();

		$this->assertTrue(is_array($config->get('EnvironmentDevTest')));
		$this->assertEquals(['name' => 'dev'], $config->get('EnvironmentDevTest'));

		$_SERVER['HTTP_HOST'] = 'prod';
		$this->object->initialize();

		$this->assertTrue(is_array($config->get('EnvironmentProdTest')));
		$this->assertEquals(['name' => 'prod'], $config->get('EnvironmentProdTest'));

		// Should be falsey values since staging doesn't exist
		$_SERVER['HTTP_HOST'] = 'staging';
		$this->object->initialize();

		$this->assertArrayNotHasKey('EnvironmentStagingTest', $config->get());
		$this->assertEquals(null, $config->get('EnvironmentStagingTest'));
	}

	/**
	 * Test that is() returns true when the environment is correct.
	 */
	public function testIs() {
		// dev
		$_SERVER['HTTP_HOST'] = 'dev';
		$this->object->initialize();
		$this->assertTrue($this->object->is('dev'));
		$this->assertFalse($this->object->is('prod'));

		// dev ip
		$_SERVER['HTTP_HOST'] = '123.0.0.0';
		$this->object->initialize();
		$this->assertTrue($this->object->is('dev'));
		$this->assertFalse($this->object->is('prod'));

		// prod
		$_SERVER['HTTP_HOST'] = 'prod';
		$this->object->initialize();
		$this->assertTrue($this->object->is('prod'));
		$this->assertFalse($this->object->is('staging'));

		// prod ip
		$_SERVER['HTTP_HOST'] = '123.456.0.0';
		$this->object->initialize();
		$this->assertTrue($this->object->is('prod'));
		$this->assertFalse($this->object->is('staging'));

		// staging
		$_SERVER['HTTP_HOST'] = 'staging';
		$this->object->initialize();
		$this->assertTrue($this->object->is('staging'));
		$this->assertFalse($this->object->is('dev'));

		// staging ip
		$_SERVER['HTTP_HOST'] = '123.456.789.0';
		$this->object->initialize();
		$this->assertTrue($this->object->is('staging'));
		$this->assertFalse($this->object->is('dev'));

		// test SERVER_ADDR over HTTP_HOST
		$_SERVER['HTTP_HOST'] = '';
		$_SERVER['SERVER_ADDR'] = '123.0.0.0';
		$this->object->initialize();
		$this->assertTrue($this->object->is('dev'));
		$this->assertFalse($this->object->is('staging'));
	}

	/**
	 * Test that isDevelopment() returns true when in development.
	 */
	public function testIsDevelopment() {
		$_SERVER['HTTP_HOST'] = 'dev';
		$this->object->initialize();
		$this->assertTrue($this->object->isDevelopment());
		$this->assertFalse($this->object->isProduction());
		$this->assertFalse($this->object->isStaging());
	}

	/**
	 * Test that isProduction() returns true when in production.
	 */
	public function testIsProduction() {
		$_SERVER['HTTP_HOST'] = 'prod';
		$this->object->initialize();
		$this->assertFalse($this->object->isDevelopment());
		$this->assertTrue($this->object->isProduction());
		$this->assertFalse($this->object->isStaging());
	}

	/**
	 * Test that isStaging() returns true when in staging.
	 */
	public function testIsStaging() {
		$_SERVER['HTTP_HOST'] = 'staging';
		$this->object->initialize();
		$this->assertFalse($this->object->isDevelopment());
		$this->assertFalse($this->object->isProduction());
		$this->assertTrue($this->object->isStaging());
	}

	/**
	 * Test that exceptions are thrown when invalid data is passed.
	 */
	public function testSetup() {
		try {
			$this->object->setup('noHosts', Environment::DEVELOPMENT, []);
		} catch (\Exception $e) {
			$this->assertTrue(true);
		}

		try {
			$this->object->setup('wrongType', 5, ['host']);
		} catch (\Exception $e) {
			$this->assertTrue(true);
		}
	}
}