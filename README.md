# monolog-influxdb

InfluxDB Handler for Monolog, which allows to store log messages in InfluxDB.

## Install

Via Composer

``` bash
$ composer require elardvermeulen/monolog-influxdb
```

## Usage

``` php
<?php

use InfluxDBHandler\InfluxDBHandler;

$influxDBHandler = new InfluxDBHandler('username','password','influxdb','hostname', '8086','dbname', \Monolog\Logger::DEBUG);

//Create logger
$logger = new \Monolog\Logger('channel');
$logger->pushHandler($influxDBHandler);

// example on how to create a info log
$logger->info("User succesfully logged in.", array('username'  => 'Peter Doe', 'userid'  => 89));

// example on how to create a info log
$logger->warning("Failed login attempt.", array('username'  => 'Peter Doe'));

// example on how to create a info log
$logger->error("Oops, something went horribly wrong.", array('username'  => 'John Doe', 'userid'  => 90));

```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please use the issue tracker.

## Requirements
Monolog InfluxDB works with PHP 7.0 or above.

## Credits

- Elard Vermeulen <https://github.com/elardvermeulen>
- All Contributors <https://github.com/elardvermeulen/monolog-influxdb/contributors>

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
