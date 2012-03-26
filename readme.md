# Titon #

A PHP 5.4 modular framework that attempts to be extremely lightweight and fast, all the while being highly configurable. Many design principles have been implemented into the core infrastructure. Titon is a very experimental framework hoping to make heavy use of lambda functions and a separating functionality and logic into modular libraries.

### Features ###

Modularity, Loose Coupling, Lazy-Loading, Design Patterns (Template, Observer, Decorator, Factory, Memoization), Dependency Injection, ORM, System Hooks, Events and Event Listeners, Benchmarks, Inline Callbacks.

### Requirements ###

* curl
* gettext (G11n)
* intl (G11n)
* phar
* mbstring

### Folder Structure ###

	app/
		config/
			environments/
				dev.php
				prod.php
			sets/
			setup.php
		libs/
		modules/
			pages/
				actions/
				controllers/
				libs/
				models/
				views/
					private/
					public/
				web/
					css/
					js/
					img/
				bootstrap.php
			admin/
				***
		resources/
			messages/
		temp/
			cache/
			debug.log
			error.log
		views/
			private/
			public/
		web/
			css/
			js/
			img/
		index.php
	vendors/
		titon/
			base/
				types/
			console/
			constant/
			core/
			libs/
				actions/
				adapters/
				behaviors/
				bundles/
				controllers/
				dispatchers/
				drivers/
				engines/
				enums/
				exceptions/
				helpers/
				listeners/
				models/
				packages/
				readers/
				routes/
				shells/
				storage/
				traits/
				translators/
				transporters/
			log/
			net/
			resources/
				locales/
				messages/
			state/
			utility/
			Exception.php
			Titon.php
			bootstrap.php
			functions.php

### Libraries ###

* Action - Re-usable and de-coupled controller actions, packaged as a stand alone class.
* Adapter - Handles the case of adapting a specific class to another class.
* Behavior - Defines a behavior pattern for a model.
* Bundle - Manages the loading and manipulating of resources and their locations.
* Controller - Handles the HTTP request and returns the HTTP response, within the dispatch cycle.
* Dispatcher - Handles the dispatch cycle in the MVC paradigm.
* Driver - Allows the model to access different types of databases; describes the schema. Only works with Titon's model system.
* Engine - Handles the rendering of the view templates.
* Enum - Pre-built convenience enums.
* Exception - Pre-built exceptions.
* Helper - Provides additional functionality to the view layer.
* Listener - Objects that listen and wait to be triggered at specific events in the system.
* Model - Represents a single entities data schema.
* Package - A combination of library types into a single related entity.
* Reader - Handles the loading of configuration file types: xml, json, yaml, etc
* Route - Maps URLs to internal destinations.
* Shell - Handles CLI execution and tasks.
* Storage - Provides different methods of caching data.
* Trait - Pre-built PHP 5.4 traits.
* Translator - Handles the translation of certain filetypes to be used by the locale message system.
* Transporter - Handles the different type of email transporting (SMTP, etc).