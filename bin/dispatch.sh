#!/bin/bash

PROFILE=$1
ASGROUP=$2

PROJ_PATH=`dirname $0`/../simpleaws
DB=${PROJ_PATH}/data/${PROFILE}.db3
NOTDONE_FLAG=0
CURRTIME="`date '+%Y-%m-%d %H:%M:%S'`"

underscored_asgroup=`echo $ASGROUP | sed -e 's/ /_/g'`

sql='SELECT quantity, done FROM schedule WHERE as_group = "'$underscored_asgroup'" AND datetime(run_at) <= datetime("'$CURRTIME'") ORDER BY run_at DESC LIMIT 1;'
result=`sqlite3 -csv $DB "$sql" | sed -e 's/\r//'`
done_flag=`echo $result | awk 'BEGIN { FS = "," } { print $2 }'`
if [ "x${done_flag}" = "x0" ]; then
    quantity=`echo $result | awk 'BEGIN { FS = "," } { print $1 }'`

    if [ $quantity -ge 1 -a $quantity -le 10 ]; then
        command="aws --profile $PROFILE autoscaling set-desired-capacity --auto-scaling-group-name '"$ASGROUP"' --desired-capacity $quantity --honor-cooldown"
        echo $command
    else
        echo "Scaling to $quantity?"
    fi
fi


exit 0;
