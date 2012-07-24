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
use \Closure;

/**
 * String and grammar inflection. Converts strings to a certain format. Camel cased, singular, plural etc.
 *
 * @package	titon.utility
 * @link	http://php.net/manual/book.mbstring.php
 */
class Inflector {

	/**
	 * Cached inflections for all methods.
	 *
	 * @access protected
	 * @var array
	 * @static
	 */
	protected static $_cache = [];

	/**
	 * Inflect a word to a camel case form with the first letter being capitalized.
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function camelCase($string) {
		return self::_cache([__METHOD__, $string], function() use ($string) {
			return str_replace(' ', '', mb_convert_case(str_replace(['_', '-'], ' ', preg_replace('/[^-_a-z0-9\s]+/i', '', $string)), MB_CASE_TITLE));
		});
	}

	/**
	 * Inflect a word to a class name. Singular camel cased form.
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function className($string) {
		return self::_cache([__METHOD__, $string], function() use ($string) {
			return self::camelCase(self::singularize($string));
		});
	}

	/**
	 * Inflect a word for a filename. Studly cased and capitalized.
	 *
	 * @access public
	 * @param string $string
	 * @param string $ext
	 * @param boolean $capitalize
	 * @return string
	 * @static
	 */
	public static function fileName($string, $ext = 'php', $capitalize = true) {
		if (mb_strpos($string, '.') !== false) {
			$string = mb_substr($string, 0, mb_strrpos($string, '.'));
		}

		$path = self::camelCase($string);

		if (!$capitalize) {
			$path = lcfirst($path);
		}

		if (mb_substr($path, -(mb_strlen($ext) + 1)) !== '.' . $ext) {
			$path .= '.' . $ext;
		}

		return $path;
	}

	/**
	 * Inflect a word to a human readable string with only the first word capitalized and the rest lowercased.
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function normalCase($string) {
		return self::_cache([__METHOD__, $string], function() use ($string) {
			return ucfirst(mb_strtolower(str_replace('_', ' ', $string)));
		});
	}

	/**
	 * Inflect a number by appending its ordinal suffix: st, nd, rd, th, etc.
	 *
	 * @access public
	 * @param int $number
	 * @return string
	 * @static
	 */
	public static function ordinal($number) {
		if (!Titon::g11n()->isEnabled()) {
			return $number;
		}

		return self::_cache([__METHOD__, $number], function() use ($number) {
			$inflections = Titon::g11n()->current()->getInflections();
			$number = (int) $number;

			if (empty($inflections) || empty($inflections['ordinal'])) {
				return $number;
			}

			$ordinal = $inflections['ordinal'];

			// Teens 11-13
			if (in_array(($number % 100), range(11, 13)) && isset($ordinal['default'])) {
				return str_replace('#', $number, $ordinal['default']);
			}

			// First, second, third
			$modNumber = $number % 10;

			foreach ($ordinal as $i => $format) {
				if (is_numeric($i) && $modNumber === $i) {
					return str_replace('#', $number, $ordinal[$i]);
				}
			}

			// Fallback
			if (isset($ordinal['default'])) {
				return str_replace('#', $number, $ordinal['default']);
			}

			return $number;
		});
	}

	/**
	 * Inflect a form to its pluralized form. Applies special rules to determine uninflected, irregular or regular forms.
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

		return self::_cache([__METHOD__, $string], function() use ($string) {
			$string = mb_strtolower($string);
			$result = null;
			$inflections = Titon::g11n()->current()->getInflections();

			if (empty($inflections)) {
				return $string;

			} else if (!empty($inflections['uninflected']) && in_array($string, $inflections['uninflected'])) {
				$result = $string;

			} else if (!empty($inflections['irregular']) && isset($inflections['irregular'][$string])) {
				$result = $inflections['irregular'][$string];

			} else if (!empty($inflections['irregular']) && in_array($string, $inflections['irregular'])) {
				$result = $string;

			} else if (!empty($inflections['plural'])) {
				foreach ($inflections['plural'] as $pattern => $replacement) {
					if (preg_match($pattern, $string)) {
						$result = preg_replace($pattern, $replacement, $string);
						break;
					}
				}
			}

			if (empty($result)) {
				$result = $string;
			}

			return $result;
		});
	}

	/**
	 * Inflect a word to a routeable format. All non-alphanumeric characters will be removed, and any spaces or underscores will be changed to dashes.
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function route($string) {
		return self::_cache([__METHOD__, $string], function() use ($string) {
			return str_replace([' ', '_'], '-', preg_replace('/[^-_a-z0-9\s]+/i', '', preg_replace('/\s{2,}+/', ' ', $string)));
		});
	}

	/**
	 * Inflect a form to its pluralized form. Applies special rules to determine uninflected, irregular or regular forms.
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

		return self::_cache([__METHOD__, $string], function() use ($string) {
			$string = mb_strtolower($string);
			$result = null;
			$inflections = Titon::g11n()->current()->getInflections();

			if (empty($inflections)) {
				return $string;

			} else if (!empty($inflections['uninflected']) && in_array($string, $inflections['uninflected'])) {
				$result = $string;

			} else if (!empty($inflections['irregular']) && in_array($string, $inflections['irregular'])) {
				$result = array_search($string, $inflections['irregular']);

			} else if (!empty($inflections['irregular']) && isset($inflections['irregular'][$string])) {
				$result = $string;

			} else if (!empty($inflections['singular'])) {
				foreach ($inflections['singular'] as $pattern => $replacement) {
					if (preg_match($pattern, $string)) {
						$result = preg_replace($pattern, $replacement, $string);
						break;
					}
				}
			}

			if (empty($result)) {
				$result = $string;
			}

			return $result;
		});
	}

	/**
	 * Inflect a word to a URL friendly slug. Removes all punctuation, replaces dashes with underscores and spaces with dashes.
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function slug($string) {
		return self::_cache([__METHOD__, $string], function() use ($string) {
			// Revert entities
			$string = html_entity_decode($string, ENT_QUOTES, Titon::config()->encoding());

			// Remove non-ascii characters
			$string = preg_replace('/[^-a-z0-9\s]+/i', '', self::transliterate($string));

			// Replace dashes and underscores
			$string = str_replace(' ', '-', str_replace('-', '_', $string));

			return mb_strtolower($string);
		});
	}

	/**
	 * Inflect a word for a database table name. Formatted as plural and camel case with the first letter lowercase.
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function tableName($string) {
		return self::_cache([__METHOD__, $string], function() use ($string) {
			return lcfirst(self::camelCase(self::pluralize($string)));
		});
	}

	/**
	 * Inflect a word to a human readable string with all words capitalized.
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function titleCase($string) {
		return self::_cache([__METHOD__, $string], function() use ($string) {
			return mb_convert_case(str_replace('_', ' ', $string), MB_CASE_TITLE);
		});
	}

	/**
	 * Inflect a word by replacing all non-ASCII characters with there equivalents.
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function transliterate($string) {
		if (!Titon::g11n()->isEnabled()) {
			return $string;
		}

		return self::_cache([__METHOD__, $string], function() use ($string) {
			$inflections = Titon::g11n()->current()->getInflections();

			if (empty($inflections) || empty($inflections['transliteration'])) {
				return $string;
			}

			// Replace with ASCII characters
			$transliterations = $inflections['transliteration'];
			$string = preg_replace(array_keys($transliterations), array_values($transliterations), $string);

			// Remove any left over non 7bit ASCII
			return preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $string);
		});
	}

	/**
	 * Inflect a word to an underscore form that strips all punctuation and special characters and converts spaces to underscores.
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function underscore($string) {
		return self::_cache([__METHOD__, $string], function() use ($string) {
			return trim(mb_strtolower(str_replace('__', '_', preg_replace('/([A-Z]{1})/', '_$1', preg_replace('/[^_a-z0-9]+/i', '', preg_replace('/[\s]+/', '_', $string))))), '_');
		});
	}

	/**
	 * Inflect a word to be used as a PHP variable. Strip all but letters, numbers and underscores. Add an underscore if the first letter is numeric.
	 *
	 * @access public
	 * @param string $string
	 * @return string
	 * @static
	 */
	public static function variable($string) {
		$string = preg_replace('/[^_a-z0-9]+/i', '', $string);

		if (is_numeric(mb_substr($string, 0, 1))) {
			$string = '_' . $string;
		}

		return $string;
	}

	/**
	 * Cache the result of an inflection by using a Closure.
	 *
	 * @access protected
	 * @param string|array $key
	 * @param Closure $callback
	 * @return mixed
	 * @static
	 */
	protected static function _cache($key, Closure $callback) {
		if (is_array($key)) {
			$key = implode('-', $key);
		}

		if (isset(self::$_cache[$key])) {
			return self::$_cache[$key];
		}

		$callback = Closure::bind($callback, null, __CLASS__);

		self::$_cache[$key] = $callback();

		return self::$_cache[$key];
	}

}