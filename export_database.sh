#!/bin/bash

echo 'Creating database'
mysql -u root -padmin < Create.sql
echo 'Prestashop database created'

echo 'Prestashop database importing...'
mysql prestashop -u root -padmin < prestashop.sql
echo 'Prestashop database imported'
