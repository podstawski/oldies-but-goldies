for i in `find . -name adodb`
do
	svn del $i
	svn -m "usuwam adodb" ci
	cp -Rp /tmp/adodb $i
	svn add $i
	svn -m "nowe adodb" ci
done
