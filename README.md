# Zoho Provider for OAuth 2.0 Client

[![Latest Version](https://img.shields.io/github/release/shahariaazam/zoho-oauth2.svg?style=flat-square)](https://github.com/shahariaazam/zoho-oauth2/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/shahariaazam/zoho-oauth2/master.svg?style=flat-square)](https://travis-ci.org/shahariaazam/zoho-oauth2)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/shahariaazam/zoho-oauth2.svg?style=flat-square)](https://scrutinizer-ci.com/g/shahariaazam/zoho-oauth2/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/shahariaazam/zoho-oauth2.svg?style=flat-square)](https://scrutinizer-ci.com/g/shahariaazam/zoho-oauth2)
[![Total Downloads](https://img.shields.io/packagist/dt/shahariaazam/zoho-oauth2.svg?style=flat-square)](https://packagist.org/packages/shahariaazam/zoho-oauth2)

This package provides Zoho OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```
composer require shahariaazam/zoho-oauth2
```

## Usage

Usage is the same as The League's OAuth client, using `\ShahariaAzam\ZohoOAuth2\Client\Provider\Zoho` as the provider.

### Authorization Code Flow

```php
$provider = new ShahariaAzam\ZohoOAuth2\Client\Provider\Zoho([
    'clientId'          => '{zoho-client-id}',
    'clientSecret'      => '{zoho-client-secret}',
    'redirectUri'       => 'https://example.com/callback-url'
]);
```
For further usage of this package please refer to the [core package documentation on "Authorization Code Grant"](https://github.com/thephpleague/oauth2-client#usage).

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/shahariaazam/zoho-oauth2/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Steven Maguire](https://github.com/stevenmaguire)
- [All Contributors](https://github.com/shahariaazam/zoho-oauth2/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/shahariaazam/zoho-oauth2/blob/master/LICENSE) for more information.
