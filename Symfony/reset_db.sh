./ccache.sh
if [ -d web/uploads ] 
then
	rm -rf web/uploads
fi
mkdir web/uploads
chmod 777 web/uploads
php app/console doctrine:query:sql "drop database if exists droppy;"
php app/console doctrine:database:create
php app/console doctrine:schema:update --force
php app/console doctrine:query:sql "insert into droppy.color (id,name,code)
values(NULL,'blue','#cccff'),(NULL,'red','#ffcccc'),(NULL,'green','#ccffcc'),
(NULL,'violet','#f0ccff'), (NULL,'orange','#ffddcc'), (NULL, 'yellow', '#ffffcc')"
timezones=`cat timezones`
php app/console doctrine:query:sql "insert into droppy.timezone (id,name,difference) values $timezones"
php app/console doctrine:query:sql "insert into droppy.language (id,name,english_name,locale)
values(NULL,'Japanese', 'Japanese', 'ja'),(NULL,'English', 'English', 'en'),(NULL,'Francais', 'French', 'fr')"
