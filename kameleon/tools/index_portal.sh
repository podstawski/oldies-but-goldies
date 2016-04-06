#!/bin/sh

KAMELEON_DIR=/www/kameleon
PHP_PATH=/usr/local/bin

eval $1
eval $2 

chdir `dirname $0`

$PHP_PATH/php -q $KAMELEON_DIR/tools/ftp_cron.php
$PHP_PATH/php -q $KAMELEON_DIR/tools/index_portal_cron.php >> $KAMELEON_DIR/log/index_portal.log
