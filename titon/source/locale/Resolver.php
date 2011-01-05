<?php
/**
 * I18n - Internationalization - Providing some kind of framework so that you can easily swap out strings, graphics, sounds and other resources and generally handling different conventions of communication.

L10n - Localization - The process of creating a bunch of strings, graphics, sounds etc. so that you can target a specific nationality, language or region.

G11n - Globalization - Doing both Internationalization and Localization steps :)
 *
 * http://us2.php.net/manual/en/book.i18n.php
 *
 * http://us2.php.net/manual/en/book.gettext.php
 
 
 ICU - http://icu-project.org (IBM)
 Unicode
 CLDR - http://cldr.unicode.org
 
 extensions: locale, collator, number/time/currency/date formatter, message and choice formatter, normalize, graphemes, IDN, calendars, resources
 
 dual API OO
 
 collator_create() = new Collator();
 numfmt_format() = NumberFormatter::format();
 locale_get_default() = Locale::getDefault();
 
 - Relies on ICU locales
 	<language>[_<script>]_<country>[_<variant>][@<keywords>]

- Default locale
	new Collator(Locale::DEFAULT);
	Locale::setDefault, getDefault
	You can use null! (same as default)
	
- Locale pieces
	getPrimaryLanguage($l);
	getScript()
	getRegion()
	getVariant()
	getKeywords()
	
	getDisplayName()
	getDisplayLanguage()
	getDisplayScript()
	getDisplayRegion($l)
	
	parseLocale() - returns array composed of locale subtags
	composeLocale() - creates locale ID out of subtags
	
	acceptFromHttp - accept-language in http headers
	lookup - find in list
	filterMatches - are they the same?
	
- Collator
	comparing / sorting strings
	collation level/strength
	all ICU collator attributes
		numeric collation
		ignoring punctuation
	
$c = new Collator("fr_CA");
if ($c->compare('cote', 'cote') < 0) {

} else {

}
$c->sort($array);*/

namespace titon\locale;

class G11n {
	
	public static function initialize() {
		$settings = Config::get('Locale');
		
		if (!empty($settings['timezone'])) {
			date_default_timezone_set($settings['timezone']);
		}
		
		if (!empty($settings['current'])) {
			setlocale(LC_ALL, $settings['current']);
			//locale_set_default($settings['current']);
			
		} else if (!empty($settings['default'])) {
			setlocale(LC_ALL, $settings['default']);
			//locale_set_default($settings['default']);
		}
	}
	
}

	