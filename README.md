# phalcon-api
Sample API using Phalcon

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phalcon/phalcon-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phalcon/phalcon-api/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/phalcon/phalcon-api/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/phalcon/phalcon-api/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/phalcon/phalcon-api/badges/build.png?b=master)](https://scrutinizer-ci.com/g/phalcon/phalcon-api/build-status/master)


Implementation of an API application using the Phalcon Framework [https://phalconphp.com](https://phalconphp.com)

### Installation
- Clone the project
- In the project folder run `nanobox run` && `nanobox run start-nginx` && `nanobox run start-php`
- Hit the IP address with postman

**NOTE** This requires [nanobox](https://nanobox.io) to be present in your system. Visit their site for installation instructions.

### Features
##### JWT Tokens
As part of the security of the API, [JWT](https://jwt.io) are used. JSON Web Tokens offer an easy way for a consumer of the API to send requests without the need to authenticate all the time. The expiry of each token depends on the setup of the API. An admin can easily keep the expiry very short, thus consumers will always have to log in first and then access a resource, or they can increase the "life" of each token, thus having less calls to the API.

##### Middleware
- Lazy loading to save resources per request
- Stop execution as early as possible when an error occurs
- Execution
    - NotFound          - 404 when the resource requested is not found
    - Authentication    - After a `/login` checks the `Authentication` header
    - TokenUser         - When a token is supplied, check if it corresponds to a user in the database
    - TokenVerification - When a token is supplied, check if it is correctly signed
    - TokenValidation   - When a token is supplied, check if it is valid (`issuedAt`, `notBefore`, `expires`) 

##### Baka HTTP
We use the library [Baka HTTP](https://github.com/bakaphp/http) to handle our Routing 

### Usage

#### Requests

**Error**

```json
{
  "jsonapi": {
    "version": "1.0"
  },
  "errors": {
    "Description of the error no 1",
    "Description of the error no 2"
  },
  "meta": {
    "timestamp": "2018-06-08T15:04:34+00:00",
    "hash": "e6d4d57162ae0f220c8649ae50a2b79fd1cb2c60"
  }
}
```

                                                    
### TODO
- Create docs endpoint
- Sorting on related resources