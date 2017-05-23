#!/bin/bash
source .config
CONFIG_DIR=$ROOT_PATH/conf/certbot
certbot certonly --webroot --config-dir=/home/guus/nginx/certbot --work-dir=/home/guus/nginx --logs-dir=/home/guus/nginx/logs -w /home/guus/nginx/letsencrypt -d shr.tdrz.nl

