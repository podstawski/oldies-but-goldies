#!/bin/sh


cd `dirname $0`/..

cms=`hostname|grep cms`

if [ ! $cms ]
then
	svn ci
	ssh root@cms.gammanet.pl /www/beta/admintools/ver.sh
	exit
fi


svn up
rev=`svn info | grep -i "changed rev:" | awk '{print $NF}'`
if [ ! "$rev" ]
then
	rev=`svn info | grep -i "ostatnio zmieniona wersja:" | awk '{print $NF}'`
fi

oldrev=`php -r 'include("include/rev.h");echo $KAMELEON_VERSION_REV;'`



if [ "$oldrev" != "$rev" ]
then
        echo "$oldrev vs. $rev" 
	trzeba_tarowac=1
	echo "<?php
		\$KAMELEON_VERSION_REV=$rev;" > include/rev.h
fi



pwddir=`pwd`
kameleon_path=`pwd`

TMP=/tmp/jhsdkf78asdhasjdbjhasd7f87asdtf8tasdf
echo "<? include(\"$kameleon_path/const.php\"); echo \$C_DB_CONNECT_USER;?> "|php -q | awk '{print $1}' >$TMP
export PGUSER=`cat $TMP`
echo "<? include(\"$kameleon_path/const.php\"); echo \$C_DB_CONNECT_PASSWORD;?> "|php -q | awk '{print $1}' >$TMP
export  PGPASSWORD=`cat $TMP`
echo "<? include(\"$kameleon_path/const.php\"); \$a=explode(':',\$C_DB_CONNECT_HOST); echo \$a[0];?> "|php -q | awk '{print $1}' >$TMP
export  PGHOST=`cat $TMP`
echo "<? include(\"$kameleon_path/const.php\"); \$a=explode(':',\$C_DB_CONNECT_HOST); echo \$a[1];?> "|php -q | awk '{print $1}' >$TMP
export  PGPORT=`cat $TMP`
export  PGPORT=`cat $TMP`
echo "<? include(\"$kameleon_path/const.php\"); echo \$C_DB_CONNECT_DBNAME;?> "|php -q | awk '{print $1}' >$TMP
export  DB=`cat $TMP`

echo "<? include(\"$kameleon_path/include/const.h\"); echo \$KAMELEON_VERSION;?> "|php -q | awk '{print $1}' >$TMP
ver=`cat $TMP`
rm -f $TMP

schema_file="$kameleon_path/changes/postgres-schema-$ver.sql"

if [ "$trzeba_tarowac" ]
then

    echo -n "Dump bazy ..."
    $kameleon_path/admintools/pg_dump -i -s -x $DB \
            | grep -v " OWNER TO " \
            | grep -v "\-\- " \
            | grep -v "REVOKE " \
            | grep -v "GRANT " \
            | grep -v "COMMENT ON" \
            | $kameleon_path/admintools/remove_tsearch.php \
            > $schema_file
    echo " ok"
    
    svn add $schema_file 2>/dev/null 
    svn ci -m "schemat bazy" $schema_file




    pliki=`find . -type f -print | grep -v "/.svn/" | grep -v "/admintools/" | grep -v "/uincludes/" | grep -v "/uimages/" | grep -v "/ufiles/" | grep -v "/szablony/" | grep -v "/log/" | grep -v "/log/" | grep -v "/FCKeditor/" | grep -v "/cache/" | grep -v "/out/" | grep -v "/fakro/" | grep -v "\./plugins/"  `
    
    
    force=0
    if [ "$1" = "force" ]
    then
            force=1
    fi
    
    destdir=admintools/final
    
    pluginfiles=""
    
    cd plugins
    for p in *.php
    do
            pluginfiles="./plugins/$p $pluginfiles"
    done
    cd ..



    for plik in $pliki $pluginfiles
    do
            if [ "$plik" = "./const.php" ]
            then
                    continue
            fi
    
            e=`basename $plik | awk -F. '{print $NF}'`
            p=`echo $plik | awk '{print substr($1,3)}'`
            finalplik=$destdir/$p
            finaldir=`dirname $finalplik`
    
            if [ "$force" = "0" ]
            then
                    if [ $finalplik -nt $p ]
                    then
                            continue
                    fi
            fi
    
            if [ ! -d $finaldir ]
            then
                    mkdir -p $finaldir
                    
                    d=""
                    for dir in `echo $finaldir | awk -F/ '{for (i=1;i<=NF;i++) print $i " "}'`
                    do
                            d="$d$dir/"
                            svn add $d 2>/dev/null
                    done	
                    
            fi
    
    
            encode=0
            newfile=1
    
            if [ -f $finalplik ]
            then
                    newfile=0
            fi
            
    
            if [ "$e" = "php" ]
            then
                    encode=1
            fi
            if [ "$e" = "h" ]
            then
                    encode=1
            fi
    
            if [ `echo $plik | grep "/remote/"` ]
            then
                    encode=0
            fi
    
            if [ `echo $plik | grep "/adodb/"` ]
            then
                    encode=0
            fi
    
            if [ `echo $plik | grep "/editor/"` ]
            then
                    encode=0
            fi
    
            if [ `echo $plik | grep "/setup/"` ]
            then
                    encode=0
            fi
    
            if [ `echo $plik | grep "/szablony.def/"` ]
            then
                    encode=0
            fi
    
            if [ `echo $plik | grep "/noloader.php"` ]
            then
                    encode=0
            fi
    
            if [ `echo $plik | grep "openid"` ]
            then
                    encode=0
            fi
    
            if [ "$encode" = "1" ]
            then
                     /usr/local/encoder/ioncube_encoder \
                            --without-runtime-loader-support \
                            --action-if-no-loader="include('noloader.php');" \
                            --compress --erase-target \
                            --allow-call-time-pass-reference \
                            --without-keeping-file-times \
                            --include "*.h" \
                            -o $finalplik $p
    
                    echo "Koduje plik $p"
    
            else
                    cp $p $finalplik
            fi
    
    
            if [ "$newfile" = "1" ]
            then
                    svn add $finalplik
            fi
    
    done


    rm -rf /tmp/kameleon
    cp -Rp $destdir /tmp/kameleon
    cd /tmp/kameleon
    for i in `find . -name .svn`
    do
            rm -r $i
    done

    echo -n "Taruje wersje ... "
    tar -czf $pwddir/out/.beta.tgz .htaccess * 
    mv $pwddir/out/.beta.tgz $pwddir/out/beta.tgz
    php $pwddir/admintools/md5.php $pwddir/out/beta.tgz > $pwddir/out/beta.md5
    cd /tmp
    rm -rf /tmp/kameleon
    echo ok
    
fi


cd $pwddir/plugins
destdir=/tmp/kameleon



for plugin in *
do
	cd $pwddir/plugins
	if [ ! -d $plugin ]
	then
		continue
	fi

       
        
	echo -n "Pakuje $plugin ... "
	cd $pwddir/plugins/$plugin

	svn up >/dev/null
        
        rev=`svn info | grep -i "changed rev:" | awk '{print $NF}'`
        if [ ! "$rev" ]
        then
                rev=`svn info | grep -i "ostatnio zmieniona wersja:" | awk '{print $NF}'`
        fi
        
        oldrev=`php -r 'include(".plugin_rev.php");echo $PLUGIN_REV;'`

        if [ "$oldrev" = "$rev" ]
        then
                echo "nie potrzeba"
                continue
        fi

        echo "<?php
                        \$PLUGIN_REV=$rev;" > .plugin_rev.php
    


	pliki=`find . -type f -print | grep -v "/.svn/"`
	rm -rf $destdir
	mkdir $destdir

	
	for plik in $pliki
	do
		e=`basename $plik | awk -F. '{print $NF}'`
		p=`echo $plik | awk '{print substr($1,3)}'`
		finalplik=$destdir/$p
		finaldir=`dirname $finalplik`

		if [ ! -d $finaldir ]
		then
			mkdir -p $finaldir
		fi

		encode=0
		

		if [ "$e" = "php" ]
		then
			encode=1
		fi
		if [ "$e" = "h" ]
		then
			encode=1
		fi

		if [ "$plugin" = "touroperator" ]
		then
			encode=0
		fi

		if [ "$plugin" = "acl" ]
		then
			encode=0
		fi


		if [ `echo $plik | grep "/setup/"` ]
		then
			encode=0
		fi


		if [ "$encode" = "1" ]
		then
			 /usr/local/encoder/ioncube_encoder \
				--without-runtime-loader-support \
				--action-if-no-loader="include('noloader.php');" \
				--compress --erase-target \
				--allow-call-time-pass-reference \
				--without-keeping-file-times \
				--include "*.h" \
				-o $finalplik $p

		else
			cp $p $finalplik
		fi


	done

	echo ok
	cd $destdir

	echo -n "Taruje $plugin ... "
	tar -czf $pwddir/out/.plugins/.beta.tgz * .[a-z]* 2>/dev/null
	mv $pwddir/out/.plugins/.beta.tgz $pwddir/out/.plugins/$plugin.tgz

	php $pwddir/admintools/md5.php $pwddir/out/.plugins/$plugin.tgz > $pwddir/out/.plugins/$plugin.md5

	echo ok
done

cd /tmp
rm -rf /tmp/kameleon
