# ecom-settings

The settings component of ecom.

## Installation

The recommended way to install ecom-settings is via [composer](http://getcomposer.org).

1. Add a single line to composer.json:

    ```
    "require": {
    	...
    	"ecom/settings": "dev-master"
    	...
    }
    ```
    
2. Run composer to update denpendencies:

    ```
    $ cd /path/to/project
    $ php composer.phar update
    ```


## Features

* Multiple storage backend support, such as RDMBS database and Redis.
* Custom storage backend support, by implement the StorageInterface interface.
* ArrayAccess support, you can access settings just like arrays.


## Authors

* Jin Hu <bixuehujin@gmail.com>


## Licence

* MIT
