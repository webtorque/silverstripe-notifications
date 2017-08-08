# silverstripe-notifications
A SilverStripe module for handling notifications to user. This can be use to send notifications via various mediums to a
Member.

Various notification are handled via a NotificationProvider. e.g.: `NotificationEmailProvider` will send email
notifications while `NotificationDataObjectProvider` will store notifications in a DataObject for display one the
website frontend.

[![Build Status DEV](https://travis-ci.org/webtorque/silverstripe-notifications.svg?branch=dev)](https://travis-ci.org/webtorque/silverstripe-notifications)

## Requirements
* PHP 5.5 or greater (tested with up to PHP 7.1)
* `silverstripe/framework:^3.2`
* `silverstripe/cms:^3.2`

### Optional
* `giggsey/libphonenumber-for-php:^8.0`

Installing `libphonenumber-for-php` provides better mobile phone number validation.

## Installation
```bash
composer require webtorque/silverstripe-notifications:^0.0
```

## Configuration
Create a YAML config file to configure the `NotificationService`. You need to speciify a notification parser and a list
of notification providers.

```YAML
NotificationService:
  constructor:
    0: '%$NotificationParser'
    1:
      - "%$NotificationEmailProvider"
      - "%$NotificationDataObjectProvider"
```

The built-in `NotificationParser` will read notification format information from a `NotificationType` data objects.
Notification types can be edited in the CMS. The expectation is that notification types will be predefined in your YML
config. Creation or deletion of notification type are disallowed for all users.

```YAML
NotificationType:
  default_records:
    - SystemName: 'RegistrationApproval'
      Name: 'Registration Approval'

    - SystemName: 'RegistrationApproved'
      Name: 'Registration Approved'

    - SystemName: 'NewPatientRequest'
      Name: 'New Patient Request'

    - SystemName: 'PatientRequestApproved'
      Name: 'Patient Request Approved'

    - SystemName: 'PatientRequestDeclined'
      Name: 'Patient Request Declined'
```

## Usage

```php
$service = Injector::inst()->get('NotificationService');
$deliveries = $service->send(
    'RegistrationApproval',                         # Notification type system name.
    ['extra' => 'Data to inject in the message'],   # Abritary data to inject in the NotificationParser.
    Member::currentUser(),                          # User who should receive the notification.
    '/notification/call-to-action-url'              # Optional Call-to-Action URL.
);

# List of NotificationFailureException for providers who failed to deliver the notification.
$deliveries->getFailures();   

# List of response from providers delivered their notification as expected.
$deliveries->getDeliveries();

```
