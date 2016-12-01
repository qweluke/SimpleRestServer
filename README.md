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

#####How to start:
- clone this repo
- run `php bin/console server:start 0.0.0.0:8000`
- go to [http://localhost:8000/dev/doc](http://localhost:8000/dev/doc) to see documentation


What you can:

1. Authorize

2. User
  - CRUD commands for USER
  - Search for an user

######Please note: this is only a server, you need your client to use it.
