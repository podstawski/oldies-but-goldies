#!/bin/sh
dir=`pwd`
cd `dirname $0`


svn up

sh scripts/lang.sh

phing -f build.xml

cd $dir
