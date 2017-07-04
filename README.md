# Web server setup

## Setup

Before running any of the services or after changing the configuration templates (conf/*servicename*/*configuration_file*.m4) you should run `./configure` in the root of this project to (re)generate the actually used configuration files

All constants used in these configuration templates are pulled from the `.config` file in the project root.

A separate step is required to initialize the sql database, for this run `./install_db` in the root of the project folder. To reinstall, first remove the mysqld folder in the project root (this is the sql data folder)

## Adding removing components

### Add/remove virtual hosts / proxies

Edit server blocks in `conf/nginx/nginx.conf.m4` and run `configure` (additionally restart nginx)

### Add/remove generated certificates

change the `run_certbot` script by adding or removing `-d <hostname>` lines, or adding/removing a line.

The current way these are arranged is one per root domain with subject alternative names for their subdomains.

These are then referenced by their domain name in `conf/nginx/nginx.conf.m4`. This looks something like:
```m4
ssl_certificate ifdef(`CERT_TEST', testcrt.txt, CERTBOT_LIVE/<server_name>/fullchain.pem);
ssl_certificate_key ifdef(`CERT_TEST', testkey.txt, CERTBOT_LIVE/<server_name>/privkey.pem);
```
where `<server_name>` is the name of the root domain inside the `run_certbot` script.

The m4 ifdef allows easy switching to test certificates for offline development

## Running the services

Each service has it's own run scripts which take one argument indicating what to do:

* start
* stop
* restart

Using start or restart will run the given service in the background and pass the local configuration files for this project to them (which should not require root to run)

The service scripts are:

* `run_php`
* `run_mysqld`
* `run_nginx`

## SSL certificates

To setup ssl certificates, use the `run_certbot` script which will generate/renew any required certificates

Check the contents of this script for the actual used domains

## Requirements

* nginx
* php-cgi
* m4
* mysqld (only tested with MariaDB)
* web-gitea setup (optional, [https://git.bakje.coffee/guus/web-gitea](https://git.bakje.coffee/guus/web-gitea))