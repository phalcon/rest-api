# phalcon-api
Baka API using Phalcon

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bakaphp/phalcon-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phalcon/phalcon-api/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/bakaphp/phalcon-api/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/phalcon/phalcon-api/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/bakaphp/phalcon-api/badges/build.png?b=master)](https://scrutinizer-ci.com/g/phalcon/phalcon-api/build-status/master)


Implementation of an API application using the Phalcon Framework [https://phalconphp.com](https://phalconphp.com)

### Installation
- Clone the project
- In the project folder run `nanobox run` , this will start nanobox and leave you inside the console
- In the project folder run `nanobox run start-nginx`  , will start nginx 
- In the project folder run `nanobox run start-php` , will start php-fpm
- Now to add your local address inside the project folder run `nanobox dns add local bakaapi.local`
- To view the information mysql , redis a dn other information needed to configure your .env variables run insde the project folder `nanobox info local`
- Inside the nanobox console run  `./vendor/bin/phinx migrate -e production` to create the db , you need to have the phinx.php file , if you dont see it on your main filder you can find the copy at `storage/ci/phinx.php`
- If you need to update a migration run `./vendor/bin/phinx-migrations  generate` , inside the nanobox console

**NOTE** This requires [nanobox](https://nanobox.io) to be present in your system. Visit their site for installation instructions.

### CLI
- On every deploy crear the session caches `php cli/cli.php clearcache` 
- On every deploy update your DB `./vendor/bin/phinx migrate -e production`
- Queue to clear jwt sessions `php cli/cli.php clearcache sessions`

### Features
- User Managament
  - Registration , Login, Multi Tenant 
- ACL *working on it
- Saas Configuracion *working on it
 - Company Configuration
 - Payment / Free trial flow
- Rapid API CRUD Creation

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
  "errors": {
    "Description of the error no 1",
    "Description of the error no 2"
  },
}
```

                                                  
### TODO
- Create docs endpoint
- Migrate Testing to Baka
