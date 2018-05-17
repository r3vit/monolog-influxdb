# monolog-influxdb

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]


InfluxDB Handler for Monolog, which allows to store log messages in InfluxDB.

## Install

Via Composer

``` bash
$ composer require elardvermeulen/monolog-influxdb
```

## Usage

``` php
//Import class
use InfluxDBHandler\InfluxDBHandler;

//Create InfluxDBHandler
$influxDBHandler = new InfluxDBHandler(, \Monolog\Logger::DEBUG);

//Create logger
$logger = new \Monolog\Logger($context);
$logger->pushHandler($influxDBHandler);

//Now you can use the logger, and further attach additional information
$logger->addWarning("This life is only a test.", array('username'  => 'John Doe', 'userid'  => 245));
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

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
