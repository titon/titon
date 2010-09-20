# Titon #

A PHP 5.3 micro framework that attempts to be extremely lightweight and fast, all the while being highly configurable. Many design principles have been implemented into the core infrastructure, some of which include:

### Features ###

Modularity, Loose Coupling, Lazy-Loading, Design Patterns (Template, Observer, Decorator, Factory), Dependency Injection, ORM, System Hooks... Just to name a few.

### Folder Structure ###

	app/
		config/
			environments/
				Development.php
				Production.php
			routes/
			sets/
			Environments.php
			Routes.php
			Setup.php
		modules/
			core/
				actions/
				components/
				config/
				controllers/
				models/
				views/
					private/
					public/
				web/
					css/
					js/
					img/
				Bootstrap.php
				Index.php
			admin/
				***
		temp/
			cache/
			session/
			Debug.log
			Error.log
		AppController.php
		AppModel.php
		AppView.php
	titon/
		source/
		components/
			adapters/
			behaviors/
			dispatchers/
			drivers/
			engines/
			listeners/
			helpers/
			shells/
			packages/
	vendors/

### Components ###

* Adapter - Handles the case of adapting a specific class to another class.
* Behavior - Defines a behavior pattern for a model.
* Dispatcher - Handles the dispatch cycle in the MVC paradigm.
* Driver - Allows the model to access different types of databases; describes the schema.
* Engine - Handles the rendering of the view templates.
* Listener - Objects to be triggered at specific events to hook into the system.
* Helper - Provides additional functionality to the view layer.
* Shell - Handles CLI execution and tasks.
* Package - A combination of component types into a single related entity.