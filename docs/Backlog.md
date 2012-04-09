# Titon Backlog #

Below is a top-level rundown of all components, either in development or in the pipeline.
Each system has a list of corresponding libraries that will be worked on before completion of the system.
Any library wrapped in parenthesis currently does not have a final location in the framework.

### Systems In Development ###

Globalization

* core/G11n - 90%
* libs/bundles - 90%
* libs/translators - 75%
* resources/locales - 65%
* resources/messages - 75%

### Upcoming Systems ###

Dispatch Cycle

* core/Dispatch
* core/Router
* libs/controllers
* libs/routes

View Rendering

* core/Dispatch
* libs/dispatchers
* libs/engines
* libs/helpers

Database/Datasource Layer

* core/Db
* libs/behaviors
* libs/daos
* libs/models
* (pagination)

Security

* (authorization)
* (authentication)
* (acl)
* (xss/csrf protection)
* (firewall)
* (persistence)

Utilities

* base/types
* utility
* (uri/url)
* (validation)

Input/Ouput / Filesystem

* io

Email

* net/Email
* libs/transporters

### Backlogged Systems ###

* Command Line Support
* REST/SOAP Support
* Development Profiler/Debugger