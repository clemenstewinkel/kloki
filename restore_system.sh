#!/bin/bash
cd $(dirname $0)

USERNAME=$(grep "^DATABASE_URL" .env | cut -f 2 -d ':' | cut -f 3 -d '/')
PASSWORD=$(grep "^DATABASE_URL" .env | cut -f 3 -d ':' | cut -f 1 -d '@')
DBNAME=$(grep "^DATABASE_URL" .env | cut -f 4 -d '/' | cut -f 1 -d '?')

TEMPDIR=$(mktemp -d)
tar -C $TEMPDIR -xzf $1 
mysql -u $USERNAME --password=$PASSWORD $DBNAME < $TEMPDIR/dbdump.sql
rm -rf uploaded_files 
mv $TEMPDIR/uploaded_files .
rm -rf $TEMPDIR

chown -R apache:apache .
