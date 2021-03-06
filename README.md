# Very short description of the package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/social-piranha/inplayer-support.svg?style=flat-square)](https://packagist.org/packages/social-piranha/inplayer-support)
[![Total Downloads](https://img.shields.io/packagist/dt/social-piranha/inplayer-support.svg?style=flat-square)](https://packagist.org/packages/social-piranha/inplayer-support)
![GitHub Actions](https://github.com/joeycoonce/inplayer-support/actions/workflows/main.yml/badge.svg)

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what PSRs you support to avoid any confusion with users and contributors.

## Installation

You can install the package via composer:

```bash
composer require social-piranha/inplayer-support
```

In .env, define InPlayer credentials:

```env
INPLAYER_CLIENT_ID=
INPLAYER_CLIENT_SECRET=
INPLAYER_MERCHANT_UUID=
```

Optionally, define

```env
INPLAYER_MERCHANT_PASSWORD
```

to support permanent deletion of users.

Optionally, define

```env
INPLAYER_ENV=staging
```

to use the staging endpoint

## Usage

```php
// Usage description here
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email joeycoonce@gmail.com instead of using the issue tracker.

## Credits

-   [Joey Coonce](https://github.com/joeycoonce)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
