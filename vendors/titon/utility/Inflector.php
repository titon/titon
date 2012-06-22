<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\utility;

use titon\Titon;

/**
 * String and grammar inflection. Converts strings to a certain format. Camel cased, singular, plural etc.
 *
 * @package	titon.utility
 * @uses	titon\Titon
 *
 * @link	http://php.net/manual/book.mbstring.php
 */
class Inflector {

	/**
	 * Cached pluralized strings for fast lookup.
	 *
	 * @access protected
	 * @var array
	 */
	protected static $_pluralized = array();

	/**
	 * Cached singularized strings for fast lookup.
	 *
	 * @access protected
	 * @var array
	 */
	protected static $_singularized = array();

	/**
	 * Inflect a word to a camel case form with the first letter being capitalized.
	 * Example: Non-camel cased string = NonCamelCasedString
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function camelCase($string) {
		return str_replace(' ', '', ucwords(strtolower(str_replace(array('_', '-'), ' ', preg_replace('/[^-_a-z0-9\s]+/i', '', $string)))));
	}

	/**
	 * Inflect a word for a filename. Studly cased and capitalized.
	 * Example: file_Name = FileName.php (or fileName.php)
	 *
	 * @access public
	 * @param string $string
	 * @param string $ext
	 * @param boolean $capitalize
	 * @return string
	 * @static
	 */
	public static function filename($string, $ext = 'php', $capitalize = true) {
		if (strpos($string, '.') !== false) {
			$string = substr($string, 0, strrpos($string, '.'));
		}

		$path = self::camelCase($string);

		if (!$capitalize) {
			$path = lcfirst($path);
		}

		if (substr($path, -(strlen($ext) + 1)) !== '.' . $ext) {
			$path .= '.' . $ext;
		}

		return $path;
	}

	/**
	 * Inflect a word to a model name. Singular camel cased form.
	 * Example: People = Person
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function modelize($string) {
		return self::camelCase(self::singularize($string));
	}

	/**
	 * Inflect a word to a human readable string with only the first word capitalized.
	 * Example: non_human_readable_word = Non human readable word
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function normalCase($string) {
		return ucfirst(strtolower(str_replace('_', ' ', $string)));
	}

	/**
	 * Inflect a form to its pluralized form. Applies special rules to determine uninflected, irregular or regular forms.
	 * Example: Person = People
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function pluralize($string) {
		if (!Titon::g11n()->isEnabled()) {
			return $string;
		}

		$string = strtolower($string);
		$return = null;

		if (isset(self::$_pluralized[$string])) {
			return self::$_pluralized[$string];
		}

		$inflections = Titon::g11n()->current()->getInflections();

		if (empty($inflections)) {
			return $string;

		} else if (!empty($inflections['uninflected']) && in_array($string, $inflections['uninflected'])) {
			$return = $string;

		} else if (!empty($inflections['irregular']) && in_array($string, $inflections['irregular'])) {
			$return = array_search($string, $inflections['irregular']);

		} else if (!empty($inflections['plural'])) {
			foreach ($inflections['plural'] as $pattern => $replacement) {
				if (preg_match($pattern, $string)) {
					$return = preg_replace($pattern, $replacement, $string);
					break;
				}
			}
		}

		if (empty($return)) {
			$return = $string;
		}

		self::$_pluralized[$string] = $return;

		return $return;
	}

	/**
	 * Inflect a form to its pluralized form. Applies special rules to determine uninflected, irregular or regular forms.
	 * Example: People = Person
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function singularize($string) {
		if (!Titon::g11n()->isEnabled()) {
			return $string;
		}

		$string = strtolower($string);
		$return = null;

		if (isset(self::$_singularized[$string])) {
			return self::$_singularized[$string];
		}

		$inflections = Titon::g11n()->current()->getInflections();

		if (empty($inflections)) {
			return $string;

		} else if (!empty($inflections['uninflected']) && in_array($string, $inflections['uninflected'])) {
			$return = $string;

		} else if (!empty($inflections['irregular']) && in_array($string, $inflections['irregular'])) {
			$return = array_search($string, $inflections['irregular']);

		} else if (!empty($inflections['singular'])) {
			foreach ($inflections['singular'] as $pattern => $replacement) {
				if (preg_match($pattern, $string)) {
					$return = preg_replace($pattern, $replacement, $string);
					break;
				}
			}
		}

		if (empty($return)) {
			$return = $string;
		}

		self::$_singularized[$string] = $return;

		return $return;
	}

	/**
	 * Inflect a word to a URL friendly slug. Removes all punctuation and replaces spaces with dashes.
	 * Example: Some non-slugged word = some-non_slugged-word
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function slug($string) {
		return strtolower(str_replace(' ', '-', str_replace('-', '_', preg_replace('/[^-a-z0-9\s]+/i', '', $string))));
	}

	/**
	 * Inflect a word for a database table name. Formatted as plural and camel case with the first word lowercase.
	 * Example: User Profile = userProfiles
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function tableize($string) {
		return lcfirst(self::camelCase(self::pluralize($string)));
	}

	/**
	 * Inflect a word to a human readable string with all words capitalized.
	 * Example: non_human_readable_word = Non Human Readable Word
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function titleCase($string) {
		return ucwords(strtolower(str_replace('_', ' ', $string)));
	}

	/**
	 * Inflect a word to an underscore form that strips all punctuation and special characters and converts spaces to underscores.
	 * Example: Non-Underscore'd String = nonunderscored_string
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function underscore($string) {
		return trim(strtolower(str_replace('__', '_', preg_replace('/([A-Z]{1})/', '_$1', preg_replace('/[^_a-z0-9]+/i', '', preg_replace('/[\s]+/', '_', $string))))), '_');
	}

	/**
	 * Inflect a word to be used as a PHP variable. Strip all but letters, numbers and underscores. Add an underscore if the first letter is numeric.
	 * Example: 1some Variable = _1someVariable
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function variable($string) {
		$string = preg_replace('/[^_a-z0-9]+/i', '', $string);

		if (is_numeric(substr($string, 0, 1))) {
			$string = '_' . $string;
		}

		return $string;
	}

}