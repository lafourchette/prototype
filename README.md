```
    ____             __        __
   / __ \_________  / /_____  / /___  ______  ___
  / /_/ / ___/ __ \/ __/ __ \/ __/ / / / __ \/ _ \
 / ____/ /  / /_/ / /_/ /_/ / /_/ /_/ / /_/ /  __/
/_/   /_/   \____/\__/\____/\__/\__, / .___/\___/
                               /____/_/
```
## Introduction
Prototype was created to help product owner to test their production in a full stack environnement.

* Start/Stop/Build VMs at ease
* LDAP auth
* Automatic mail alert on events (start, stopped, expired)

## Installation
After cloning the repository:
```bash
make build
```
Then edit config.json
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
And serve it with a webserver, for example
```bash
php -S localhost:8000 -t web
```

Contributors
------------
- Laurent Chenay (lchenay), original idea
- Guillaume Cavana (gcavana), main developer
- Oliver Laurendeau (olaurendeau)
- Laurent Robin, ux
- David Moreau (dav-m85), additionnal developer