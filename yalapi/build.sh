#!/bin/bash
cd `dirname $0`
dir=`pwd`

svn up
phing -f backend/build.xml chmod clear_cache

php backend/scripts/migrate.php

cd frontend
python generate.py build

cd $dir
phing -f backend/build.xml loader

cd $dir
php backend/scripts/recreate_acl.php
