#!/bin/sh

# domosl.sh		- scenario loop
# 2014-07-30	v 1.0

DIR=/var/www/html/protected

do_start(){
    count=$(ps aux | grep "yiic support scenarioloop" | wc -l)

    if [ $count -ge 2 ]; then
        echo "Already started"
    else
        nohup $DIR/yiic support scenarioloop > /dev/null &
        echo "Started success"
    fi

}

do_stop(){
    count=$(ps aux | grep "yiic support scenarioloop" | wc -l)

    if [ $count -ge 2 ]; then
        kill `ps aux | grep "yiic support scenarioloop" | awk '{print $2}'`
        echo "Stopped success"
    else
        echo "Yet not started"
    fi

}

do_status(){
    count=$(ps aux | grep "yiic support scenarioloop" | wc -l)

    if [ $count -ge 2 ]; then
        echo "Status: running"
    else
        echo "Status: NOT started"
    fi
}


case "$1" in

    start|"")
        do_start
        exit 0
        ;;

    restart)
	do_stop
	do_start
        exit 0
        ;;

    status)
	do_status
	exit 0;
	;;

    stop)
	do_stop
        exit 0
        ;;

    *)
        echo "Usage: domosl.sh [start|restart|stop|status]"
        exit 3
        ;;

esac

:
