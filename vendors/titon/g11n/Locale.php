<?php

namespace titon\locale;

use \titon\core\Config;

class L10n {

    /**
	 * List of words that have irregular/weird plural and singular forms.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__irregular = array(
		'en_us' => array(
			'person' => 'people', 'man' => 'men', 'child' => 'children',
			'sex' => 'sexes', 'move' => 'moves', 'atlas' => 'atlases',
			'beef' => 'beefs', 'brother' => 'brothers', 'corpus' => 'corpuses',
			'cow' => 'cows', 'ganglion' => 'ganglions', 'genie' => 'genies',
			'genus' => 'genera', 'graffito' => 'graffiti', 'hoof' => 'hoofs',
			'loaf' => 'loaves', 'money' => 'monies', 'mongoose' => 'mongooses',
			'mythos' => 'mythoi', 'numen' => 'numina', 'occiput' => 'occiputs',
			'octopus' => 'octopuses', 'opus' => 'opuses', 'ox' => 'oxen',
			'penis' => 'penises', 'soliloquy' => 'soliloquies', 'testis' => 'testes',
			'trilby' => 'trilbys', 'turf' => 'turfs', 'woman' => 'women'
		)
	);

	/**
	 * List of locales that are supported for inflection.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__locales = array('en_us');

	/**
	 * Rules to check for plural formed words.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__plural = array(
		'en_us' => array(
			'/(quiz)$/i' => '\1zes',
			'/^(ox)$/i' => '\1en',
			'/([m|l])ouse$/i' => '\1ice',
			'/(matr|vert|ind)ix|ex$/i' => '\1ices',
			'/(x|ch|ss|sh)$/i' => '\1es',
			'/([^aeiouy]|qu)ies$/i' => '\1y',
			'/([^aeiouy]|qu)y$/i' => '\1ies',
			'/(hive)$/i' => '\1s',
			'/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
			'/sis$/i' => 'ses',
			'/([ti])um$/i' => '\1a',
			'/(buffal|tomat)o$/i' => '\1oes',
			'/(bu)s$/i' => '\1ses',
			'/(alias|status)/i'=> '\1es',
			'/(octop|vir)us$/i'=> '\1i',
			'/(ax|test)is$/i'=> '\1es',
			'/s$/i'=> 's',
			'/$/'=> 's'
		)
	);

	/**
	 * List of words that have been pluralized for this request.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__pluralized = array();

	/**
	 * Rules to check for singular formed words.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__singular = array(
		'en_us' => array(
			'/(quiz)zes$/i' => '\\1',
			'/(matr)ices$/i' => '\\1ix',
			'/(vert|ind)ices$/i' => '\\1ex',
			'/^(ox)en/i' => '\\1',
			'/(alias|status)es$/i' => '\\1',
			'/([octop|vir])i$/i' => '\\1us',
			'/(cris|ax|test)es$/i' => '\\1is',
			'/(shoe)s$/i' => '\\1',
			'/(o)es$/i' => '\\1',
			'/(bus)es$/i' => '\\1',
			'/([m|l])ice$/i' => '\\1ouse',
			'/(x|ch|ss|sh)es$/i' => '\\1',
			'/(m)ovies$/i' => '\\1ovie',
			'/(s)eries$/i' => '\\1eries',
			'/([^aeiouy]|qu)ies$/i' => '\\1y',
			'/([lr])ves$/i' => '\\1f',
			'/(tive)s$/i' => '\\1',
			'/(hive)s$/i' => '\\1',
			'/([^f])ves$/i' => '\\1fe',
			'/(^analy)ses$/i' => '\\1sis',
			'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\\1\\2sis',
			'/([ti])a$/i' => '\\1um',
			'/(n)ews$/i' => '\\1ews',
			'/s$/i' => '',
		)
	);

	/**
	 * List of words that have been singularized for this request.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__singularized = array();

	/**
	 * List of words that should not be inflected for singular or plural.
	 *
	 * @access private
	 * @var array
	 * @static
	 */
	private static $__uninflected = array(
		'en_us' => array(
			'equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep',
			'amoyese', 'bison', 'borghese', 'bream', 'breeches', 'britches', 'buffalo', 'cantus',
			'carp', 'chassis', 'clippers', 'cod', 'coitus', 'congoese', 'contretemps', 'corps',
			'debris', 'diabetes', 'djinn', 'eland', 'elk', 'faroese', 'flounder',
			'foochowese', 'gallows', 'genevese', 'genoese', 'gilbertese', 'graffiti',
			'headquarters', 'herpes', 'hijinks', 'hottentotese', 'innings',
			'jackanapes', 'kiplingese', 'kongoese', 'lucchese', 'mackerel', 'maltese', 'media',
			'mews', 'moose', 'mumps', 'nankingese', 'news', 'nexus', 'niasese',
			'pekingese', 'piedmontese', 'pincers', 'pistoiese', 'pliers', 'portuguese',
			'proceedings', 'rabies', 'rice', 'rhinoceros', 'salmon', 'sarawakese', 'scissors',
			'seabass', 'series', 'shavese', 'shears', 'siemens', 'species', 'swine', 'testes',
			'trousers', 'trout', 'tuna', 'vermontese', 'wenchowese', 'whiting', 'wildebeest',
			'yengeese'
		)
	);

    /**
	 * Adds a catalog of inflection rules and patterns for a certain locale.
	 *
	 * @access public
	 * @param string $locale
	 * @param array $rules
	 * @return boolean
	 * @static
	 */
	public static function addCatalog($locale, array $rules = array()) {
		if (!empty($rules) && !empty($locale)) {
			self::$__locales[] = $locale;
			self::$__irregular[$locale] = array();
			self::$__plural[$locale] = array();
			self::$__singular[$locale] = array();
			self::$__uninflected[$locale] = array();

			foreach ($rules as $group => $ruleSets) {
				self::${'__' . $group}[$locale] = $ruleSets;
			}

			return true;
		}

		return false;
	}

	/**
	 * Add additional rules to certain locales and specific rulesets. Returns true if rules applied, else false.
	 *
	 * @access public
	 * @param string|null $locale
	 * @param array $rules
	 * @return boolean
	 * @static
	 */
	public static function addRules($locale, $rules = array()) {
		if (!$locale) {
			$locale = Titon::get('config')->get('Locale.default');
		}

		if (!empty($rules)) {
			foreach ($rules as $group => $ruleSets) {
				if (!isset(self::${'__' . $group}[$locale])) {
					self::${'__' . $group}[$locale] = array();
				}

				self::${'__' . $group}[$locale] = $ruleSets + self::${'__' . $group}[$locale];
			}

			return true;
		}

		return false;
	}

    /**
	 * Check to see if a locale is supported, if not default to en_US (or custom default).
	 *
	 * @access public
	 * @return string
	 * @static
	 */
	public static function getLocale() {
		$locale = Titon::get('config')->get('Locale.current');

		if (in_array($locale, self::$__locales)) {
			return strtolower($locale);
		}

		return strtolower(Titon::get('config')->get('Locale.default'));
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
		$locale = self::getLocale();
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

		return $string;
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
		$locale = self::getLocale();
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
		return $string;
	}
    
}