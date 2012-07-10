# Titon Conventions #

### Miscellaneous ###

* In files that contain only PHP, leave out the closing ?> tag
* Save each file as UTF-8 without BOM (byte-order mark)
* Use the Unix LF (linefeed) line ending on all saved files
* All paths that are assigned to a variable or constant must end in a trailing slash
* Variables should be written in camelBack
* Constants should be written in UPPER_CASE

```php
$examplePath = __DIR__ . '/example/path/';
define('EXAMPLE_PATH', '/var/example/path/');
```

### Folders ###

* All folders must be lowercase
* Any folder that houses multiples of a same type should be plural (libs, models, resources, etc) when applicable
* Folder names should never be multi-word, either use a singular form or an acronym
* Folder names should not consist of any character except letters and numbers

```
/titon/libs/controllers/core/DefaultController.php
/titon/resources/locales/en/
/titon/utility/
```

### Namespaces ###

* Each namespace should match up to a folder in the filesystem
* Each root namespace package (folder) should contain a package specific Exception class
* Should follow all the same rules listed above for folders
* Should follow the PSR-0: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md

### Classes (Interfaces, Traits) ###

* Only one per file and namespace package (excluding test cases)
* Each external class being used should have an accompanying use statement
* Any exception thrown should be the parent package's defined exception
* Declare visibility on all properties and methods

Names

* Should be written in CamelCaps
* Should be nouns when applicable (Animal)
* Interfaces/Traits should be adjectives (Controller, not Control)
* Abstract classes that extend an interface should prefix the word abstract with the interface (ControllerAbstract)
* Classes that extend interfaces should suffix the interface name into the classname (ExampleController)

Properties and methods

* Should be written in camelBack
* Should be verbs or the name of the property they are interacting with
* Protected visibility should begin with a single underscore
* Private visibility should begin with a double underscore
* All properties should be protected excluding rare cases
* Default method arguments should be placed last

```php
namespace titon\example;

use titon\libs\controllers\Controller;
use titon\example\ExampleException;

class ExampleController implements Controller {

	protected $_protectedProp = [];

	public function publicMethod($var) {
		throw new ExampleException();
	}

	private function __privateMethod($var, $default = 1) {
		return;
	}

}
```

### Method Naming ###

Please use the following words (or variations of) when implementing methods in your classes.
Getters and setters should use singular words when dealing with a single record, and plurals when dealing with collections of data.

* get() - Fetching data based on a key
* set() - Setting data based on a key (will overwrite)
* add() - Adding data to an array without the need for a key (will append)
* remove() - Remove data based on a key
* create() - Create or generate some data or values
* flush() - Remove all data
* listing() - Return all data
* has() - Checks if a key/index exists within an array
* is() - Check to see if something matches something
* setup() - Sets up the object with required data
* parse() - Extracts content out of data
* load() - Loads data from an external source (file), can also be used to load and parse contents
* run() - Executes the classes functional purpose
* output() - Output data based on a key

Reserved names.

* initialize() - Triggered immediately during class __construct() if extending titon\base\Base
* current() - Used to return the current instance from a collection
* startup()
* shutdown()