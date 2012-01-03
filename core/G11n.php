<?php

namespace titon\core;

use \titon\libs\storage\Storage;
use \titon\libs\translators\Translator;

class G11n {

	/**
	 * Possible formats for locale keys.
	 */
	const FORMAT_1 = 1;
	const FORMAT_2 = 2;
	const FORMAT_3 = 3;
	
	/**
	 * Currently active locale key based on the client.
	 * 
	 * @access protected
	 * @var string
	 */
	protected $_current;
	
	/**
	 * Fallback locale key if none can be found.
	 * 
	 * @access protected
	 * @var string
	 */
	protected $_fallback;
	
	/**
	 * Supported locales and related meta data.
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_locales = array();

	/**
	 * Storage engine for caching.
	 * 
	 * @access protected
	 * @var Storage
	 */
	protected $_storage;

	/**
	 * A list of all possible locale codes based on the ISO-639 and ISO-3166 standards.
	 * 
	 * @link http://en.wikipedia.org/wiki/IETF_language_tag
	 * @link http://en.wikipedia.org/wiki/ISO_639
	 * @link http://en.wikipedia.org/wiki/ISO_3166-1
	 * @link http://loc.gov/standards/iso639-2/php/code_list.php
	 * 
	 * @access protected
	 * @var array
	 */
	protected $_supportedLocales = array(
		'aa'	=> array('id' => 'aa',		'iso2' => 'aa',	'iso3' => 'aar', 'timezone' => '', 'language' => 'Afar'),
		'ab'	=> array('id' => 'ab',		'iso2' => 'ab',	'iso3' => 'abk', 'timezone' => '', 'language' => 'Abkhazian'),
		'ae'	=> array('id' => 'ae',		'iso2' => 'ae',	'iso3' => 'ave', 'timezone' => '', 'language' => 'Avestan'),
		'af'	=> array('id' => 'af',		'iso2' => 'af',	'iso3' => 'afr', 'timezone' => '', 'language' => 'Afrikaans'),
		'ak'	=> array('id' => 'ak',		'iso2' => 'ak',	'iso3' => 'aka', 'timezone' => '', 'language' => 'Akan'),
		'am'	=> array('id' => 'am',		'iso2' => 'am',	'iso3' => 'amh', 'timezone' => '', 'language' => 'Amharic'),
		'an'	=> array('id' => 'an',		'iso2' => 'an',	'iso3' => 'arg', 'timezone' => '', 'language' => 'Aragonese'),
		'ar'	=> array('id' => 'ar',		'iso2' => 'ar',	'iso3' => 'ara', 'timezone' => '', 'language' => 'Arabic'),
		'ar-ae'	=> array('id' => 'ar_AE',	'fallback' => 'ar', 'timezone' => '', 'language' => 'Arabic (U.A.E.)'),
		'ar-bh'	=> array('id' => 'ar_BH',	'fallback' => 'ar', 'timezone' => '', 'language' => 'Arabic (Bahrain)'),
		'ar-dz'	=> array('id' => 'ar_DZ',	'fallback' => 'ar', 'timezone' => '', 'language' => 'Arabic (Algeria)'),
		'ar-eg'	=> array('id' => 'ar_EG',	'fallback' => 'ar', 'timezone' => '', 'language' => 'Arabic (Egypt)'),
		'ar-iq'	=> array('id' => 'ar_IQ',	'fallback' => 'ar', 'timezone' => '', 'language' => 'Arabic (Iraq)'),
		'ar-jo'	=> array('id' => 'ar_JO',	'fallback' => 'ar', 'timezone' => '', 'language' => 'Arabic (Jordan)'),
		'ar-kw'	=> array('id' => 'ar_KW',	'fallback' => 'ar', 'timezone' => '', 'language' => 'Arabic (Kuwait)'),
		'ar-lb'	=> array('id' => 'ar_LB',	'fallback' => 'ar', 'timezone' => '', 'language' => 'Arabic (Lebanon)'),
		'ar-ly'	=> array('id' => 'ar_LY',	'fallback' => 'ar', 'timezone' => '', 'language' => 'Arabic (Libya)'),
		'ar-ma'	=> array('id' => 'ar_MA',	'fallback' => 'ar', 'timezone' => '', 'language' => 'Arabic (Morocco)'),
		'ar-om'	=> array('id' => 'ar_OM',	'fallback' => 'ar', 'timezone' => '', 'language' => 'Arabic (Oman)'),
		'ar-qa'	=> array('id' => 'ar_QA',	'fallback' => 'ar', 'timezone' => '', 'language' => 'Arabic (Qatar)'),
		'ar-sa'	=> array('id' => 'ar_SA',	'fallback' => 'ar', 'timezone' => '', 'language' => 'Arabic (Saudi Arabia)'),
		'ar-sy'	=> array('id' => 'ar_SY',	'fallback' => 'ar', 'timezone' => '', 'language' => 'Arabic (Syria)'),
		'ar-tn'	=> array('id' => 'ar_TN',	'fallback' => 'ar', 'timezone' => '', 'language' => 'Arabic (Tunisia)'),
		'ar-ye'	=> array('id' => 'ar_YE',	'fallback' => 'ar', 'timezone' => '', 'language' => 'Arabic (Yemen)'),
		'av'	=> array('id' => 'av',		'iso2' => 'av',	'iso3' => 'ava', 'timezone' => '', 'language' => 'Avaric'),
		'ay'	=> array('id' => 'ay',		'iso2' => 'ay',	'iso3' => 'aym', 'timezone' => '', 'language' => 'Aymara'),
		'az'	=> array('id' => 'az',		'iso2' => 'az',	'iso3' => 'aze', 'timezone' => '', 'language' => 'Azerbaijani'),
		'az-az'	=> array('id' => 'az_AZ',	'fallback' => 'az', 'timezone' => '', 'language' => 'Azerbaijani (Cyrillic)'),
		'ba'	=> array('id' => 'ba',		'iso2' => 'ba',	'iso3' => 'bak', 'timezone' => '', 'language' => 'Bashkir'),
		'be'	=> array('id' => 'be',		'iso2' => 'be',	'iso3' => 'bel', 'timezone' => '', 'language' => 'Belarusian'),
		'bg'	=> array('id' => 'bg',		'iso2' => 'bg',	'iso3' => 'bul', 'timezone' => '', 'language' => 'Bulgarian'),
		'bi'	=> array('id' => 'bi',		'iso2' => 'bi',	'iso3' => 'bis', 'timezone' => '', 'language' => 'Bislama'),
		'bh'	=> array('id' => 'bh',		'iso2' => 'bh',	'iso3' => 'bih', 'timezone' => '', 'language' => 'Bihari'),
		'bm'	=> array('id' => 'bm',		'iso2' => 'bm',	'iso3' => 'bam', 'timezone' => '', 'language' => 'Bambara'),
		'bn'	=> array('id' => 'bn',		'iso2' => 'bn',	'iso3' => 'ben', 'timezone' => '', 'language' => 'Bengali'),
		'bo'	=> array('id' => 'bo',		'iso2' => 'bo',	'iso3' => array('tib', 'bod'), 'timezone' => '', 'language' => 'Tibetan'),
		'bo-cn'	=> array('id' => 'bo_CN',	'fallback' => 'bo', 'timezone' => '', 'language' => 'Tibetan (China)'),
		'bo-in'	=> array('id' => 'bo_IN',	'fallback' => 'bo', 'timezone' => '', 'language' => 'Tibetan (India)'),
		'br'	=> array('id' => 'br',		'iso2' => 'br',	'iso3' => 'bre', 'timezone' => '', 'language' => 'Breton'),
		'bs'	=> array('id' => 'bs',		'iso2' => 'bs',	'iso3' => 'bos', 'timezone' => '', 'language' => 'Bosnian'),
		'ca'	=> array('id' => 'ca',		'iso2' => 'ca',	'iso3' => 'cat', 'timezone' => '', 'language' => 'Catalan'),
		'ch'	=> array('id' => 'ch',		'iso2' => 'ch',	'iso3' => 'cha', 'timezone' => '', 'language' => 'Chamorro'),
		'ce'	=> array('id' => 'ce',		'iso2' => 'ce',	'iso3' => 'che', 'timezone' => '', 'language' => 'Chechen'),
		'co'	=> array('id' => 'co',		'iso2' => 'co',	'iso3' => 'cos', 'timezone' => '', 'language' => 'Corsican'),
		'cr'	=> array('id' => 'cr',		'iso2' => 'cr',	'iso3' => 'cre', 'timezone' => '', 'language' => 'Cree'),
		'cs'	=> array('id' => 'cs',		'iso2' => 'cs',	'iso3' => array('ces', 'cze'), 'timezone' => '', 'language' => 'Czech'),
		'cv'	=> array('id' => 'cv',		'iso2' => 'cv',	'iso3' => 'chv', 'timezone' => '', 'language' => 'Chuvash'),
		'cy'	=> array('id' => 'cy',		'iso2' => 'cy',	'iso3' => array('wel', 'cym'), 'timezone' => '', 'language' => 'Welsh'),
		'da'	=> array('id' => 'da',		'iso2' => 'da',	'iso3' => 'dan', 'timezone' => '', 'language' => 'Danish'),
		'de'	=> array('id' => 'de',		'iso2' => 'de',	'iso3' => array('ger', 'deu'), 'timezone' => '', 'language' => 'German (Standard)'),
		'de-at'	=> array('id' => 'de_AT',	'fallback' => 'de', 'timezone' => '', 'language' => 'German (Austria)'),
		'de-ch'	=> array('id' => 'de_CH',	'fallback' => 'de', 'timezone' => '', 'language' => 'German (Swiss)'),
		'de-de'	=> array('id' => 'de_DE',	'fallback' => 'de', 'timezone' => '', 'language' => 'German (Germany)'),
		'de-li'	=> array('id' => 'de_LI',	'fallback' => 'de', 'timezone' => '', 'language' => 'German (Liechtenstein)'),
		'de-lu'	=> array('id' => 'de_LU',	'fallback' => 'de', 'timezone' => '', 'language' => 'German (Luxembourg)'),
		'dv'	=> array('id' => 'dv',		'iso2' => 'dv',	'iso3' => 'div', 'timezone' => '', 'language' => 'Divehi'),
		'dz'	=> array('id' => 'dz',		'iso2' => 'dz',	'iso3' => 'dzo', 'timezone' => '', 'language' => 'Dzongkha'),
		'ee'	=> array('id' => 'ee',		'iso2' => 'ee',	'iso3' => 'ewe', 'timezone' => '', 'language' => 'Ewe'),
		'el'	=> array('id' => 'el',		'iso2' => 'el',	'iso3' => array('gre', 'ell'), 'timezone' => '', 'language' => 'Greek'),
		'en'	=> array('id' => 'en',		'iso2' => 'en',	'iso3' => 'eng', 'timezone' => '', 'language' => 'English'),
		'en-au'	=> array('id' => 'en_AU',	'fallback' => 'en', 'timezone' => '', 'language' => 'English (Australian)'),
		'en-bz'	=> array('id' => 'en_BZ',	'fallback' => 'en', 'timezone' => '', 'language' => 'English (Belize)'),
		'en-ca'	=> array('id' => 'en_CZ',	'fallback' => 'en', 'timezone' => '', 'language' => 'English (Canadian)'),
		'en-cb'	=> array('id' => 'en_CB',	'fallback' => 'en', 'timezone' => '', 'language' => 'English (Caribbean)'),
		'en-gb'	=> array('id' => 'en_GB',	'fallback' => 'en', 'timezone' => '', 'language' => 'English (British)'),
		'en-in'	=> array('id' => 'en_IN',	'fallback' => 'en', 'timezone' => '', 'language' => 'English (India)'),
		'en-ie'	=> array('id' => 'en_IE',	'fallback' => 'en', 'timezone' => '', 'language' => 'English (Ireland)'),
		'en-jm'	=> array('id' => 'en_JM',	'fallback' => 'en', 'timezone' => '', 'language' => 'English (Jamaica)'),
		'en-nz'	=> array('id' => 'en_NZ',	'fallback' => 'en', 'timezone' => '', 'language' => 'English (New Zealand)'),
		'en-ph'	=> array('id' => 'en_PH',	'fallback' => 'en', 'timezone' => '', 'language' => 'English (Phillippines)'),
		'en-tt'	=> array('id' => 'en_TT',	'fallback' => 'en', 'timezone' => '', 'language' => 'English (Trinidad)'),
		'en-us'	=> array('id' => 'en_US',	'fallback' => 'en', 'timezone' => '', 'language' => 'English (United States)'),
		'en-za'	=> array('id' => 'en_ZA',	'fallback' => 'en', 'timezone' => '', 'language' => 'English (South Africa)'),
		'eo'	=> array('id' => 'eo',		'iso2' => 'eo',	'iso3' => 'epo', 'timezone' => '', 'language' => 'Esperanto'),
		'es'	=> array('id' => 'es',		'iso2' => 'es',	'iso3' => 'spa', 'timezone' => '', 'language' => 'Spanish (Spain - Traditional)'),
		'es-ar'	=> array('id' => 'es_AR',	'fallback' => 'es', 'timezone' => '', 'language' => 'Spanish (Argentina)'),
		'es-bo'	=> array('id' => 'es_BO',	'fallback' => 'es', 'timezone' => '', 'language' => 'Spanish (Bolivia)'),
		'es-cl'	=> array('id' => 'es_CL',	'fallback' => 'es', 'timezone' => '', 'language' => 'Spanish (Chile)'),
		'es-co'	=> array('id' => 'es_CO',	'fallback' => 'es', 'timezone' => '', 'language' => 'Spanish (Colombia)'),
		'es-cr'	=> array('id' => 'es_CR',	'fallback' => 'es', 'timezone' => '', 'language' => 'Spanish (Costa Rica)'),
		'es-do'	=> array('id' => 'es_DO',	'fallback' => 'es', 'timezone' => '', 'language' => 'Spanish (Dominican Republic)'),
		'es-ec'	=> array('id' => 'es_EC',	'fallback' => 'es', 'timezone' => '', 'language' => 'Spanish (Ecuador)'),
		'es-es'	=> array('id' => 'es_ES',	'fallback' => 'es', 'timezone' => '', 'language' => 'Spanish (Spain)'),
		'es-gt'	=> array('id' => 'es_GT',	'fallback' => 'es', 'timezone' => '', 'language' => 'Spanish (Guatemala)'),
		'es-hn'	=> array('id' => 'es_HN',	'fallback' => 'es', 'timezone' => '', 'language' => 'Spanish (Honduras)'),
		'es-mx'	=> array('id' => 'es_MX',	'fallback' => 'es', 'timezone' => '', 'language' => 'Spanish (Mexico)'),
		'es-ni'	=> array('id' => 'es_NI',	'fallback' => 'es', 'timezone' => '', 'language' => 'Spanish (Nicaragua)'),
		'es-pa'	=> array('id' => 'es_PA',	'fallback' => 'es', 'timezone' => '', 'language' => 'Spanish (Panama)'),
		'es-pe'	=> array('id' => 'es_PE',	'fallback' => 'es', 'timezone' => '', 'language' => 'Spanish (Peru)'),
		'es-pr'	=> array('id' => 'es_PR',	'fallback' => 'es', 'timezone' => '', 'language' => 'Spanish (Puerto Rico)'),
		'es-py'	=> array('id' => 'es_PY',	'fallback' => 'es', 'timezone' => '', 'language' => 'Spanish (Paraguay)'),
		'es-sv'	=> array('id' => 'es_SV',	'fallback' => 'es', 'timezone' => '', 'language' => 'Spanish (El Salvador)'),
		'es-uy'	=> array('id' => 'es_UY',	'fallback' => 'es', 'timezone' => '', 'language' => 'Spanish (Uruguay)'),
		'es-ve'	=> array('id' => 'es_VE',	'fallback' => 'es', 'timezone' => '', 'language' => 'Spanish (Venezuela)'),
		'et'	=> array('id' => 'et',		'iso2' => 'et',	'iso3' => 'est', 'timezone' => '', 'language' => 'Estonian'),
		'eu'	=> array('id' => 'eu',		'iso2' => 'eu',	'iso3' => array('baq', 'eus'), 'timezone' => '', 'language' => 'Basque'),
		'fa'	=> array('id' => 'fa',		'iso2' => 'fa',	'iso3' => array('fas', 'per'), 'timezone' => '', 'language' => 'Farsi / Persian'),
		'ff'	=> array('id' => 'ff',		'iso2' => 'ff',	'iso3' => 'ful', 'timezone' => '', 'language' => 'Fulah'),
		'fi'	=> array('id' => 'fi',		'iso2' => 'fi',	'iso3' => 'fin', 'timezone' => '', 'language' => 'Finnish'),
		'fj'	=> array('id' => 'fj',		'iso2' => 'fj',	'iso3' => 'fij', 'timezone' => '', 'language' => 'Fijian'),
		'fo'	=> array('id' => 'fo',		'iso2' => 'fo',	'iso3' => 'fao', 'timezone' => '', 'language' => 'Faeroese'),
		'fr'	=> array('id' => 'fr',		'iso2' => 'fr',	'iso3' => array('fra', 'fre'), 'timezone' => '', 'language' => 'French (Standard)'),
		'fr-be'	=> array('id' => 'fr_BE',	'fallback' => 'fr', 'timezone' => '', 'language' => 'French (Belgium)'),
		'fr-ca'	=> array('id' => 'fr_CA',	'fallback' => 'fr', 'timezone' => '', 'language' => 'French (Canadian)'),
		'fr-ch'	=> array('id' => 'fr_CH',	'fallback' => 'fr', 'timezone' => '', 'language' => 'French (Swiss)'),
		'fr-fr'	=> array('id' => 'fr_FR',	'fallback' => 'fr', 'timezone' => '', 'language' => 'French (France)'),
		'fr-lu'	=> array('id' => 'fr_LU',	'fallback' => 'fr', 'timezone' => '', 'language' => 'French (Luxembourg)'),
		'fy'	=> array('id' => 'fy',		'iso2' => 'fy',	'iso3' => 'fry', 'timezone' => '', 'language' => 'Frisian'),
		'ga'	=> array('id' => 'ga',		'iso2' => 'ga',	'iso3' => 'gle', 'timezone' => '', 'language' => 'Irish'),
		'gd'	=> array('id' => 'gd',		'iso2' => 'gd',	'iso3' => 'gla', 'timezone' => '', 'language' => 'Gaelic (Scots)'),
		'gd-ie'	=> array('id' => 'gd_IE',	'fallback' => 'gd', 'timezone' => '', 'language' => 'Gaelic (Irish)'),
		'gl'	=> array('id' => 'gl',		'iso2' => 'gl',	'iso3' => 'glg', 'timezone' => '', 'language' => 'Galician'),
		'gn'	=> array('id' => 'gn',		'iso2' => 'gn',	'iso3' => 'grn', 'timezone' => '', 'language' => 'Guarani'),
		'gu'	=> array('id' => 'gu',		'iso2' => 'gu',	'iso3' => 'guj', 'timezone' => '', 'language' => 'Gujarati'),
		'gv'	=> array('id' => 'gv',		'iso2' => 'gv',	'iso3' => 'glv', 'timezone' => '', 'language' => 'Manx'),
		'ha'	=> array('id' => 'ha',		'iso2' => 'ha',	'iso3' => 'hau', 'timezone' => '', 'language' => 'Hausa'),
		'he'	=> array('id' => 'he',		'iso2' => 'he',	'iso3' => 'heb', 'timezone' => '', 'language' => 'Hebrew'),
		'hi'	=> array('id' => 'hi',		'iso2' => 'hi',	'iso3' => 'hin', 'timezone' => '', 'language' => 'Hindi'),
		'ho'	=> array('id' => 'ho',		'iso2' => 'ho',	'iso3' => 'hmo', 'timezone' => '', 'language' => 'Hiri Motu'),
		'hr'	=> array('id' => 'hr',		'iso2' => 'hr',	'iso3' => 'hrv', 'timezone' => '', 'language' => 'Croatian'),
		'ht'	=> array('id' => 'ht',		'iso2' => 'ht',	'iso3' => 'hat', 'timezone' => '', 'language' => 'Haitian'),
		'hu'	=> array('id' => 'hu',		'iso2' => 'hu',	'iso3' => 'hun', 'timezone' => '', 'language' => 'Hungarian'),
		'hy'	=> array('id' => 'hy',		'iso2' => 'hy',	'iso3' => array('arm', 'hye'), 'timezone' => '', 'language' => 'Armenian'),
		'hz'	=> array('id' => 'hz',		'iso2' => 'hz',	'iso3' => 'her', 'timezone' => '', 'language' => 'Herero'),
		'id'	=> array('id' => 'id',		'iso2' => 'id',	'iso3' => 'ind', 'timezone' => '', 'language' => 'Indonesian'),
		'ie'	=> array('id' => 'ie',		'iso2' => 'ie',	'iso3' => 'ile', 'timezone' => '', 'language' => 'Interlingue'),
		'ig'	=> array('id' => 'ig',		'iso2' => 'ig',	'iso3' => 'ibo', 'timezone' => '', 'language' => 'Igbo'),
		'ii'	=> array('id' => 'ii',		'iso2' => 'ii',	'iso3' => 'iii', 'timezone' => '', 'language' => 'Sichuan Yi'),
		'ik'	=> array('id' => 'ik',		'iso2' => 'ik',	'iso3' => 'ipk', 'timezone' => '', 'language' => 'Inupiaq'),
		'io'	=> array('id' => 'io',		'iso2' => 'io',	'iso3' => 'ido', 'timezone' => '', 'language' => 'Ido'),
		'iu'	=> array('id' => 'iu',		'iso2' => 'iu',	'iso3' => 'iku', 'timezone' => '', 'language' => 'Inuktitut'),
		'is'	=> array('id' => 'is',		'iso2' => 'is',	'iso3' => array('isl', 'ice'), 'timezone' => '', 'language' => 'Icelandic'),
		'it'	=> array('id' => 'it',		'iso2' => 'it',	'iso3' => 'ita', 'timezone' => '', 'language' => 'Italian'),
		'it-ch'	=> array('id' => 'it_CH',	'fallback' => 'it', 'timezone' => '', 'language' => 'Italian (Swiss)'),
		'ja'	=> array('id' => 'ja',		'iso2' => 'ja',	'iso3' => 'jpn', 'timezone' => '', 'language' => 'Japanese'),
		'ka'	=> array('id' => 'ka',		'iso2' => 'ka',	'iso3' => array('kat', 'geo'), 'timezone' => '', 'language' => 'Georgian'),
		'kg'	=> array('id' => 'kg',		'iso2' => 'kg',	'iso3' => 'kon', 'timezone' => '', 'language' => 'Kongo'),
		'ki'	=> array('id' => 'ki',		'iso2' => 'ki',	'iso3' => 'kik', 'timezone' => '', 'language' => 'Kikuyu'),
		'kj'	=> array('id' => 'kj',		'iso2' => 'kj',	'iso3' => 'kua', 'timezone' => '', 'language' => 'Kuanyama'),
		'kk'	=> array('id' => 'kk',		'iso2' => 'kk',	'iso3' => 'kaz', 'timezone' => '', 'language' => 'Kazakh'),
		'kl'	=> array('id' => 'kl',		'iso2' => 'kl',	'iso3' => 'kal', 'timezone' => '', 'language' => 'Kalaallisut'),
		'km'	=> array('id' => 'km',		'iso2' => 'km',	'iso3' => 'khm', 'timezone' => '', 'language' => 'Central Khmer'),
		'kn'	=> array('id' => 'kn',		'iso2' => 'kn',	'iso3' => 'kan', 'timezone' => '', 'language' => 'Kannada'),
		'ko'	=> array('id' => 'ko',		'iso2' => 'ko',	'iso3' => 'kor', 'timezone' => '', 'language' => 'Korean'),
		'ko-kp'	=> array('id' => 'ko_KP',	'fallback' => 'ko', 'timezone' => '', 'language' => 'Korea (North)'),
		'ko-kr'	=> array('id' => 'ko_KR',	'fallback' => 'ko', 'timezone' => '', 'language' => 'Korea (South)'),
		'kr'	=> array('id' => 'kr',		'iso2' => 'kr',	'iso3' => 'kau', 'timezone' => '', 'language' => 'Kanuri'),
		'ks'	=> array('id' => 'ks',		'iso2' => 'ks',	'iso3' => 'kas', 'timezone' => '', 'language' => 'Kashmiri'),
		'ku'	=> array('id' => 'ku',		'iso2' => 'ku',	'iso3' => 'kur', 'timezone' => '', 'language' => 'Kurdish'),
		'kv'	=> array('id' => 'kv',		'iso2' => 'kv',	'iso3' => 'kom', 'timezone' => '', 'language' => 'Komi'),
		'kw'	=> array('id' => 'kw',		'iso2' => 'kw',	'iso3' => 'cor', 'timezone' => '', 'language' => 'Cornish'),
		'ky'	=> array('id' => 'ky',		'iso2' => 'ky',	'iso3' => 'kir', 'timezone' => '', 'language' => 'Kirghiz'),
		'la'	=> array('id' => 'la',		'iso2' => 'la',	'iso3' => 'lat', 'timezone' => '', 'language' => 'Latin'),
		'lb'	=> array('id' => 'lb',		'iso2' => 'lb',	'iso3' => 'ltz', 'timezone' => '', 'language' => 'Luxembourgish'),
		'lg'	=> array('id' => 'lg',		'iso2' => 'lg',	'iso3' => 'lug', 'timezone' => '', 'language' => 'Ganda'),
		'li'	=> array('id' => 'li',		'iso2' => 'li',	'iso3' => 'lim', 'timezone' => '', 'language' => 'Limburgan'),
		'ln'	=> array('id' => 'ln',		'iso2' => 'ln',	'iso3' => 'lin', 'timezone' => '', 'language' => 'Lingala'),
		'lo'	=> array('id' => 'lo',		'iso2' => 'lo',	'iso3' => 'lao', 'timezone' => '', 'language' => 'Lao'),
		'lt'	=> array('id' => 'lt',		'iso2' => 'lt',	'iso3' => 'lit', 'timezone' => '', 'language' => 'Lithuanian'),
		'lu'	=> array('id' => 'lu',		'iso2' => 'lu',	'iso3' => 'lub', 'timezone' => '', 'language' => 'Luba-Katanga'),
		'lv'	=> array('id' => 'lv',		'iso2' => 'lv',	'iso3' => 'lav', 'timezone' => '', 'language' => 'Latvian'),
		'mh'	=> array('id' => 'mh',		'iso2' => 'mh',	'iso3' => 'mah', 'timezone' => '', 'language' => 'Marshallese'),
		'mg'	=> array('id' => 'mg',		'iso2' => 'mg',	'iso3' => 'mlg', 'timezone' => '', 'language' => 'Malagasy'),
		'mi'	=> array('id' => 'mi',		'iso2' => 'mi',	'iso3' => array('mri', 'mao'), 'timezone' => '', 'language' => 'Maori'),
		'mk'	=> array('id' => 'mk',		'iso2' => 'mk',	'iso3' => array('mkd', 'mac'), 'timezone' => '', 'language' => 'Macedonian'),
		'ml'	=> array('id' => 'ml',		'iso2' => 'ml',	'iso3' => 'mal', 'timezone' => '', 'language' => 'Malayalam'),
		'mn'	=> array('id' => 'mn',		'iso2' => 'mn',	'iso3' => 'mon', 'timezone' => '', 'language' => 'Mongolian'),
		'mr'	=> array('id' => 'mr',		'iso2' => 'mr',	'iso3' => 'mar', 'timezone' => '', 'language' => 'Marathi'),
		'ms'	=> array('id' => 'ms',		'iso2' => 'ms',	'iso3' => array('msa', 'may'), 'timezone' => '', 'language' => 'Malaysian'),
		'ms-bn'	=> array('id' => 'ms_BN',	'fallback' => 'ms', 'timezone' => '', 'language' => 'Malaysian (Brunei)'),
		'mt'	=> array('id' => 'mt',		'iso2' => 'mt',	'iso3' => 'mlt', 'timezone' => '', 'language' => 'Maltese'),
		'my'	=> array('id' => 'my',		'iso2' => 'my',	'iso3' => array('mya', 'bur'), 'timezone' => '', 'language' => 'Burmese'),
		'na'	=> array('id' => 'na',		'iso2' => 'na',	'iso3' => 'nau', 'timezone' => '', 'language' => 'Nauru'),
		'nb'	=> array('id' => 'nb',		'iso2' => 'nb',	'iso3' => 'nob', 'timezone' => '', 'language' => 'Norwegian Bokmal'),
		'nd'	=> array('id' => 'nd',		'iso2' => 'nd',	'iso3' => 'nde', 'timezone' => '', 'language' => 'North Ndebele'),
		'ne'	=> array('id' => 'ne',		'iso2' => 'ne',	'iso3' => 'nep', 'timezone' => '', 'language' => 'Nepali'),
		'ng'	=> array('id' => 'ng',		'iso2' => 'ng',	'iso3' => 'ndo', 'timezone' => '', 'language' => 'Ndonga'),
		'nl'	=> array('id' => 'nl',		'iso2' => 'nl',	'iso3' => array('nld', 'dut'), 'timezone' => '', 'language' => 'Dutch (Standard)'),
		'nl-be'	=> array('id' => 'nl_BE',	'fallback' => 'nl', 'timezone' => '', 'language' => 'Dutch (Belgium)'),
		'nn'	=> array('id' => 'nn',		'iso2' => 'nn',	'iso3' => 'nno', 'timezone' => '', 'language' => 'Norwegian Nynorsk'),
		'no'	=> array('id' => 'no',		'iso2' => 'no',	'iso3' => 'nor', 'timezone' => '', 'language' => 'Norwegian'),
		'nr'	=> array('id' => 'nr',		'iso2' => 'nr',	'iso3' => 'nbl', 'timezone' => '', 'language' => 'South Ndebele'),
		'nv'	=> array('id' => 'nv',		'iso2' => 'nv',	'iso3' => 'nav', 'timezone' => '', 'language' => 'Navajo'),
		'ny'	=> array('id' => 'ny',		'iso2' => 'ny',	'iso3' => 'nya', 'timezone' => '', 'language' => 'Nyanja'),
		'oj'	=> array('id' => 'oj',		'iso2' => 'oj',	'iso3' => 'oji', 'timezone' => '', 'language' => 'Ojibwa'),
		'om'	=> array('id' => 'om',		'iso2' => 'om',	'iso3' => 'orm', 'timezone' => '', 'language' => 'Oromo'),
		'or'	=> array('id' => 'or',		'iso2' => 'or',	'iso3' => 'ori', 'timezone' => '', 'language' => 'Oriya'),
		'os'	=> array('id' => 'os',		'iso2' => 'os',	'iso3' => 'oss', 'timezone' => '', 'language' => 'Ossetian'),
		'pa'	=> array('id' => 'pa',		'iso2' => 'pa',	'iso3' => 'pan', 'timezone' => '', 'language' => 'Panjabi'),
		'pi'	=> array('id' => 'pi',		'iso2' => 'pi',	'iso3' => 'pli', 'timezone' => '', 'language' => 'Pali'),
		'pl'	=> array('id' => 'pl',		'iso2' => 'pl',	'iso3' => 'pol', 'timezone' => '', 'language' => 'Polish'),
		'ps'	=> array('id' => 'ps',		'iso2' => 'ps',	'iso3' => 'pus', 'timezone' => '', 'language' => 'Pushto'),
		'pt'	=> array('id' => 'pt',		'iso2' => 'pt',	'iso3' => 'por', 'timezone' => '', 'language' => 'Portuguese (Portugal)'),
		'pt-br'	=> array('id' => 'pt_BR',	'fallback' => 'pt', 'timezone' => '', 'language' => 'Portuguese (Brazil)'),
		'qu'	=> array('id' => 'qu',		'iso2' => 'qu',	'iso3' => 'que', 'timezone' => '', 'language' => 'Quechua'),
		'rm'	=> array('id' => 'rm',		'iso2' => 'rm',	'iso3' => 'roh', 'timezone' => '', 'language' => 'Rhaeto-Romanic'),
		'rn'	=> array('id' => 'rn',		'iso2' => 'rn',	'iso3' => 'run', 'timezone' => '', 'language' => 'Rundi'),
		'ro'	=> array('id' => 'ro',		'iso2' => 'ro',	'iso3' => array('ron', 'rum'), 'timezone' => '', 'language' => 'Romanian'),
		'ro-mo'	=> array('id' => 'ro_MO',	'fallback' => 'ro', 'timezone' => '', 'language' => 'Romanian (Moldavia)'),
		'ru'	=> array('id' => 'ru',		'iso2' => 'ru',	'iso3' => 'rus', 'timezone' => '', 'language' => 'Russian'),
		'ru-mo'	=> array('id' => 'ru_MO',	'fallback' => 'ru', 'timezone' => '', 'language' => 'Russian (Moldavia)'),
		'rw'	=> array('id' => 'rw',		'iso2' => 'rw',	'iso3' => 'kin', 'timezone' => '', 'language' => 'Kinyarwanda'),
		'sa'	=> array('id' => 'sa',		'iso2' => 'sa',	'iso3' => 'san', 'timezone' => '', 'language' => 'Sanskrit'),
		'sb'	=> array('id' => 'sb',		'iso2' => 'sb',	'iso3' => 'wen', 'timezone' => '', 'language' => 'Sorbian'),
		'sc'	=> array('id' => 'sc',		'iso2' => 'sc',	'iso3' => 'srd', 'timezone' => '', 'language' => 'Sardinian'),
		'sd'	=> array('id' => 'sd',		'iso2' => 'sd',	'iso3' => 'snd', 'timezone' => '', 'language' => 'Sindhi'),
		'se'	=> array('id' => 'se',		'iso2' => 'se',	'iso3' => 'sme', 'timezone' => '', 'language' => 'Sami'),
		'sg'	=> array('id' => 'sg',		'iso2' => 'sg',	'iso3' => 'sag', 'timezone' => '', 'language' => 'Sango'),
		'si'	=> array('id' => 'si',		'iso2' => 'si',	'iso3' => 'sin', 'timezone' => '', 'language' => 'Sinhala'),
		'sk'	=> array('id' => 'sk',		'iso2' => 'sk',	'iso3' => array('slk', 'slo'), 'timezone' => '', 'language' => 'Slovak'),
		'sl'	=> array('id' => 'sl',		'iso2' => 'sl',	'iso3' => 'slv', 'timezone' => '', 'language' => 'Slovenian'),
		'sm'	=> array('id' => 'sm',		'iso2' => 'sm',	'iso3' => 'smo', 'timezone' => '', 'language' => 'Samoan'),
		'sn'	=> array('id' => 'sn',		'iso2' => 'sn',	'iso3' => 'sna', 'timezone' => '', 'language' => 'Shona'),
		'so'	=> array('id' => 'so',		'iso2' => 'so',	'iso3' => 'som', 'timezone' => '', 'language' => 'Somali'),
		'sq'	=> array('id' => 'sq',		'iso2' => 'sq',	'iso3' => array('alb', 'sqi'), 'timezone' => '', 'language' => 'Albanian'),
		'sr'	=> array('id' => 'sr',		'iso2' => 'sr',	'iso3' => 'scc', 'timezone' => '', 'language' => 'Serbian'),
		'ss'	=> array('id' => 'ss',		'iso2' => 'ss',	'iso3' => 'ssw', 'timezone' => '', 'language' => 'Swati'),
		'st'	=> array('id' => 'st',		'iso2' => 'st',	'iso3' => 'sot', 'timezone' => '', 'language' => 'Sotho'),
		'sv'	=> array('id' => 'sv',		'iso2' => 'sv',	'iso3' => 'swe', 'timezone' => '', 'language' => 'Swedish'),
		'sv-fi'	=> array('id' => 'sv_FI',	'fallback' => 'sv', 'timezone' => '', 'language' => 'Swedish (Finland)'),
		'sw'	=> array('id' => 'sw',		'iso2' => 'sw',	'iso3' => 'swa', 'timezone' => '', 'language' => 'Swahili'),
		'sz'	=> array('id' => 'sz',		'iso2' => 'sz',	'iso3' => 'smi', 'timezone' => '', 'language' => 'Sami (Lappish)'),
		'ta'	=> array('id' => 'ta',		'iso2' => 'ta',	'iso3' => 'tam', 'timezone' => '', 'language' => 'Tamil'),
		'te'	=> array('id' => 'te',		'iso2' => 'te',	'iso3' => 'tel', 'timezone' => '', 'language' => 'Telugu'),
		'tg'	=> array('id' => 'tg',		'iso2' => 'tg',	'iso3' => 'tgk', 'timezone' => '', 'language' => 'Tajik'),
		'th'	=> array('id' => 'th',		'iso2' => 'th',	'iso3' => 'tha', 'timezone' => '', 'language' => 'Thai'),
		'ti'	=> array('id' => 'ti',		'iso2' => 'ti',	'iso3' => 'tir', 'timezone' => '', 'language' => 'Tigrinya'),
		'tk'	=> array('id' => 'tk',		'iso2' => 'tk',	'iso3' => 'tuk', 'timezone' => '', 'language' => 'Turkmen'),
		'tl'	=> array('id' => 'tl',		'iso2' => 'tl',	'iso3' => 'tgl', 'timezone' => '', 'language' => 'Tagalog'),
		'tn'	=> array('id' => 'tn',		'iso2' => 'tn',	'iso3' => 'tsn', 'timezone' => '', 'language' => 'Tswana'),
		'to'	=> array('id' => 'to',		'iso2' => 'to',	'iso3' => 'ton', 'timezone' => '', 'language' => 'Tonga'),
		'tr'	=> array('id' => 'tr',		'iso2' => 'tr',	'iso3' => 'tur', 'timezone' => '', 'language' => 'Turkish'),
		'ts'	=> array('id' => 'ts',		'iso2' => 'ts',	'iso3' => 'tso', 'timezone' => '', 'language' => 'Tsonga'),
		'tt'	=> array('id' => 'tt',		'iso2' => 'tt',	'iso3' => 'tat', 'timezone' => '', 'language' => 'Tatar'),
		'tw'	=> array('id' => 'tw',		'iso2' => 'tw',	'iso3' => 'twi', 'timezone' => '', 'language' => 'Twi'),
		'ty'	=> array('id' => 'ty',		'iso2' => 'ty',	'iso3' => 'tah', 'timezone' => '', 'language' => 'Tahitian'),
		'ug'	=> array('id' => 'ug',		'iso2' => 'ug',	'iso3' => 'uig', 'timezone' => '', 'language' => 'Uighur'),
		'uk'	=> array('id' => 'uk',		'iso2' => 'uk',	'iso3' => 'ukr', 'timezone' => '', 'language' => 'Ukrainian'),
		'ur'	=> array('id' => 'ur',		'iso2' => 'ur',	'iso3' => 'urd', 'timezone' => '', 'language' => 'Urdu'),
		'uz'	=> array('id' => 'uz',		'iso2' => 'uz',	'iso3' => 'uzb', 'timezone' => '', 'language' => 'Uzbek'),
		've'	=> array('id' => 've',		'iso2' => 've',	'iso3' => 'ven', 'timezone' => '', 'language' => 'Venda'),
		'vi'	=> array('id' => 'vi',		'iso2' => 'vi',	'iso3' => 'vie', 'timezone' => '', 'language' => 'Vietnamese'),
		'vo'	=> array('id' => 'vo',		'iso2' => 'vo',	'iso3' => 'vol', 'timezone' => '', 'language' => 'Volapük'),
		'wa'	=> array('id' => 'wa',		'iso2' => 'wa',	'iso3' => 'wln', 'timezone' => '', 'language' => 'Walloon'),
		'wo'	=> array('id' => 'wo',		'iso2' => 'wo',	'iso3' => 'wol', 'timezone' => '', 'language' => 'Wolof'),
		'xh'	=> array('id' => 'xh',		'iso2' => 'xh',	'iso3' => 'xho', 'timezone' => '', 'language' => 'Xhosa'),
		'yi'	=> array('id' => 'yi',		'iso2' => 'yi',	'iso3' => 'yid', 'timezone' => '', 'language' => 'Yiddish'),
		'yo'	=> array('id' => 'yo',		'iso2' => 'yo',	'iso3' => 'yor', 'timezone' => '', 'language' => 'Yoruba'),
		'za'	=> array('id' => 'za',		'iso2' => 'za',	'iso3' => 'zha', 'timezone' => '', 'language' => 'Zhuang'),
		'zh'	=> array('id' => 'zh',		'iso2' => 'zh',	'iso3' => array('chi', 'zho'), 'timezone' => '', 'language' => 'Chinese'),
		'zh-cn'	=> array('id' => 'zh_CN',	'fallback' => 'zh', 'timezone' => '', 'language' => 'Chinese (PRC)'),
		'zh-hk'	=> array('id' => 'zh_HK',	'fallback' => 'zh', 'timezone' => '', 'language' => 'Chinese (Hong Kong)'),
		'zh-mo'	=> array('id' => 'zh_MO',	'fallback' => 'zh', 'timezone' => '', 'language' => 'Chinese (Macau)'),
		'zh-sg'	=> array('id' => 'zh_SG',	'fallback' => 'zh', 'timezone' => '', 'language' => 'Chinese (Singapore)'),
		'zh-tw'	=> array('id' => 'zh_TW',	'fallback' => 'zh', 'timezone' => '', 'language' => 'Chinese (Taiwan)'),
		'zu'	=> array('id' => 'zu',		'iso2' => 'zu',	'iso3' => 'zul', 'timezone' => '', 'language' => 'Zulu'),
	);
	
	/**
	 * Translator object to use for string fetching and parsing.
	 * 
	 * @access protected
	 * @var Translator
	 */
	protected $_translator;
	
	/**
	 * Apple the locale using PHPs built in setlocale().
	 * 
	 * @link http://php.net/setlocale
	 * @link http://php.net/manual/locale.setdefault.php
	 * 
	 * @access public
	 * @param string $key 
	 * @return G11n
	 * @throws CoreException
	 * @chainable
	 */
	public function apply($key) {
		if (empty($this->_locales[$key])) {
			throw new CoreException(sprintf('Locale %s does not exist.', $key));
		}
		
		$locale = $this->find($key);
		$this->_current = $key;
		
		// Build array of options to set
		$options = array(
			$locale['id'] . '.UTF8',
			$locale['id'] . '.UTF-8',
			$locale['id']
		);
		
		if (!empty($locale['iso3'])) {
			$options = array_merge($options, array(
				$locale['iso3'] . '.UTF8',
				$locale['iso3'] . '.UTF-8',
				$locale['iso3']
			));
		}
		
		if (!empty($locale['iso2'])) {
			$options = array_merge($options, array(
				$locale['iso2'] . '.UTF8',
				$locale['iso2'] . '.UTF-8',
				$locale['iso2']
			));
		}
		
		$options = array_merge($options, array(
			'eng.UTF8',
			'eng.UTF-8',
			'eng',
			'en_US'
		));

		putenv('LC_ALL=' . $locale['id']);
		setlocale(LC_ALL, $options);
		
		if (!empty($locale['timezone'])) {
			$this->applyTimezone($locale['timezone']);
		}
		
		return $this;
	}
	
	/**
	 * Apply the timezone.
	 * 
	 * @access public
	 * @param string $timezone
	 * @return G11n 
	 * @chainable
	 */
	public function applyTimezone($timezone) {
		date_default_timezone_set($timezone);
		
		return $this;
	}
	
	/**
	 * Convert a locale key to 3 possible formats.
	 * 
	 *	FORMAT_1 - en-us
	 *	FORMAT_2 - en-US
	 *	FORMAT_3 - en_US
	 * 
	 * @access public
	 * @param string $key
	 * @param int $format
	 * @return string
	 */
	public function canonicalize($key, $format = self::FORMAT_1) {
		$parts = explode('-', str_replace('_', '-', strtolower($key)));
		$return = $parts[0];
		
		if (isset($parts[1])) {
			switch ($format) {
				case self::FORMAT_1:
					$return .= '-' . $parts[1];
				break;
				case self::FORMAT_2:
					$return .= '-' . strtoupper($parts[1]);
				break;
				case self::FORMAT_3:
					$return .= '_' . strtoupper($parts[1]);
				break;
			}
		}
		
		return $return;
	}
	
	/**
	 * Return the current locale config, or a certain value.
	 * 
	 * @access public
	 * @param string $key
	 * @return string|array
	 */
	public function current($key = null) {
		$locale = $this->_locales[$this->_current];
		
		if (isset($locale[$key])) {
			return $locale[$key];
		}
		
		return $locale;
	}
	
	/**
	 * Define the fallback language if none can be found or is not supported.
	 * 
	 * @access public
	 * @param string $key
	 * @return G11n 
	 * @throws CoreException
	 * @chainable
	 */
	public function fallbackAs($key) {
		if (empty($this->_locales[$key])) {
			throw new CoreException(sprintf('Locale %s has not been setup.', $key));
		}

		$this->_fallback = $key;
		
		$locale = $this->find($key);
		
		ini_set('intl.default_locale', $locale['id']);
		
		return $this;
	}
	
	/**
	 * Return the fallback locale.
	 * 
	 * @access public
	 * @return array
	 * @throws CoreException
	 */
	public function getFallback() {
		if (!$this->_fallback || empty($this->_locales[$this->_fallback])) {
			throw new CoreException('Fallback locale has not been setup.');
		}
		
		return $this->_locales[$this->_fallback];
	}
	
	/**
	 * Returns the setup locales.
	 * 
	 * @access public
	 * @return array
	 */
	public function getLocales() {
		return $this->_locales;
	}
	
	/**
	 * Returns the supported locales.
	 * 
	 * @access public
	 * @return array
	 */
	public function getSupportedLocales() {
		return $this->_supportedLocales;
	}
	
	/**
	 * Detect which locale to use based on the clients Accept-Language header.
	 * 
	 * @access public
	 * @throws CoreException
	 * @return void
	 */
	public function initialize() {
		if (!$this->isEnabled()) {
			return;
		}
		
		$header = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);

		if (strpos($header, ';') !== false) {
			$header = strstr($header, ';', true);
		}
		
		$header = explode(',', $header);
		$current = null;
		
		if (count($header) > 0) {
			foreach ($header as $locale) {
				if (isset($this->_locales[$locale])) {
					$current = $locale;
					break;
				}
			}
		}
		
		// Set current to the fallback if none found
		if ($current == null) {
			$current = $this->_fallback;
		}
		
		// Apply the locale
		$this->apply($current);
		
		// Check for a translator
		if (empty($this->_translator)) {
			throw new CoreException('A translator is required for G11n string parsing.');
		}
	}
	
	/**
	 * Does the current locale matched the passed key?
	 * 
	 * @access public
	 * @param string $key
	 * @return boolean
	 */
	public function is($key) {
		return ($this->current('key') == $key || $this->current('id') == $key);
	}
	
	/**
	 * G11n will be enabled if a locale has been setup.
	 * 
	 * @access public
	 * @return boolean 
	 */
	public function isEnabled() {
		return !empty($this->_locales);
	}
	
	/**
	 * Find the locale within the list of supported locales based on the given key. 
	 * If a locale has a parent, merge the parent into the child to gain its values.
	 * 
	 * @access public
	 * @param string $key
	 * @return array
	 * @throws CoreException
	 */
	public function find($key) {
		$key = $this->canonicalize($key);
		
		if (!isset($this->_supportedLocales[$key])) {
			throw new CoreException(sprintf('%s is not a supported locale.', $key));
		}
		
		$locale = $this->_supportedLocales[$key];
		
		if (isset($locale['fallback'])) {
			$locale = $locale + $this->find($locale['fallback']);
		}
			
		$locale['language'] = substr($locale['id'], 0, 2);
		$locale['region'] = substr($locale['id'], -2);
		$locale['key'] = $key;
		
		return $locale;
	}
	
	/**
	 * Find a locale using the ISO 2 character code standard.
	 * 
	 * @access public
	 * @param string $key
	 * @return array
	 * @throws CoreException
	 */
	public function findByIso2($key) {
		$return = array();
		
		foreach ($this->_supportedLocales as $locale) {
			if (isset($locale['iso2']) && $locale['iso2'] == $key) {
				$return = $locale;
			}
		}
		
		if (empty($return)) {
			throw new CoreException(sprintf('No locale was found using the ISO2 key %s.', $key));
		}
		
		return $return;
	}
	
	/**
	 * Find a locale using the ISO 3 character code standard.
	 * 
	 * @access public
	 * @param string $key
	 * @return array
	 * @throws CoreException
	 */
	public function findByIso3($key) {
		$return = array();
		
		foreach ($this->_supportedLocales as $locale) {
			if (isset($locale['iso3'])) {
				if (is_array($locale['iso3']) && in_array($key, $locale['iso3']) || $locale['iso3'] == $key) {
					$return = $locale;
				}
			}
		}
		
		if (empty($return)) {
			throw new CoreException(sprintf('No locale was found using the ISO3 key %s.', $key));
		}
		
		return $return;
	}
	
	/**
	 * Accepts a list of locale keys to setup the application with. 
	 * The list may accept the locale key in the array index or value position. 
	 * If the locale key is placed in the index, the value may consist of an array to overwrite with.
	 * 
	 * @access public
	 * @param array $keys
	 * @return G11n 
	 * @chainable
	 */
	public function setup(array $keys) {
		foreach ($keys as $key => $locale) {
			if (is_string($locale)) {
				$key = $locale;
				$locale = array();
			}
			
			$locale = $locale + $this->find($key);
			$this->_locales[$key] = $locale;
		}
		
		return $this;
	}
	
	/**
	 * Sets the translator to use in the string locating and translating process.
	 * 
	 * @access public
	 * @param Translator $translator
	 * @return G11n 
	 * @chainable
	 */
	public function setTranslator(Translator $translator) {
		$this->_translator = $translator;
		
		return $this;
	}
	
	/**
	 * Set the storage engine to use for catalog caching.
	 * 
	 * @access public
	 * @param Storage $storage
	 * @return G11n 
	 * @chainable
	 */
	public function setStorage(Storage $storage) {
		$this->_storage = $storage;
		
		return $this;
	}
	
	/**
	 * Return a translated string using the translator. 
	 * Will use the built in MessageFormatter to parse strings with dynamic data.
	 * 
	 * @access public
	 * @param string $key
	 * @param array $params
	 * @return string
	 */
	public function translate($key, array $params = array()) {	
		return $this->_translator->translate($key, $params);
	}

}

