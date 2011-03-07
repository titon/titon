# Titon #

A PHP 5.3 micro framework that attempts to be extremely lightweight and fast, all the while being highly configurable. Many design principles have been implemented into the core infrastructure. Titon is a very experimental framework hoping to make heavy use of lambda functions and prototypal architecture, ala Javascript.

### Features ###

Modularity, Loose Coupling, Lazy-Loading, Design Patterns (Template, Observer, Decorator, Factory), Dependency Injection, ORM, System Hooks, Events and Event Listeners, Benchmarks, Inline Callbacks.

### Folder Structure ###

	app/
		config/
			environments/
				Development.php
				Production.php
			sets/
			Routes.php
			Setup.php
		library/
		modules/
			pages/
				actions/
				controllers/
				library/
				models/
				views/
					private/
					public/
				web/
					css/
					js/
					img/
				Bootstrap.php
			admin/
				***
		temp/
			cache/
			session/
			debug.log
			error.log
		views/
			private/
			public/
		web/
			css/
			js/
			img/
		AppController.php
		AppModel.php
		AppView.php
	titon/
		console/
		library/
			adapters/
			behaviors/
			dispatchers/
			drivers/
			engines/
			helpers/
			listeners/
			packages/
			readers/
			routes/
			shells/
		source/
		vendors/

### Library ###

* Adapter - Handles the case of adapting a specific class to another class.
* Behavior - Defines a behavior pattern for a model.
* Dispatcher - Handles the dispatch cycle in the MVC paradigm.
* Driver - Allows the model to access different types of databases; describes the schema. Only works with Titon's model system.
* Engine - Handles the rendering of the view templates.
* Helper - Provides additional functionality to the view layer.
* Listener - Objects that listen and wait to be triggered at specific events in the system.
* Package - A combination of library types into a single related entity.
* Readers - Handles the loading of configuration file types: xml, json, yaml, etc
* Routes - Maps URLs to internal destinations.
* Shell - Handles CLI execution and tasks.