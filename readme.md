# Titon #

A PHP 5.3 micro framework that attempts to be extremely lightweight and fast, all the while being highly configurable. Many design principles have been implemented into the core infrastructure. Titon is a very experimental framework hoping to make heavy use of lambda functions and prototypal architecture, ala Javascript.

### Features ###

Modularity, Loose Coupling, Lazy-Loading, Design Patterns (Template, Observer, Decorator, Factory), Dependency Injection, ORM, System Hooks, Events and Event Listeners, Benchmarks, Inline Callbacks.

### Folder Structure ###

	app/
		config/
			environments/
				development.php
				production.php
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
	tests/
	titon/
		base/
		console/
		constant/
		core/
		data/
		libs/
			actions/
			adapters/
			behaviors/
			controllers/
			dispatchers/
			drivers/
			engines/
			enums/
			helpers/
			listeners/
			packages/
			readers/
			routes/
			shells/
			storage/
			traits/
			translators/
			transporters/
		locale/
		log/
		net/
		state/
		utility/
		vendors/
		Exception.php
		Titon.php
		bootstrap.php
		functions.php

### Libraries ###

* Action - Re-usable and de-coupled controller actions, packaged as a stand alone class.
* Adapter - Handles the case of adapting a specific class to another class.
* Behavior - Defines a behavior pattern for a model.
* Controller - Handles the HTTP request and returns the HTTP response, within the dispatch cycle.
* Dispatcher - Handles the dispatch cycle in the MVC paradigm.
* Driver - Allows the model to access different types of databases; describes the schema. Only works with Titon's model system.
* Engine - Handles the rendering of the view templates.
* Enums - Pre-built convenience enums.
* Helper - Provides additional functionality to the view layer.
* Listener - Objects that listen and wait to be triggered at specific events in the system.
* Package - A combination of library types into a single related entity.
* Reader - Handles the loading of configuration file types: xml, json, yaml, etc
* Route - Maps URLs to internal destinations.
* Shell - Handles CLI execution and tasks.
* Storage - Provides different methods of caching data.
* Trait - Pre-built PHP 5.4 traits.
* Translator - Handles the translation of certain filetypes to be used by the locale message system.
* Transporter - Handles the different type of email transporting (SMTP, etc).