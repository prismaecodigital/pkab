#!/bin/sh
# Import every dump in /opt/dump on first boot of an empty MySQL volume.
# Only the development stack mounts that directory.
set -e

for file in /opt/dump/*.sql /opt/dump/*.sql.gz; do
    [ -e "$file" ] || continue

    echo "Importing $file into $MYSQL_DATABASE"

    case "$file" in
        *.gz) gunzip -c "$file" | mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" ;;
        *)    mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" < "$file" ;;
    esac
done
