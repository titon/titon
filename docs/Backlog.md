# Titon Backlog #

Below is a top-level rundown of all components, either in development or in the pipeline.
Each system has a list of corresponding libraries that will be worked on before completion of the system.
Any library wrapped in parenthesis currently does not have a final location in the framework.

### Systems In Development ###

View Rendering

* core/Dispatch
* libs/dispatchers
* libs/engines - 100%
* libs/helpers

### Upcoming Systems ###

Dispatch Cycle

* core/Dispatch - 50%
* core/Router - 100%
* libs/controllers - 100%
* libs/routes - 100%

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