```
    ____             __        __
   / __ \_________  / /_____  / /___  ______  ___
  / /_/ / ___/ __ \/ __/ __ \/ __/ / / / __ \/ _ \
 / ____/ /  / /_/ / /_/ /_/ / /_/ /_/ / /_/ /  __/
/_/   /_/   \____/\__/\____/\__/\__, / .___/\___/
                               /____/_/
```
Introduction
------------

Prototype was created to help product owner to test their production in a full stack environnement.

What you will like about this project

* Start/Stop/Build prototype at ease
* Easy deployment
* Data extract from production

### Functionalities

* Automatic mail alert on events (start, stopped, expired)
* Choose people to alert via ldap
* Allowed to choose any branch for each project
* Choose your database extract (next version)
* Choose the expiration date (next version)


Install
-------

```bash
make build

# check makefile for installation vars
sudo make install
```

You need to install the crontab manually. It will check each VM to verify if one need to be started, or is they have expired...

```
* * * * * if [ $(ps aux | grep "prototype/console" | grep -v grep | wc -l) -lt 1 ] ; then /var/www/lafourchette-prototype/console prototype:get-vm-id | xargs -P 4 -n 1 -r /var/www/lafourchette-prototype/console prototype:check ; fi;
```

Testing
-------

```bash
make test
```

See also
--------
* https://docs.google.com/a/lafourchette.com/document/d/11e9pBdeqv3Wtt0y9FXcka26nb2vH6zc96TVDjz3unWA/edit#


How prototype works
-------------------
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