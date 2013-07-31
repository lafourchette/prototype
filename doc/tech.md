# How prototype works

Prototype is composed with two parts, a beautiful web ui and a series of crons.

## What's in it ?

* Silex based application
* Ldap connection
* Doctrine orm provider
* Github api
* see composer.json

### Frontend

* Checker objects
    * Use to check availability
* Creator objects
    * Use to create object and add logic to en entity
* Decider objects
    * Use to decide which server is available to create a new vm
* Manager objects
    * Use to persist and flush object to the database
* Entity objects
    * Use by doctrine ORM to map sql table

### Backend

TODO