#/bin/sh

docker volume create -d local -o type=cifs -o device=./ www
