#!/bin/bash
#
# http://symfony.com/doc/current/book/installation.html
#
PROJ_PATH=`dirname $0`/..
HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX ${PROJ_PATH}/app/cache ${PROJ_PATH}/app/logs
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX ${PROJ_PATH}/app/cache ${PROJ_PATH}/app/logs
#
#
exit 0;
