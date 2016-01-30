#!/bin/bash

SERVER="deploy@ritho.net"
PORT="1982"

function dlog {
	echo " => $1"
}

dlog "Starting deploy..."

dlog "Pushing package to prod server"
#scp -P ${PORT} ${DEB_FILE} ${SERVER}:~/

dlog "Installing new version of gestas"
#ssh -p ${PORT} -tt ${SERVER} "sudo dpkg -i ~/${DEB_FILE} 2>&1"
