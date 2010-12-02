<?php
/**
 * String and grammar inflection. Converts strings to a certain format. Camel cased, singular, plural etc.
 *
 * @copyright	Copyright 2009, Titon (A PHP Micro Framework)
 * @link		http://titonphp.com
 * @license		http://opensource.org/licenses/bsd-license.php (The BSD License)
 */

namespace titon\utility;

use \titon\locale\L10n;

/**
 * Inflector Class
 * 
 * @package		Titon
 * @subpackage	Titon.Utility
 */ 
class Inflector {
	
	/**
	 * Inflect a word to a camel case form with the first letter being capitalized.
	 * Example: Non-Camel Case'd String = NonCamelCasedString
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
	 * @return string
	 * @access static
	 */
	public static function filename($string, $ext = 'php') {
		return ucfirst(static::camelize($string)) .'.'. $ext;
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
		return static::camelize(static::singularize($string));
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
		return L10n::pluralize($string);
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
		return L10n::singularize($string);
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
		$string = preg_replace('/[^-A-Za-z0-9\s]+/', '', $string);
		$string = str_replace(' ', '-', ucwords(str_replace('-', '_', $string)));
		return $string;
	}
	
	/**
	 * Inflect a word for a database table name. Formatted as plural and camel case with the first word lowercase.
	 * Example: User Profile = userProfiles
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @access static
	 */
	public static function tableize($string) { 
		return lcfirst(static::camelize(static::pluralize($string)));
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
			$string = '_'. $string;
		}

		return $string;
	}

}