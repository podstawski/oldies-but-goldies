#!/bin/sh

cmd=$1

file="$2/$3"

if [ "$cmd" = "commit" ]
then
	cmd="-m a commit"
fi

if [ "$cmd" = "update" ]
then
	file=$2
fi


/usr/local/bin/svn --username $4 --password $5 --non-interactive $cmd $file >/dev/null 2>/tmp/svn.$4.err

cat /tmp/svn.$4.err
rm -f /tmp/svn.$4.err
