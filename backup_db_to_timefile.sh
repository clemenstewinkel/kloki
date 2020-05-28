#!/bin/bash
cd $(dirname $0)

USERNAME=$(grep "^DATABASE_URL" .env.local | cut -f 2 -d ':' | cut -f 3 -d '/')
PASSWORD=$(grep "^DATABASE_URL" .env.local | cut -f 3 -d ':' | cut -f 1 -d '@')
DBNAME=$(grep "^DATABASE_URL" .env.local | cut -f 4 -d '/' | cut -f 1 -d '?')

TEMPDIR=$(mktemp -d)
FILENAME=$(date +%Y%m%d-%H%M%S.tgz)
mysqldump -u $USERNAME --password=$PASSWORD $DBNAME > $TEMPDIR/dbdump.sql
tar -C $TEMPDIR -czf /root/kloki_backups/$FILENAME dbdump.sql 
rm -rf $TEMPDIR
