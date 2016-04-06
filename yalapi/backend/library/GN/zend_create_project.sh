#!/bin/bash

#sh zend_create_project.sh nazwa_projektu katalog

if [ ! $2 ]
then
	echo $0 project_name project_remote_svn_dir [local_dir]
	exit
fi

local_dir=`dirname $0`
if [ $3 ]
then
	local_dir=$3
fi

rm -rf $local_dir/$1
mkdir -p $local_dir/$1
cd $local_dir/$1
svn co https://svn.gammanet.pl/include/ZendProject .
svn mkdir -m '' https://svn.gammanet.pl/$2
svn mkdir -m '' https://svn.gammanet.pl/$2/$1
svn mkdir -m '' https://svn.gammanet.pl/$2/$1/trunk
find -name '.svn'|xargs rm -rf
svn co https://svn.gammanet.pl/$2/$1/trunk .
svn add *
svn ci -m 'created project $1'
cd library
sh .externals.sh
cd ..
svn up
