# Titon #

A PHP 5.4 modular framework that attempts to be extremely lightweight and fast, all the while being highly configurable. Many design principles have been implemented into the core infrastructure. Titon is a very experimental framework hoping to make heavy use of lambda functions and a separating functionality and logic into modular libraries.

### Features ###

Modularity, Loose Coupling, Lazy-Loading, Design Patterns (Template, Observer, Decorator, Factory, Memoization), Design Principles (DRY, KISS, YAGNI), Dependency Injection and Management, Events and Listeners, Benchmarks, Inline Callbacks.

### Requirements ###

* curl
* gettext (G11n)
* intl (G11n)
* phar
* mbstring
* mcrypt

### Libraries ###

* Action - Re-usable and de-coupled controller actions, packaged as a stand alone class.
* Adapter - Handles the case of adapting a specific class to another class.
* Augment - Miniature classes used to encapsulate specific functionality for primary classes.
* Behavior - Defines a behavior pattern for Dao callbacks.
* Bundle - Manages the loading and manipulating of resources and their locations.
* Controller - Handles the HTTP request and returns the HTTP response, within the dispatch cycle.
* Dao - Database access objects handle the connection and interaction with the database layer.
* Dispatcher - Handles the dispatch cycle in the MVC paradigm.
* Engine - Handles the rendering of the view templates.
* Enum - Pre-built convenience enums.
* Exception - Pre-built exceptions.
* Helper - Provides additional functionality to the view layer.
* Listener - Objects that listen and wait to be triggered at specific events in the system.
* Model - Represents a single entities data schema.
* Package - A combination of library types into a single related entity.
* Reader - Handles the loading and parsing of file types: xml, json, yaml, etc
* Route - Maps URLs to internal destinations.
* Shell - Handles CLI execution and tasks.
* Storage - Provides different methods of caching data.
* Trait - Pre-built PHP 5.4 traits.
* Translator - Handles the translation of certain file types to be used by the G11n message system.
* Transporter - Handles the different type of email transporting (SMTP, etc).

### Execution Cycle ###

* Include app/index.php
* Set app constants
* Include titon/bootstrap.php
* Set titon constants
* Titon.initialize()
    * Construct core classes (order important)
        * Loader() - Set autoloader
        * Debugger() - Set exception and error handling
        * Environment()
        * Application()
        * Cache()
        * Config()
        * Registry()
        * Router()
        * G11n()
        * Event()
        * Dispatch()
* Include app/setup.php
* Titon.startup()
    * Loop through each core class and initialize
        * Loader()
        * Debugger()
        * Environment() - Parse headers and enable
        * Application() - Loop through each module and include bootstrap.php
        * Cache()
        * Config()
        * Registry()
        * Router() - Parse and determine the route
        * G11n() - Parse headers and enable
        * Event()
        * Dispatch()
* Event.notify('titon.startup')
* Dispatch.run()
    * Load Dispatcher
    * Event.notify('dispatch.preDispatch')
    * Load Controller
        * Controller.preProcess()
        * Event.notify('controller.preProcess')
        * Execute controller action
        * Controller.postProcess()
        * Event.notify('controller.postProcess')
    * Load Engine
        * Engine.preRender()
        * Event.notify('view.preRender')
        * Render view template
        * Engine.postRender()
        * Event.notify('view.postRender')
    * Event.notify('dispatch.postDispatch')
* Dispatch.output()
* Event.notify('titon.shutdown')
* Titon.shutdown()
    * Loop through each core class and deinitialize