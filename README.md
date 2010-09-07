# sfRestWebServicePlugins

The `sfRestWebServicePlugin` offers an easy interface for REST API based on your domain model.

## Installation and configuration

### Installation

Use the default plugin installer procedure

    php symfony plugin:install sfRestWebServicePlugin

then enable the plugin in your projectConfiguration class ( remember it needs the `sfDoctrinePlugin` enabled too, cause the services are based on your Doctrine schema ):

    public function setup()
    {
      $this->enablePlugins('sfDoctrinePlugin');
      $this->enablePlugins('sfRestWebServicePlugin');
    }

Last step, enable the module in the settings.yml of the application you want the webservices to be exposed into:

    all:
      ...
      enabled_modules:        [sfRestWebService]
      ...

### Configuration

You can - obviously - override and extends plugin's classes by creating them in your application's module directory.

The `sfRestWebServicePlugin` is based on the `sfRestWebService` module bundled with the plugin, so you only need to replicate the module on your application:

    $ mkdir apps/myApp/modules/sfRestWebService

For example, to override a template you will only need to create it on your application at the path:

`apps/myApp/modules/sfRestWebService/templates/errorSuccess.json.php`

The **core** configuration on the module lies in the config.yml that you have to locally override:

    $ touch apps/myApp/modules/sfRestWebService/config/config.yml

    all:
      protected: true
      allowed: [127.0.0.1]
      protectedRoute: secure
      services:
        name:
          model:  user
          methodForQuery: findActives
          states: [GET, PUT]

Here's a brief explanation for every configuration parameter:

    all:  The environment

    protected:  boolean, the webservices are protected or not?

    allowed:  if the webservices are protected, specify a YAML array of IP addresses that can access the services

    protectedRoute:  sets the route that non-allowed IP addresses will be redirected to

    services:  an array of single services configurations

    name:  the service name ( used in the service URL )

    model:  the model of the service

    methodForQuery:  a method for GET requests. If not specified, doctrine will do a `->createQuery()->execute()`

    states:  allowed request states ( PUT, POST, GET, DELETE ). If not specified, all state are allowed

If you turn on authentication, **remember to specify a secure route**.
If you have module `default` enabled, the route can be `secure` ( which is the name of the `default/secure` route ).

## Requirements

This plugins requires PHP's `short open tags` parameter set to `OFF`.
It would not be such a difficult matter to make the plugin work also with `short open tags` enabled, the point is that you shouldn't work this way.

## A specification

Since **PHP** sucks in so many ways handling PUT requests this plugin handles them with symfony's native REST architecture ( so, not not real PUT requests, but requests with the additional parametere `sf_method` set to PUT ).

## URLs

Suppose a configuration like:

    all:
      protected: true
      allowed: [127.0.0.1]
      protectedRoute: secure
      services:
        users:
          model:  User
          methodForQuery: ~
          states: ~

The URLs that the `sfRestWebService` module will match are:

  * http://domain.tld/app.php/api/user ( known as **entry** )
  * http://domain.tld/app.php/api/user/1 ( known as **resource** )
  * http://domain.tld/app.php/api/user/search/name/fabien ( known as **search** )

From now on, we will refer to _ask an entry_, or _ask a search_ and so on.


## Asking the services

Here are just a few examples on how to query an imaginary service with CURL.

### Ask an entry

    GET

    $ curl -X GET http://domain.tld/index.php/api/user

    POST

    $ curl -X GET http://domain.tld/index.php/api/user -F name='John Doe' -F email='john@sf.com'

### Ask a resource

    GET

    $ curl -X GET http://domain.tld/index.php/api/user/1

    DELETE

    $ curl -X DELETE http://domain.tld/index.php/api/user/1

    PUT

    $ curl -X POST http://domain.tld/index.php/api/user/1 -F sf_method=PUT -F name='John C.Hanged'

### Ask a search

    GET

    $ curl -X GET http://domain.tld/index.php/api/user/search/email/gmail

## Responses

### Entry

    http://domain.tld/app.php/api/user

#### GET

Returns a collection of objects:

    <?xml version="1.0" encoding="utf-8"?>
    <objects>
      <object id="1">
        <id>1</id>
        <name>John Doe</name>
      </object>
      <object id="2">
        <id>2</id>
        <name>Mark Madsen</name>
      </object>
    </objects>

an error if the service is available but the configuration is malformed:

    <error>
      Internal server error: unsupported service
    </error>

or a 404 status code if the service doesn't exists.

#### POST

Returns the just created object:

    <object id="7">
        <id>7</id>
        <name>Alessandro Nadalin</name>
    </object>

an error if the data passed via POST doesn't pass validation:

    <error>
      Validation failed in class User

      1 field had validation error:

        * 1 validator failed on name (notnull)
    </error>

an error if the service is available but the configuration is malformed:

    <error>
      Internal server error: unsupported service
    </error>

or a 404 status code if the service doesn't exists.

#### DELETE

Not supported.

#### PUT

Not supported.

### Resource

    http://domain.tld/app.php/api/user/:id

#### GET

Returns the requested resource by ID:

    <object id="7">
        <id>7</id>
        <name>Alessandro Nadalin</name>
    </object>

an error if the resource doesn't exist:

    <error>
      Unable to load the specified resource
    </error>

an error if the service is available but the configuration is malformed:

    <error>
      Internal server error: unsupported service
    </error>

or a 404 status code if the service doesn't exists.

#### POST

Not supported.

#### DELETE

Returns a simple feedback:

    <object>
      Object has been deleted
    </object>

an error if the resource you are trying to delete doesn't exist:

    <error>
      Unable to load the specified resource
    </error>

an error if the service is available but the configuration is malformed:

    <error>
      Internal server error: unsupported service
    </error>

or a 404 status code if the service doesn't exists.

#### PUT

Returns the just updated object:

    <object id="7">
        <id>7</id>
        <name>Alessandro Nadalin has been updated</name>
    </object>

an error if the resource doesn't exist:

    <error>
      Unable to load the specified resource
    </error>

an error if the service is available but the configuration is malformed:

    <error>
      Internal server error: unsupported service
    </error>

or a 404 status code if the service doesn't exists.

### Search

#### GET

    http://domain.tld/app.php/api/user/search/:column/:value

Returns a collection of objects matching a `where(":column LIKE ?", "%:value%")` statement:

    <objects>
      <object id="2">
        <id>2</id>
        <name>Mark Madsen</name>
      </object>
      <object id="7">
        <id>7</id>
        <name>Alessandro Nadalin</name>
      </object>
    </objects>

an error if the column you are trying to search by doesn't exist:

    <error>
      Invalid search column
    </error>

an error if the service is available but the configuration is malformed:

    <error>
      Internal server error: unsupported service
    </error>

or a 404 status code if the service doesn't exists.

#### POST

Not supported.

#### DELETE

Not supported.

#### PUT

Not supported.

## Formats

The services send responses in:

  * XML ( default format: http://domain.tld/app.php/api/user )
  * JSON ( http://domain.tld/app.php/api/user.json )
  * YAML ( http://domain.tld/app.php/api/user.yaml )

## The `methodForQuery` parameter

If specified, it's used in a case: processing **a GET request on an entry**.

Supposing your service's `methodForQuery` is `findItalian` and the `model` parameter is `user`
you will need to create a new method in the `UserTable` class:

    public function findItalian(Doctrine_Query $query)
    {
      $query = // ...do stuff with the query...

      return $query;
    }

The `$query` that the method receives is always:

    Doctrine::getTable('model')->createQuery('wsmodel');

**NOTE:** do not execute the query.