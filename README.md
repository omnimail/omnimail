# ![Omnimail](https://cdn.rawgit.com/gabrielbull/omnimail/master/omnimail-logo.svg "Omnimail")

[![Build Status](https://travis-ci.org/gabrielbull/omnimail.svg?branch=master)](https://travis-ci.org/gabrielbull/omnimail)
[![StyleCI](https://styleci.io/repos/12901491/shield)](https://styleci.io/repos/12901491)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gabrielbull/omnimail/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gabrielbull/omnimail/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/gabrielbull/omnimail/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/gabrielbull/omnimail/?branch=master)
[![Code Climate](https://codeclimate.com/github/gabrielbull/omnimail/badges/gpa.svg)](https://codeclimate.com/github/gabrielbull/omnimail)
[![Latest Stable Version](http://img.shields.io/packagist/v/omnimail/omnimail.svg?style=flat)](https://packagist.org/packages/omnimail/omnimail)
[![Total Downloads](https://img.shields.io/packagist/dt/omnimail/omnimail.svg?style=flat)](https://packagist.org/packages/omnimail/omnimail)
[![License](https://img.shields.io/packagist/l/omnimail/omnimail.svg?style=flat)](https://packagist.org/packages/omnimail/omnimail)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/3d56cb99-e407-42d6-8245-6ad3b94374ce/mini.png)](https://insight.sensiolabs.com/projects/3d56cb99-e407-42d6-8245-6ad3b94374ce)
[![Join the chat at https://gitter.im/gabrielbull/omnimail](https://badges.gitter.im/gabrielbull/omnimail.svg)](https://gitter.im/gabrielbull/omnimail?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Send email across all platforms using one interface.

## Table Of Content

1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Providers](#providers)
  - [AmazonSES](#amazon-ses)
  - [Mailgun](#mailgun)
  - [Mailjet](#mailjet)
  - [Mandrill](#mandrill)
  - [Postmark](#postmark)
  - [Sendgrid](#sendgrid)
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
5. Exceptions
6. Logging
7. [License](#license-section)

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

<a name="mailjet"></a>
### Mailjet

#### Installation

To use the Mailjet sender class, you will need to install the `mailjet/mailjet-apiv3-php` library using composer.

```
composer require mailjet/mailjet-apiv3-php
```

#### Usage

```php
use Omnimail\Email;
use Omnimail\Mailjet;

$sender = new Mailjet($apikey, $apisecret);

$email = (new Email())
    ->addTo('example@email.com')
    ->setFrom('example@email.com')
    ->setSubject('Hello, world!')
    ->setTextBody('Hello World!\n\nHow are you?');

$sender->send($email);
```

<a name="mandrill"></a>
### Mandrill

#### Installation

To use the Mandrill sender class, you will need to install the `mandrill/mandrill` library using composer.

```
composer require mandrill/mandrill
```

#### Usage

```php
use Omnimail\Email;
use Omnimail\Mandrill;

$sender = new Mandrill($apiKey);

$email = (new Email())
    ->addTo('example@email.com')
    ->setFrom('example@email.com')
    ->setSubject('Hello, world!')
    ->setTextBody('Hello World!\n\nHow are you?');

$sender->send($email);
```

<a name="postmark"></a>
### Postmark

#### Installation

To use the Postmark sender class, you will need to install the `wildbit/postmark-php` library using composer.

```
composer require wildbit/postmark-php
```

#### Usage

```php
use Omnimail\Email;
use Omnimail\Postmark;

$sender = new Postmark($serverApiToken);

$email = (new Email())
    ->addTo('example@email.com')
    ->setFrom('example@email.com')
    ->setSubject('Hello, world!')
    ->setTextBody('Hello World!\n\nHow are you?');

$sender->send($email);
```

<a name="sendgrid"></a>
### Sendgrid

#### Installation

To use the Sendgrid sender class, you will need to install the `sendgrid/sendgrid` library using composer.

```
composer require sendgrid/sendgrid
```

#### Usage

```php
use Omnimail\Email;
use Omnimail\Sendgrid;

$sender = new Sendgrid($apiKey);

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
