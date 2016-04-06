#!/bin/bash


cd `dirname $0`/..


function pobierz 
{
	wget $1 >/dev/null 2>/dev/null
	if [ ! -f `basename $1` ]
	then
		fetch $1
	fi
}



echo -n "Getting newest version of kameleon ..."

pobierz http://beta.webkameleon.com/out/beta.md5

if [ "`cat beta.md5`" != "`cat .beta.md5`" ] 
then
	pobierz http://beta.webkameleon.com/out/beta.tgz 
	tar -xzf beta.tgz
	rm -f beta.tgz 
	echo " ok"
	mv beta.md5 .beta.md5
else
	echo " no need"
	rm -f beta.md5
fi

cd plugins
for plugin in *
do
	if [ ! -d $plugin ]
	then
		continue
	fi

	echo -n "Getting newest version of $plugin ..."
	cd $plugin


	pobierz http://beta.webkameleon.com/out/.plugins/$plugin.md5
	if [ "`cat $plugin.md5`" != "`cat .$plugin.md5`" ] 
	then
		pobierz http://beta.webkameleon.com/out/.plugins/$plugin.tgz 

		tar -xzf $plugin.tgz
		rm -f $plugin.tgz
	
		mv $plugin.md5 .$plugin.md5
		echo " ok"

	else
		echo " no need"
		rm -f $plugin.md5
	fi

	cd ..
done
