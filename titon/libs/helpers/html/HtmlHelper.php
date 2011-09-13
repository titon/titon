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
 * @uses	titon\Titon
 * @uses	titon\utility\Encrypt
 */
class HtmlHelper extends HelperAbstract {

	/**
	 * Mapping of HTML tags for this helper.
	 *
	 * @access protected
	 * @var array
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
	 * Create an HTML anchor link.
	 *
	 * @access public
	 * @param string $title
	 * @param string|array $url
	 * @param array $attributes
	 * @return string
	 */
	public function anchor($title, $url, array $attributes = array()) {
		$attributes['href'] = Titon::router()->detect($url);

		return $this->tag('anchor',
			$this->attributes($attributes),
			$title
		);
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
	 * @param string $path
	 * @param array $attributes
	 * @return string
	 */
	public function link($path, array $attributes = array()) {
		$attributes = $attributes + array(
			'rel'   => 'stylesheet',
			'type'  => 'text/css',
			'media' => 'screen'
		);
		
		$attributes['href'] = $path;
		
		return $this->tag('link', $this->attributes($attributes));
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

		$attributes['escape'] = false;

		return $this->anchor($email, 'mailto:' . $email, $attributes);
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
					$content = 'text/html; charset=' . Titon::config()->encoding();
				break;
			}
		}

		$metaTypes = array(
			'content-type'          => array('http-equiv' => 'Content-Type', 'content' => $content),
			'content-script-type'   => array('http-equiv' => 'Content-Script-Type', 'content' => $content),
			'content-style-type'    => array('http-equiv' => 'Content-Style-Type', 'content' => $content),
			'content-language'      => array('http-equiv' => 'Content-Language', 'content' => $content),
			'keywords'				=> array('name' => 'keywords', 'content' => $content),
			'description'			=> array('name' => 'description', 'content' => $content),
			'author'				=> array('name' => 'author', 'content' => $content),
			'robots'				=> array('name' => 'robots', 'content' => $content),
			'rss'					=> array('type' => 'application/rss+xml', 'rel' => 'alternate', 'title' => $type, 'link' => $content),
			'atom'					=> array('type' => 'application/atom+xml', 'title' => $type, 'link' => $content),
			'icon'					=> array('type' => 'image/x-icon', 'rel' => 'icon', 'link' => $content),
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
	 * Create a script element to include a JS file or to encompass JS code.
	 *
	 * @access public
	 * @param string $source
	 * @param string $isBlock
	 * @return string
	 */
	public function script($source, $isBlock = false) {
		$attributes = array('type' => 'text/javascript');
		$content = '';
		
		if ($isBlock) {
			$content = '<![CDATA[' . $source . ']]>';
		} else {
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

	/**
	 * Grab the page title if it has been set.
	 *
	 * @access public
	 * @param string|array $separator
	 * @return string
	 */
	public function title($separator = ' - ') {
		$pageTitle = $this->_engine->data('pageTitle');

		if (is_array($pageTitle)) {
			return implode($separator, $pageTitle);
		}

		return $pageTitle;
	}

}