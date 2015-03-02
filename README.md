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

* Start/Stop/Build VMs at ease
* LDAP auth
* Automatic mail alert on events (start, stopped, expired)

Install
-------
```bash
make build

# check makefile for installation vars
sudo make install
```

You need to install the crontab manually. It will check each VM to verify if one need to be started, or is they have expired...
```cron
* * * * * if [ $(ps aux | grep "prototype/console" | grep -v grep | wc -l) -lt 1 ] ; then /var/www/lafourchette-prototype/console prototype:get-vm-id | xargs -P 4 -n 1 -r /var/www/lafourchette-prototype/console prototype:check ; fi;
```

Configuration goes that way
```javascript
"provisioners":[{
    "type": "local", // local file
    "path": "."
},{
    "type":"github", // github remote file
    "repository":"",
    "path":"",
    "token":"",
    "user":""
}]
```

Testing
-------
```bash
make test
```

Contributors
------------
- Laurent Chenay (lchenay), original idea
- Guillaume Cavana (gcavana), main developer
- Oliver Laurendeau (olaurendeau)
- Laurent Robin, ux
- David Moreau (dav-m85), additionnal developer