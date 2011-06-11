<?php
/**
 * Titon: The PHP 5.3 Micro Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\libs\helpers\html;

use \titon\Titon;
use \titon\libs\helpers\HelperAbstract;
use \titon\utility\Encrypt;

/**
 * The HtmlHelper is primarily used for dynamic HTML tag creation within templates.
 *
 * @package	titon.libs.helpers.html
 */
class HtmlHelper extends HelperAbstract {

	/**
	 * A list of all breadcrumbs in the trail, with the title, url and attributes.
	 *
	 * @access protected
	 * @var array
	 */
	protected $_breadcrumbs = array();

	/**
	 * Mapping of HTML tags for this helper.
	 *
	 * @access protected
	 * @var string
	 */
	protected $_tags = array(
		'anchor'	=> '<a%s>%s</a>',
		'link'		=> '<link%s>',
		'meta'		=> '<meta%s>',
		'script'	=> '<script%s>%s</script>',
		'style'		=> '<style%s>%s</style>',
		'image'		=> '<img%s>'
	);

	/**
	 * Add a link to the breadcrumbs.
	 *
	 * @access public
	 * @param string $title
	 * @param string|array $url
	 * @param array $attributes
	 * @return void
	 */
	public function addCrumb($title, $url, array $attributes = array()) {
		$this->_breadcrumbs[] = array($title, $url, $attributes);
	}

	/**
	 * Create an HTML anchor link.
	 *
	 * @access public
	 * @param string $title
	 * @param string|array $url
	 * @param array $attributes
	 * @return string
	 */
	public function anchor($title, $url, array $attributes = array()) {
		$url = Titon::router()->detect($url);

		if (is_array($route)) {
			$url = Titon::router()->build($url);
		}

		$attributes['href'] = $url;

		if (!isset($attributes['title'])) {
			$attributes['title'] = htmlentities($title, ENT_COMPAT, Titon::config()->charset());
		}

		return $this->tag('anchor',
			$this->attributes($attributes),
			$title
		);
	}

	/**
	 * Return a trail of breadcrumbs, formatted as anchor links, separated by $separator.
	 *
	 * @access public
	 * @param string $separator
	 * @return string
	 */
	public function breadcrumbs($separator = ' &raquo; ') {
		$trail = array();

		if (!empty($this->_breadcrumbs)) {
			foreach ($this->_breadcrumbs as $crumb) {
				$trail[] = $this->anchor($crumb[0], $crumb[1], $crumb[2]);
			}
		}

		return implode($separator, $trail);
	}

	/**
	 * Return the HTML5 doctype.
	 *
	 * @access public
	 * @return string
	 */
	public function docType() {
		return "<!DOCTYPE html>\n";
	}

	/**
	 * Create an image element.
	 *
	 * @access public
	 * @param string $path
	 * @param array $attributes
	 * @return string
	 */
	public function image($path, array $attributes = array()) {
		$attributes['src'] = $path;

		if (!isset($attributes['alt'])) {
			$attributes['alt'] = '';
		}

		$url = null;

		if (isset($attributes['url'])) {
			$url = $attributes['url'];
			unset($attributes['url']);
		}

		$image = $this->tag('image', $this->attributes($attributes));

		if ($url) {
			return $this->anchor($image, $url, array('title' => $attributes['alt']));
		}

		return $image;
	}

	/**
	 * Create a link element.
	 *
	 * @access public
	 * @param array $attributes
	 * @return string
	 */
	public function link(array $attributes = array()) {
		return $this->tag('link',
			$this->attributes($attributes)
		);
	}

	/**
	 * Creates a mailto hyperlink. Emails will be obfuscated to hide against spambots and harvesters.
	 *
	 * @access public
	 * @param string $email
	 * @param array $attributes
	 * @return string
	 */
	public function mailto($email, array $attributes = array()) {
		$email = Encrypt::obfuscate($email);

		if (!isset($attributes['title'])) {
			$attributes['title'] = '';
		}

		return $this->anchor($email, 'mailto:'. $email, $attributes);
	}

	/**
	 * Create a meta element. Has predefined values for common meta tags.
	 *
	 * @access public
	 * @param string $type
	 * @param string $content
	 * @param array $attributes
	 * @return string
	 */
	public function meta($type, $content = null, array $attributes = array()) {
		if (empty($content)) {
			switch (strtolower($type)) {
				case 'content-script-type': 
					$content = 'text/javascript'; 
				break;
				case 'content-style-type': 
					$content = 'text/css'; 
				break;
				case 'content-type': 
					$content = 'text/html; charset='. Titon::config()->charset();
				break;
			}
		}

		$metaTypes = array(
			'content-type'          => array('http-equiv' => 'Content-Type', 'content' => $content),
			'content-script-type'   => array('http-equiv' => 'Content-Script-Type', 'content' => $content),
			'content-style-type'    => array('http-equiv' => 'Content-Style-Type', 'content' => $content),
			'content-language'      => array('http-equiv' => 'Content-Language', 'content' => $content),
			'keywords'      => array('name' => 'keywords', 'content' => $content),
			'description'   => array('name' => 'description', 'content' => $content),
			'author'        => array('name' => 'author', 'content' => $content),
			'robots'        => array('name' => 'robots', 'content' => $content),
			'rss'   => array('type' => 'application/rss+xml', 'rel' => 'alternate', 'title' => $type, 'link' => $content),
			'atom'  => array('type' => 'application/atom+xml', 'title' => $type, 'link' => $content),
			'icon'  => array('type' => 'image/x-icon', 'rel' => 'icon', 'link' => $content),
		);

		if (isset($metaTypes[strtolower($type)])) {
			$attributes = $attributes + $metaTypes[strtolower($type)];
		} else {
			$attributes['name'] = $type;
			$attributes['content'] = $content;
		}

		return $this->tag('meta', $this->attributes($attributes));
	}

	/**
	 * Grab the page title if it has been set.
	 *
	 * @access public
	 * @param string|array $separator
	 * @return string
	 */
	public function pageTitle($separator = ' - ') {
		$pageTitle = $this->_view->config('data.pageTitle');

		if (is_array($pageTitle)) {
			return implode($separator, $pageTitle);
		}

		return $pageTitle;
	}

	/**
	 * Create a script element to include a JS file or to encompass JS code.
	 *
	 * @access public
	 * @param string $source
	 * @param string $content
	 * @param bool $escape
	 * @return string
	 */
	public function script($source, $content = null, $escape = true) {
		$attributes = array('type' => 'text/javascript');

		if (!empty($source)) {
			$attributes['src'] = $source;
		}

		return $this->tag('script',
			$this->attributes($attributes),
			$content
		);
	}

	/**
	 * Create a style element to encompass CSS.
	 *
	 * @access public
	 * @param string $content
	 * @return string
	 */
	public function style($content) {
		return $this->tag('style',
			$this->attributes(array('type' => 'text/css')),
			$content
		);
	}

}