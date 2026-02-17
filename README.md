# Laravel MTN MoMo

[![Latest Version on Packagist](https://img.shields.io/packagist/v/akika/laravel-momo.svg?style=flat-square)](https://packagist.org/packages/akika/laravel-momo)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/akikadigital/laravel-momo/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/akika/laravel-momo/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/akikadigital/laravel-momo/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/akika/laravel-momo/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/akika/laravel-momo.svg?style=flat-square)](https://packagist.org/packages/akika/laravel-momo)

An unofficial package for MTN MoMo.

## Installation

You can install the package via composer:

```bash
composer require akika/laravel-momo
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-momo-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-momo-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="laravel-momo-views"
```

## Usage

```php
$moMo = new Akika\MoMo();
echo $moMo->echoPhrase('Hello, Akika!');
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

- [Akika Digital](https://github.com/akika)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
