DOMAIN=prototype.local
PORT=80
ROOT=$(shell pwd)

sqlite3-exists: ; @which sqlite3 > /dev/null
composer.phar:
	php -r "readfile('https://getcomposer.org/installer');" | php

build: composer.phar
	php ./composer.phar install

install: sqlite3-exists
	cat db/dev.sql | sqlite3 db/dev
	cp installer/nginx.conf /etc/nginx/sites-available/lafourchette-prototype
	sed "s/%DOMAIN%/$(DOMAIN)/g" -i /etc/nginx/sites-available/lafourchette-prototype
	sed "s/%PORT%/$(PORT)/g" -i /etc/nginx/sites-available/lafourchette-prototype
	sed "s@%ROOT%@$(ROOT)@g" -i /etc/nginx/sites-available/lafourchette-prototype
	[ -e /etc/nginx/sites-enabled/lafourchette-prototype ] || ln -s /etc/nginx/sites-available/lafourchette-prototype /etc/nginx/sites-enabled/lafourchette-prototype
	service nginx reload
yay:
	echo $(ROOT)