<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\tests\titon\libs\helpers\html;

use titon\tests\TestCase;
use titon\libs\helpers\html\HtmlHelper;
use titon\utility\Crypt;
use \Exception;

/**
 * Test class for titon\libs\helpers\html\HtmlHelper.
 */
class HtmlHelperTest extends TestCase {

	/**
	 * Initialize helper.
	 */
	protected function setUp() {
		$this->object = new HtmlHelper();
	}

	/**
	 * Test that anchor() generates the correct <a> markup.
	 */
	public function testAnchor() {
		$this->assertEquals('<a href="">Anchor</a>' . PHP_EOL, $this->object->anchor('Anchor', ''));
		$this->assertEquals('<a href="/">Anchor Link!</a>' . PHP_EOL, $this->object->anchor('Anchor Link!', '/'));

		// test escaping
		$this->assertEquals('<a href="/">Double &quot;quotes&quot;</a>' . PHP_EOL, $this->object->anchor('Double "quotes"', '/'));
		$this->assertEquals('<a href="/">Single &#039;quotes&#039;</a>' . PHP_EOL, $this->object->anchor("Single 'quotes'", '/'));

		// attributes
		$this->assertEquals('<a href="/" title="Test &quot;title&quot; here">Anchor</a>' . PHP_EOL, $this->object->anchor('Anchor', '/', ['title' => 'Test "title" here']));
		$this->assertEquals('<a class="className" href="/" id="id-name">Anchor</a>' . PHP_EOL, $this->object->anchor('Anchor', '/', ['id' => 'id-name', 'class' => 'className']));

		// urls
		$this->assertEquals('<a href="/users/index">Users</a>' . PHP_EOL, $this->object->anchor('Users', ['module' => 'users']));
		$this->assertEquals('<a href="/users/profile">Users Profile</a>' . PHP_EOL, $this->object->anchor('Users Profile', ['module' => 'users', 'controller' => 'profile']));
		$this->assertEquals('<a href="/users/profile/activity">Users Profile Activity</a>' . PHP_EOL, $this->object->anchor('Users Profile Activity', ['module' => 'users', 'controller' => 'profile', 'action' => 'activity']));
		$this->assertEquals('<a href="/users/index/index/123">Users</a>' . PHP_EOL, $this->object->anchor('Users', ['module' => 'users', 123]));
	}

	/**
	 * Test that doctype() generates the correct <DOCTYPE> markup.
	 */
	public function testDoctype() {
		$this->assertEquals('<!DOCTYPE html>' . PHP_EOL, $this->object->doctype());
	}

	/**
	 * Test that image() generates the correct <img> markup.
	 */
	public function testImage() {
		$this->assertEquals('<img alt="" src="image.png">' . PHP_EOL, $this->object->image('image.png'));
		$this->assertEquals('<img alt="Foobar" src="/some/path/image.JPG">' . PHP_EOL, $this->object->image('/some/path/image.JPG', ['alt' => 'Foobar']));
		$this->assertEquals('<img alt="Foobar" src="invalid path/image.gif" title="&quot;Image&quot;">' . PHP_EOL, $this->object->image('invalid path/image.gif', ['alt' => 'Foobar', 'title' => '"Image"']));

		// with anchors
		$this->assertEquals('<a href="/"><img alt="" src="image.png"></a>' . PHP_EOL, $this->object->image('image.png', [], '/'));
		$this->assertEquals('<a href="/"><img alt="Alternate" src="image.png"></a>' . PHP_EOL, $this->object->image('image.png', ['alt' => 'Alternate'], '/'));
		$this->assertEquals('<a href="/users/index"><img alt="" src="image.png"></a>' . PHP_EOL, $this->object->image('image.png', [], ['module' => 'users']));
	}

	/**
	 * Test that link() generates the correct <link> markup.
	 */
	public function testLink() {
		$this->assertEquals('<link href="style.css" media="screen" rel="stylesheet" type="text/css">' . PHP_EOL, $this->object->link('style.css'));
		$this->assertEquals('<link href="path/style.css" media="handheld" rel="stylesheet" type="text/css">' . PHP_EOL, $this->object->link('path/style.css', ['media' => 'handheld']));
	}

	/**
	 * Test that mailto() generates the correct <a> markup with mailto: href.
	 */
	public function testMailto() {
		$domain = Crypt::obfuscate('test@domain.com');

		$this->assertEquals('<a href="mailto:' . $domain . '" title="">' . $domain . '</a>' . PHP_EOL, $this->object->mailto('test@domain.com'));
		$this->assertEquals('<a href="mailto:' . $domain . '" title="Email me!">' . $domain . '</a>' . PHP_EOL, $this->object->mailto('test@domain.com', ['title' => 'Email me!']));
		$this->assertEquals('<a class="className" href="mailto:' . $domain . '" title="Email me!">' . $domain . '</a>' . PHP_EOL, $this->object->mailto('test@domain.com', ['title' => 'Email me!', 'class' => 'className']));
		$this->assertEquals('<a href="mailto:' . $domain . '" onclick="alert();" title="Email me!">' . $domain . '</a>' . PHP_EOL, $this->object->mailto('test@domain.com', ['title' => 'Email me!', 'onclick' => 'alert();']));
	}

	/**
	 * Test that meta() generates the correct <meta> markup.
	 */
	public function testMeta() {
		$this->assertEquals('<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">' . PHP_EOL, $this->object->meta('Content-Type'));
		$this->assertEquals('<meta content="application/json" http-equiv="Content-Type">' . PHP_EOL, $this->object->meta('content-type', 'application/json'));
		$this->assertEquals('<meta content="text/javascript" http-equiv="Content-Script-Type">' . PHP_EOL, $this->object->meta('content-script-type'));
		$this->assertEquals('<meta content="text/css" http-equiv="Content-Style-Type">' . PHP_EOL, $this->object->meta('content-Style-type'));
		$this->assertEquals('<meta content="" http-equiv="Content-Language">' . PHP_EOL, $this->object->meta('content-language'));
		$this->assertEquals('<meta content="en-us" http-equiv="Content-Language">' . PHP_EOL, $this->object->meta('content-language', 'en-us'));

		$this->assertEquals('<meta content="" name="keywords">' . PHP_EOL, $this->object->meta('keywords'));
		$this->assertEquals('<meta content="key, words, &quot;here&quot;, &amp;, stuff" name="keywords">' . PHP_EOL, $this->object->meta('keywords', 'key, words, "here", &, stuff'));
		$this->assertEquals('<meta content="" name="description">' . PHP_EOL, $this->object->meta('description'));
		$this->assertEquals('<meta content="This &quot;is&quot; a description." name="description">' . PHP_EOL, $this->object->meta('description', 'This "is" a description.'));
		$this->assertEquals('<meta content="" name="robots">' . PHP_EOL, $this->object->meta('robots'));
		$this->assertEquals('<meta content="nofollow" name="robots">' . PHP_EOL, $this->object->meta('robots', 'nofollow'));
		$this->assertEquals('<meta content="" name="author">' . PHP_EOL, $this->object->meta('author'));
		$this->assertEquals('<meta content="Miles &quot;gearvOsh&quot; Johnson" name="author">' . PHP_EOL, $this->object->meta('author', 'Miles "gearvOsh" Johnson'));

		$this->assertEquals('<meta link="" rel="alternate" title="" type="application/rss+xml">' . PHP_EOL, $this->object->meta('rss'));
		$this->assertEquals('<meta link="feed.rss" rel="alternate" title="" type="application/rss+xml">' . PHP_EOL, $this->object->meta('rss', 'feed.rss'));
		$this->assertEquals('<meta link="path/feed.rss" rel="alternate" title="My RSS" type="application/rss+xml">' . PHP_EOL, $this->object->meta('rss', 'path/feed.rss', ['title' => 'My RSS']));
		$this->assertEquals('<meta link="" title="" type="application/atom+xml">' . PHP_EOL, $this->object->meta('atom'));
		$this->assertEquals('<meta link="atom.rss" title="" type="application/atom+xml">' . PHP_EOL, $this->object->meta('atom', 'atom.rss'));
		$this->assertEquals('<meta link="path/atom.rss" title="My Atom Feed" type="application/atom+xml">' . PHP_EOL, $this->object->meta('atom', 'path/atom.rss', ['title' => 'My Atom Feed']));
		$this->assertEquals('<meta link="favicon.ico" rel="icon" type="image/x-icon">' . PHP_EOL, $this->object->meta('icon', 'favicon.ico'));

		// custom, open graph, etc
		$this->assertEquals('<meta content="website" property="og:type">' . PHP_EOL, $this->object->meta([
			'property' => 'og:type',
			'content' => 'website'
		]));
	}

	/**
	 * Test that script() generates the correct <script> markup.
	 */
	public function testScript() {
		$this->assertEquals('<script src="script.js" type="text/javascript"></script>' . PHP_EOL, $this->object->script('script.js'));
		$this->assertEquals('<script src="path/script.js" type="text/javascript"></script>' . PHP_EOL, $this->object->script('path/script.js'));
		$this->assertEquals('<script type="text/javascript"><![CDATA[script.js]]></script>' . PHP_EOL, $this->object->script('script.js', true));
		$this->assertEquals('<script type="text/javascript"><![CDATA[(function() { alert(); })();]]></script>' . PHP_EOL, $this->object->script('(function() { alert(); })();', true));
	}

	/**
	 * Test that style() generates the correct <style> markup.
	 */
	public function testStyle() {
		$this->assertEquals('<style type="text/css">#id { color: red; }</style>' . PHP_EOL, $this->object->style('#id { color: red; }'));
		$this->assertEquals('<style type="text/css">#id { color: red; }' . PHP_EOL . '.class { text-align: left; }</style>' . PHP_EOL, $this->object->style('#id { color: red; }' . PHP_EOL . '.class { text-align: left; }'));
	}


}