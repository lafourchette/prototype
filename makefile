DOMAIN=prototype.local
PORT=80
ROOT=$(shell pwd)

sqlite3-exists: ; @which sqlite3 > /dev/null
composer.phar:
	php -r "readfile('https://getcomposer.org/installer');" | php

build: composer.phar
	php ./composer.phar install
	git describe --tags > VERSION

install: sqlite3-exists
	cp installer/nginx.conf /etc/nginx/sites-available/lafourchette-prototype
	sed "s/%DOMAIN%/$(DOMAIN)/g" -i /etc/nginx/sites-available/lafourchette-prototype
	sed "s/%PORT%/$(PORT)/g" -i /etc/nginx/sites-available/lafourchette-prototype
	sed "s@%ROOT%@$(ROOT)@g" -i /etc/nginx/sites-available/lafourchette-prototype
	[ -e /etc/nginx/sites-enabled/lafourchette-prototype ] || ln -s /etc/nginx/sites-available/lafourchette-prototype /etc/nginx/sites-enabled/lafourchette-prototype
	service nginx reload

config.json:
	cp installer/config.json config.json

test: db.sqlite3 config.json
	[ -e tmp ] || mkdir tmp
	echo "Run php -S localhost:8000 -t web web/index.php"

db.sqlite3:
	cat installer/schema.sql | sqlite3 db.sqlite3

clean:
	rm -rf logs/*.log
	rm -rf tmp

mrproper: clean
	rm config.json
	rm db.sqlite3

close:
	touch MAINTENANCE.lock