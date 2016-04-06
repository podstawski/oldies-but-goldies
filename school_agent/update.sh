#!/bin/bash
cd `dirname $0`
svn up
phing -f build.xml
