<?php
/**
 * Titon: A PHP 5.4 Modular Framework
 *
 * @copyright	Copyright 2010, Titon
 * @link		http://github.com/titon
 * @license		http://opensource.org/licenses/bsd-license.php (BSD License)
 */

return array(
	'irregular' => array(
		'atlas' => 'atlases',
		'beef' => 'beefs',
		'brother' => 'brothers',
		'child' => 'children',
		'corpus' => 'corpuses',
		'cow' => 'cows',
		'ganglion' => 'ganglions',
		'genie' => 'genies',
		'genus' => 'genera',
		'graffito' => 'graffiti',
		'hoof' => 'hoofs',
		'loaf' => 'loaves',
		'man' => 'men',
		'money' => 'monies',
		'mongoose' => 'mongooses',
		'move' => 'moves',
		'mythos' => 'mythoi',
		'numen' => 'numina',
		'occiput' => 'occiputs',
		'octopus' => 'octopuses',
		'opus' => 'opuses',
		'ox' => 'oxen',
		'penis' => 'penises',
		'person' => 'people',
		'sex' => 'sexes',
		'soliloquy' => 'soliloquies',
		'testis' => 'testes',
		'trilby' => 'trilbys',
		'turf' => 'turfs',
		'woman' => 'women'
	),
	'uninflected' => array(
		'equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep',
		'amoyese', 'bison', 'borghese', 'bream', 'breeches', 'britches', 'buffalo', 'cantus',
		'carp', 'chassis', 'clippers', 'cod', 'coitus', 'congoese', 'contretemps', 'corps',
		'debris', 'diabetes', 'djinn', 'eland', 'elk', 'faroese', 'flounder',
		'foochowese', 'gallows', 'genevese', 'genoese', 'gilbertese', 'graffiti',
		'headquarters', 'herpes', 'hijinks', 'hottentotese', 'innings', 'yengeese',
		'jackanapes', 'kiplingese', 'kongoese', 'lucchese', 'mackerel', 'maltese', 'media',
		'mews', 'moose', 'mumps', 'nankingese', 'news', 'nexus', 'niasese',
		'pekingese', 'piedmontese', 'pincers', 'pistoiese', 'pliers', 'portuguese',
		'proceedings', 'rabies', 'rice', 'rhinoceros', 'salmon', 'sarawakese', 'scissors',
		'seabass', 'series', 'shavese', 'shears', 'siemens', 'species', 'swine', 'testes',
		'trousers', 'trout', 'tuna', 'vermontese', 'wenchowese', 'whiting', 'wildebeest',
	),
	'plural' => array(
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
	),
	'singular' => array(
		'/(quiz)zes$/i' => '\1',
		'/(matr)ices$/i' => '\1ix',
		'/(vert|ind)ices$/i' => '\1ex',
		'/^(ox)en/i' => '\1',
		'/(alias|status)es$/i' => '\1',
		'/([octop|vir])i$/i' => '\1us',
		'/(cris|ax|test)es$/i' => '\1is',
		'/(shoe)s$/i' => '\1',
		'/(o)es$/i' => '\1',
		'/(bus)es$/i' => '\1',
		'/([m|l])ice$/i' => '\1ouse',
		'/(x|ch|ss|sh)es$/i' => '\1',
		'/(m)ovies$/i' => '\1ovie',
		'/(s)eries$/i' => '\1eries',
		'/([^aeiouy]|qu)ies$/i' => '\1y',
		'/([lr])ves$/i' => '\1f',
		'/(tive)s$/i' => '\1',
		'/(hive)s$/i' => '\1',
		'/([^f])ves$/i' => '\1fe',
		'/(^analy)ses$/i' => '\1sis',
		'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
		'/([ti])a$/i' => '\1um',
		'/(n)ews$/i' => '\1ews',
		'/s$/i' => ''
	)
);