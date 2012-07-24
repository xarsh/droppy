./ccache.sh
rm -r web/uploads
php app/console doctrine:query:sql "drop database if exists droppy;"
php app/console doctrine:database:create
php app/console doctrine:schema:update --force
php app/console doctrine:query:sql "insert into droppy.genre (id, name, canonical_name)
values (null, 'IT', 'it'), (null, 'Music', 'music'), (null, 'Other', 'other');"


