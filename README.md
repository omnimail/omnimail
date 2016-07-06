# ![Omnimail](https://cdn.rawgit.com/gabrielbull/omnimail/master/omnimail-logo.svg "Omnimail")

[![Join the chat at https://gitter.im/gabrielbull/omnimail](https://badges.gitter.im/gabrielbull/omnimail.svg)](https://gitter.im/gabrielbull/omnimail?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

[![Build Status](https://travis-ci.org/gabrielbull/omnimail.svg?branch=master)](https://travis-ci.org/gabrielbull/omnimail)
[![StyleCI](https://styleci.io/repos/12901491/shield)](https://styleci.io/repos/12901491)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gabrielbull/omnimail/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gabrielbull/omnimail/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/gabrielbull/omnimail/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/gabrielbull/omnimail/?branch=master)
[![Code Climate](https://codeclimate.com/github/gabrielbull/omnimail/badges/gpa.svg)](https://codeclimate.com/github/gabrielbull/omnimail)
[![Latest Stable Version](http://img.shields.io/packagist/v/omnimail/omnimail.svg?style=flat)](https://packagist.org/packages/omnimail/omnimail)
[![Total Downloads](https://img.shields.io/packagist/dt/omnimail/omnimail.svg?style=flat)](https://packagist.org/packages/omnimail/omnimail)
[![License](https://img.shields.io/packagist/l/omnimail/omnimail.svg?style=flat)](https://packagist.org/packages/omnimail/omnimail)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4a9cc9ed-22da-4f03-aa18-9c0c2430c7b6/mini.png)](https://insight.sensiolabs.com/projects/4a9cc9ed-22da-4f03-aa18-9c0c2430c7b6)
[![Join the chat at https://gitter.im/omnimail/omnimail](https://badges.gitter.im/omnimail/omnimail.svg)](https://gitter.im/omnimail/omnimail?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Send email across all platforms using one interface.

## Table Of Content

1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Providers](#providers)
  - [AmazonSES](#amazon-ses)
  - [Mailgun](#mailgun)

4. Email
  - To
  - From
  - CC
  - BCC
  - Reply to
  - Subject
  - Text Body
  - HTML Body
  - Attachements
  
3. [License](#license-section)

<a name="requirements"></a>
## Requirements

This library uses PHP 5.5+.

<a name="installation"></a>
## Installation

It is recommended that you install the Omnimail library [through composer](http://getcomposer.org/). To do so,
run the Composer command to install the latest stable version of Omnimail library.

```shell
composer require omnimail/omnimail
```

<a name="providers"></a>
## Providers

<a name="amazon-ses"></a>
### AmazonSES

#### Installation

To use the AmazonSES sender class, you will need to install the `daniel-zahariev/php-aws-ses` library using composer.

```
composer require daniel-zahariev/php-aws-ses
```

#### Usage

```php
use Omnimail\Email;
use Omnimail\AmazonSES;

$sender = new AmazonSES($accessKey, $secretKey);

$email = (new Email())
    ->addTo('example@email.com')
    ->setFrom('example@email.com')
    ->setSubject('Hello, world!')
    ->setTextBody('Hello World!\n\nHow are you?');

$sender->send($email);
```

<a name="mailgun"></a>
### Mailgun

#### Installation

To use the Mailgun sender class, you will need to install the `mailgun/mailgun-php` library using composer.

```
composer require mailgun/mailgun-php
```

#### Usage

```php
use Omnimail\Email;
use Omnimail\Mailgun;

$sender = new Mailgun($apiKey, $domain);

$email = (new Email())
    ->addTo('example@email.com')
    ->setFrom('example@email.com')
    ->setSubject('Hello, world!')
    ->setTextBody('Hello World!\n\nHow are you?');

$sender->send($email);
```

<a name="license-section"></a>
## License

Omnimail is licensed under [The MIT License (MIT)](LICENSE).
