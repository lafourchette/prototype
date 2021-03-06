DOMAIN=prototype.local
PORT=80
ROOT=$(shell pwd)
BOWER_C=bower_components

#
# Main targets
#
build: composer.phar config.json db.sqlite3
	php ./composer.phar install
	git describe --tags > VERSION

build-assets:
	node_modules/.bin/bower install
	cp -f $(BOWER_C)/bootstrap/dist/css/bootstrap.min.css web/css/.
	cp -f $(BOWER_C)/bootstrap/dist/fonts/* web/fonts/.
	cp -f $(BOWER_C)/bootstrap/dist/js/bootstrap.min.js web/js/.
	cp -f $(BOWER_C)/chosen/chosen.jquery.min.js web/js/.
	cp -f $(BOWER_C)/chosen/chosen.min.css web/css/.
	cp -f $(BOWER_C)/chosen/*.png web/css/.
	cp -f $(BOWER_C)/jeditable/jquery.jeditable.js web/js/.
	cp -f $(BOWER_C)/jquery/dist/jquery.min.js web/js/.
	cp -f $(BOWER_C)/components-font-awesome/css/font-awesome.min.css web/css/.
	cp -f $(BOWER_C)/components-font-awesome/fonts/* web/fonts/.

clean:
	rm -rf logs/*.log
	rm -rf tmp

mrproper: clean
	rm config.json
	rm db.sqlite3

#
# Sub targets
#
composer.phar:
	php -r "readfile('https://getcomposer.org/installer');" | php

install-nginx:
	cp dist/nginx.conf /etc/nginx/sites-available/lafourchette-prototype
	sed "s/%DOMAIN%/$(DOMAIN)/g" -i /etc/nginx/sites-available/lafourchette-prototype
	sed "s/%PORT%/$(PORT)/g" -i /etc/nginx/sites-available/lafourchette-prototype
	sed "s@%ROOT%@$(ROOT)@g" -i /etc/nginx/sites-available/lafourchette-prototype
	[ -e /etc/nginx/sites-enabled/lafourchette-prototype ] || ln -s /etc/nginx/sites-available/lafourchette-prototype /etc/nginx/sites-enabled/lafourchette-prototype
	service nginx reload

config.json:
	cp dist/config.json config.json

test: db.sqlite3 config.json
	[ -e tmp ] || mkdir tmp
	echo "Run php -S localhost:8000 -t web web/index.php"

db.sqlite3:
	cat dist/schema.sql | sqlite3 db.sqlite3
