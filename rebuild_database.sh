#!/bin/bash
rm src/Migrations/*
echo "Deleting database..."
./bin/console doctrine:database:drop --force
echo "Recreating database..."
./bin/console doctrine:database:create
echo "Create migration..."
./bin/console make:migration 
echo "Performing migration..."
./bin/console do:mi:mi 
echo "Deleting uploaded files..."
rm uploaded_files/*/*
echo "creating fixtures..."
./bin/console do:fi:lo 
