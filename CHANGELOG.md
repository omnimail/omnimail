# CHANGELOG

## 0.4.0 (2021-06-19)
 - Fix psr-4 issue in Common\Responses\Mailing
 - PHPMailer SMTPDebug should be disabled by default
 - Code re-factoring and re-organized
 - CI Improvement & Automation
 - scrutinizer, travis and styleCI removed
 - PHP Version bumped from 5.5 to 5.6

## 0.3.5 / 0.3.5.1 (2020-08-17)
 - Fixed AmazonSES error

## 0.3.4 (2020-05-03)
 - Attachment support added in SMTP provider

## 0.3.3 (2019-10-12)
 - SMTP & Gmail provider added

## 0.3.2 (2018-09-28)
 - Fix Mandrill class's subject and response problems

## 0.3.1 (2018-01-05)

 - Fixed issue with SendinBlue
 - Updated Sendgrid mailer

## 0.3.0 (2017-05-15)

 - Added factory methods to create mailer
 - Renamed EmailSenderInterface to MailerInterface 
 - Added setters and getters for Mailers configs

## 0.2.0 (2017-02-06)

- Added flag to disable verify peer and verify host to Amazon SES constructor
- Fixed bug with Mailjet provider

## 0.1.4 (2016-07-26)

- Added toArray to attachment and email classes and interfaces
- Fixed issue with PSR logger and emails not being arrays

## 0.1.3 (2016-07-26)

- Started making unit tests
- Made library independent from Guzzle
- Uses php7 random_bytes instead of openssl_random_pseudo_bytes with polyfill for PHP 5.x

## 0.1.2 (2016-07-07)

- Added support for inline attachments

## 0.1.1 (2016-07-06)

- Fixed bugs with Sendgrid sender

## 0.1.0 (2016-07-06)

- Initial release
