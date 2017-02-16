REST server
======

####Simple REST server

#####This example uses:
- [Symfony3](http://symfony.com/what-is-symfony)
- [FOSUserBundle](http://symfony.com/doc/current/bundles/FOSUserBundle/index.html)
- [FOSRESTBundle](http://symfony.com/doc/current/bundles/FOSRestBundle/index.html)
- [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle)
- [NelmioApiDocBundle](http://symfony.com/doc/current/bundles/NelmioApiDocBundle/index.html)
- [JMSSerializerBundle](http://jmsyst.com/bundles/JMSSerializerBundle)

#####Requirements:
- PHP 7
- MySQL 5.7 or MariaDB

#####How to start:
- clone this repo
- run `composer install` to install necessary dependencies
- run `php bin/console server:start 0.0.0.0:8000`
- go to [http://localhost:8000/dev/doc](http://localhost:8000/dev/doc) to see documentation

If you are having any permissions problems please follow [this](http://symfony.com/doc/current/setup/file_permissions.html).

What you can:

1. Authorize

2. User
  - CRUD commands for USER
  - Search for an user
  - CRUD commands for company contacts
  - search for an company contact
  - CRUD commands for company
  - search for a company

######Please note: this is only a server, you need your client to use it.
