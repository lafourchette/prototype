# Lafourchette Prototype

Read [Functional documentation](doc/index.md)

# Install

*Nginx*

```bash
sudo cp installer/nginx.conf /etc/nginx/sites-available/lafourchette-prototype
sudo ln -s /etc/nginx/sites-available/lafourchette-prototype /etc/nginx/sites-enabled/lafourchette-prototype
sudo service nginx reload
```

*Sqllite*

''''
sqlite3 db/dev
Ctrl+Z
cat db/dev.sql | sqlite3 db/dev
''''

# Crontab

The system need a crontab to work properly.

''''
* * * * * if [ $(ps aux | grep "prototype/console" | grep -v grep | wc -l) -lt 1 ] ; then /var/www/lafourchette-prototype/console prototype:get-vm-id | xargs -P 4 -n 1 -r /var/www/lafourchette-prototype/console prototype:check ; fi;
''''

This crontab will check each VM to verify if one need to be started, or is they have expired...

# See also

See also: https://docs.google.com/a/lafourchette.com/document/d/11e9pBdeqv3Wtt0y9FXcka26nb2vH6zc96TVDjz3unWA/edit#
