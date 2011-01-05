<?php
/**
 * The HTML helper is used primarily for specific HTML tag creation. When using the helper to build HTML,
 * it stores key values within the framework that are tunneled elsewhere.
 * It also utilizes the Router to parsed and return internal link paths.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\modules\helpers\html;

use \titon\core\App;
use \titon\modules\helpers\HelperAbstract;
use \titon\router\Router;
use \titon\utility\Encrypt;

/**
 * HTML Helper
 *
 * @package		Titon
 * @subpackage	Titon.Modules.Helpers
 */
class Html extends HelperAbstract {

    /**
     * A list of all breadcrumbs in the trail, with the title, url and attributes.
     *
     * @access protected
     * @var array
     */
    protected $_breadcrumbs = array();

    /**
     * A list of all HTML and XHTML tags used within the current helper.
     * If an element has multiple variations, it is represented with an array.
     *
     * @access protected
     * @var string
     */
    protected $_tags = array(
        'anchor'    => '<a%s>%s</a>',
        'link'      => array('<link%s>', '<link%s />'),
        'meta'      => array('<meta%s>', '<meta%s />'),
        'script'    => '<script%s>%s</script>',
        'style'     => '<style%s>%s</style>',
		'image'		=> array('<img%s>', '<img%s />')
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
        if ($route = Router::detect($url)) {
            $url = Router::build($route);
        }

        $attributes['href'] = $url;
        
        if (!isset($attributes['title'])) {
            $attributes['title'] = htmlentities($title, ENT_COMPAT, App::charset());
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
        if (!empty($this->_breadcrumbs)) {
            $trail = array();

            foreach ($this->_breadcrumbs as $crumb) {
                $trail[] = $this->anchor($crumb[0], $crumb[1], $crumb[2]);
            }

            return implode($separator, $trail);
        }

        return null;
    }

    /**
     * Create an HTML doctype. Can be mismatched for different HTMLs and versions.
     *
     * @access public
     * @param string $markup
     * @param string $type
     * @param string $version
     * @return string
     */
    public function docType($markup = 'html', $type = 'strict', $version = '4') {
        $docTypes = array(
            'html-4-strict'     => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
            'html-4-trans'      => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
            'html-4-frameset'   => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
            'html-5'            => '<!DOCTYPE html>',
            'xhtml-1-strict'    => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
            'xhtml-1-trans'     => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
            'xhtml-1-frameset'  => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
            'xhtml-1.1'         => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">'
        );

        if ($markup == 'xhtml') {
            $version = 1;
        }

        if ($type == 'transitional') {
            $type = 'trans';
        }

        $slug = $markup .'-'. $version;
        if (!empty($type)) {
            $slug .= '-'. $type;
        }

        $slug = strtolower($slug);
        
        if (!isset($docTypes[$slug])) {
            $slug = 'html-4-strict';
        }

        $this->View->configure(array('doctype' => $slug));
        return $docTypes[$slug] ."\n";
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
		if (substr($path, 0, strlen(IMGR)) != IMGR) {
			$path = IMGR . $path;
		}
		
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
                case 'content-script-type': $content = 'text/javascript'; break;
                case 'content-style-type':  $content = 'text/css'; break;
                case 'content-type':
                    $content  = ($this->isDoctype('xhtml') ? 'application/xhtml\+xml' : 'text/html');
                    $content .= '; charset='. App::charset();
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
     * Create a script element to include a JS file or to encompass JS code.
     *
     * @access public
     * @param string $source
     * @param string $content
     * @param boolean $escape
     * @return string
     */
    public function script($source, $content = null, $escape = true) {
        $attributes = array('type' => 'text/javascript');

        if (!empty($source)) {
            $attributes['src'] = $source;
        }

        if (!empty($content) && $escape === true && $this->isDoctype('xhtml')) {
            $content = '/* <![CDATA[ */'. $content .'/* ]]> */';
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
    public function style($content = null) {
        return $this->tag('style',
            $this->attributes(array('type' => 'text/css')),
            $content
        );
    }

}