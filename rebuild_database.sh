#!/bin/bash
rm var/data.db
rm src/Migrations/*
./bin/console doctrine:database:create
./bin/console make:migration 
./bin/console do:mi:mi 

rm uploaded_files/*/*
./bin/console do:fi:lo 
