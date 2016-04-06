cat _install.sh > headmaster.sh

mkdir tmp
cp -Rp application library public tmp
cd tmp
rm -rf application/logs/* application/cache/*
find -name .svn |xargs rm -rf

tar czf  ../headmaster.tgz *
cat ../headmaster.tgz >> ../headmaster.sh
cd ..
rm -rf tmp
chmod +x headmaster.sh
