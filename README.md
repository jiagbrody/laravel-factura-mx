# Librería para la facturacion en México, creacion de facturas y CFDI.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jiagbrody/laravel-factura-mx.svg?style=flat-square)](https://packagist.org/packages/jiagbrody/laravel-factura-mx)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jiagbrody/laravel-factura-mx/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jiagbrody/laravel-factura-mx/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jiagbrody/laravel-factura-mx/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jiagbrody/laravel-factura-mx/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jiagbrody/laravel-factura-mx.svg?style=flat-square)](https://packagist.org/packages/jiagbrody/laravel-factura-mx)

This package is used with the Laravel Framework and currently resolves billing in Mexico for CFDI version 4.0. It is likely that the documentation will be based on Spanish since it is the native language for technicalities and others according to what governs the SAT “Servicio de Administración Tributaria”.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-factura-mx.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-factura-mx)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require jiagbrody/laravel-factura-mx
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-factura-mx-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-factura-mx-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="laravel-factura-mx-views"
```

## Usage

```php
$laravelFacturaMx = new JiagBrody\LaravelFacturaMx();
echo $laravelFacturaMx->echoPhrase('Hello, JiagBrody!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [jiagbrody](https://github.com/jiagbrody)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
