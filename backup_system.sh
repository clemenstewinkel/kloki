#!/bin/bash
cd $(dirname $0)

USERNAME=$(grep "^DATABASE_URL" .env | cut -f 2 -d ':' | cut -f 3 -d '/')
PASSWORD=$(grep "^DATABASE_URL" .env | cut -f 3 -d ':' | cut -f 1 -d '@')
DBNAME=$(grep "^DATABASE_URL" .env | cut -f 4 -d '/' | cut -f 1 -d '?')


TEMPDIR=$(mktemp -d)

mysqldump -u $USERNAME --password=$PASSWORD $DBNAME > $TEMPDIR/dbdump.sql
cp -r uploaded_files $TEMPDIR
tar -C $TEMPDIR -czf $1 dbdump.sql uploaded_files
rm -rf $TEMPDIR
