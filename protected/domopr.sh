#!/bin/sh

# domopr.sh		- main process
# 2014-06-11	v 1.0

DIR=/var/www/html/protected

do_start(){
    count=$(ps aux | grep "yiic rabbitmq process" | wc -l)

    if [ $count -ge 2 ]; then
        echo "Already started"
    else
        nohup $DIR/yiic rabbitmq process > /dev/null &
        echo "Started success"
    fi

}

do_stop(){
    count=$(ps aux | grep "yiic rabbitmq process" | wc -l)

    if [ $count -ge 2 ]; then
        kill `ps aux | grep "yiic rabbitmq process" | awk '{print $2}'`
        echo "Stopped success"
    else
        echo "Yet not started"
    fi

}

do_status(){
    count=$(ps aux | grep "yiic rabbitmq process" | wc -l)

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
        echo "Usage: domopr.sh [start|restart|stop|status]"
        exit 3
        ;;

esac

:
