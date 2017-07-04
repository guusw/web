# Web server setup

## Setup

Before running any of the services or after changing the configuration templates (conf/*servicename*/*configuration_file*.m4) you should run `./configure` in the root of this project to (re)generate these configuration files

All constants used in these configuration templates are pulled from the `.config` file in the project root.

A separate step is required to initialize the sql database, for this run `./install_db` in the root of the project folder. To reinstall, first remove the mysqld folder in the project root (this is the sql data folder)

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

* git (to download openssl automatically)
* pcre (development libraries, optional)
* openssl (development libraries, optional)
* nginx (optional)
* php-cgi
* mysqld (MariaDB)
* m4 (for configuration file template generation)

Optional libraries can be downloaded and compiled automatically (see `.config` and `build` files and the section below for more info)

## Custom nginx build:

This repository contains some scripts to make a custom nginx build and use it instead of the one installed on the system.

To build this version, do the following:

* Check `.config` for options
    * Check the options to automatically download PCRE/OpenSSL if you don't have them installed
* Run `./build` in the project root folder
