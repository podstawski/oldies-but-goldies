.ifndef SRCDIR

SRCDIR=/www/kameleon.gammanet.pl
.endif

.ifndef mfdir
mfdir=${PWD}
.endif

.ifndef makedir
makedir=.
.endif

all: admin admin/include admin/include/action \
	tools include license include/action include/class \
	api api/include api/include/action \
	api/include/captcha api/include/captcha/fonts api/include/captcha/util \
	info.php noauth.php licence.php index.php empty.php preview.php login.php \
	modules \
	modules/@crm modules/@crm/action modules/@crm/.update modules/@crm/.delete modules/@crm/calendar \
	modules/@api modules/@api/.api/action modules/@api/.update modules/@api/.delete modules/@api modules/@api/kameleon  modules/@api/.api
	@for i in $> ; do dir=`dirname $$i` ; \
	if [ ! "$$dir" = "." ] ; then \
		olddir=${PWD} ;  oldsrc=${SRCDIR} ;\
		cd $$dir ; \
		export mfdir=${mfdir} ; export SRCDIR=${SRCDIR}/$$dir ;\
		if [ ! -d `basename $$i` ] ; then mkdir `basename $$i` ; fi ; \
		touch -t 9601010000 `basename $$i` ; \
		make -f ${mfdir}/Makefile `basename $$i` ; \
		cd $$olddir ; SRCDIR=${oldsrc} ;\
	fi ;done



*.h *.php .*.h : ${SRCDIR}/${makedir}/$@ ${mfdir}/Makefile
	ioncube5 --action-if-no-loader="include('noloader.php');" \
		--preamble-file /www/kameleon1e.gammanet.pl/preamble \
		--compress --erase-target \
		--allow-call-time-pass-reference \
		--without-keeping-file-times \
		--include "*.h" \
		${SRCDIR}/${makedir}/$@ -o ./$@ \


*.html *.sh *.js *.css *.xml .*.xml *.htm* *.inc *.gif *.jpg *.png: ${SRCDIR}/${makedir}/$@ ${mfdir}/Makefile
	cp -p ${SRCDIR}/${makedir}/$@  ./$@

.ifndef regular_file

* .*: 	${SRCDIR}/$@ ${mfdir}/Makefile
	@if [ -d $@ ]; then cd $@; \
	echo "entering `pwd`" ;\
	export makedir=$@; export mfdir=${mfdir} ; \
	for i in ${SRCDIR}/$@/.[a-zA-Z0-9]* ${SRCDIR}/$@/* ; \
	do if [ ! -h $$i ] ; then if [ -f $$i ] ; then \
		if [ ! -f "`basename $$i`" ] ; then touch -t 9601010000 `basename $$i` ; fi ;\
		make -D regular_file -f "${mfdir}/Makefile" `basename $$i` ; \
	fi ; \
	fi ; done ; fi

.endif





const.h.trash index_portal.sh changes.txt:	
	@echo -n

#modules/@learn modules/@learn/action modules/@learn/.update modules/@learn/.delete modules/@learn/js modules/@learn/kameleon \
