<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

namespace titon\utility;

/**
 * String and grammar inflection. Converts strings to a certain format. Camel cased, singular, plural etc.
 *
 * @package	titon.utility
 * 
 * @link	http://php.net/manual/book.mbstring.php
 */
class Inflector {

	/**
	 * Inflect a word to a camel case form with the first letter being capitalized.
	 * Example: Non-Camel Cased String = NonCamelCasedString
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function camelize($string) {
		return str_replace(' ', '', ucwords(strtolower(str_replace(array('_', '-'), ' ', preg_replace('/[^-_A-Za-z0-9\s]+/', '', $string)))));
	}

	/**
	 * Inflect a word for a filename. Camel cased and capitalized.
	 * Example: file_Name = FileName.php
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

		$path = self::camelize($string);
		
		if (!$capitalize) {
			$path = lcfirst($path);
		}

		if (substr($path, -(strlen($ext) + 1)) != '.' . $ext) {
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
		return self::camelize($string);
	}

	/**
	 * Inflect a word to a normal human readable string. Can uppercase the first letter of all words or the first word.
	 * Example: non_human_readable_word = Non human readable word
	 *
	 * @access public
	 * @param string $string
	 * @param string $callback
	 * @return string
	 * @static
	 */
	public static function normalize($string, $callback = 'ucwords') {
		$string = strtolower(str_replace('_', ' ', $string));

		if ($callback && function_exists($callback)) {
			$string = $callback($string);
		}

		return $string;
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
		/*$locale = self::getLocale();
		$string = strtolower($string);

		if (isset(self::$__pluralized[$string])) {
			return self::$__pluralized[$string];
		}else if (isset(self::$__uninflected[$locale]) && in_array($string, self::$__uninflected[$locale])) {
			return $string;
		} else if (isset(self::$__irregular[$locale]) && isset(self::$__irregular[$locale][$string])) {
			return self::$__irregular[$locale][$string];
		}

		if (isset(self::$__plural[$locale])) {
			foreach (self::$__plural[$locale] as $pattern => $replacement) {
				if (preg_match($pattern, $string)) {
					self::$__pluralized[$string] = preg_replace($pattern, $replacement, $string);
					return self::$__pluralized[$string];
				}
			}
		}

		return $string;*/
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
		/*$locale = self::getLocale();
		$string = strtolower($string);

		if (isset(self::$__singularized[$string])) {
			return self::$__singularized[$string];
		} else if (isset(self::$__uninflected[$locale]) && in_array($string, self::$__uninflected[$locale])) {
			return $string;
		} else if (isset(self::$__irregular[$locale]) && in_array($string, self::$__irregular[$locale])) {
			return array_search($string, self::$__irregular[$locale]);
		}

		if (isset(self::$__singular[$locale])) {
			foreach (self::$__singular[$locale] as $pattern => $replacement) {
				if (preg_match($pattern, $string)) {
					self::$__singularized[$string] = preg_replace($pattern, $replacement, $string);
					return self::$__singularized[$string];
				}
			}
		}

		self::$__singularized[$string] = $string;
		return $string;*/
	}

	/**
	 * Inflect a word to a URL friendly slug. Removes all punctuation and replaces spaces with dashes.
	 * Example: Some non-slugged word = Some-Non_slugged-Word
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function slugify($string) {
		return strtolower(str_replace(' ', '-', str_replace('-', '_', preg_replace('/[^-A-Za-z0-9\s]+/', '', $string))));
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
		return lcfirst(self::camelize(self::pluralize($string)));
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
		return strtolower(preg_replace('/(\w)([A-Z]{1})/', '$1_$2', preg_replace('/[^_A-Za-z0-9\s]+/', '', $string)));
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
		$string = preg_replace('/[^_A-Za-z0-9]+/', '', $string);

		if (is_numeric(substr($string, 0, 1))) {
			$string = '_' . $string;
		}

		return $string;
	}

}