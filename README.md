# phalcon-api
Sample API using Phalcon

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/niden/phalcon-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/niden/phalcon-api/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/niden/phalcon-api/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/niden/phalcon-api/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/niden/phalcon-api/badges/build.png?b=master)](https://scrutinizer-ci.com/g/niden/phalcon-api/build-status/master)


Implementation of an API application using the Phalcon Framework (https://phalconphp.com)

### Installation
- Clone the project
- In the project folder run `nanobox run php-server`
- Hit the IP address with postman

This requires [nanobox](https://nanobox.io) to be present in your system. Visit their site for installation instructions.

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

### Usage

#### Requests
The routes available are:

| Method | Route             | Payload                                                                                                     |
|--------|-------------------|-------------------------------------------------------------------------------------------------------------|
| `POST` | `login`           | `{"username": string, "password": string}`                                                                  |
| `POST` | `companies`       | `{"name": string, "address": <string>, "city": <string>, "phone": <string>`                                 |
| `GET`  | `individualtypes` | `/<typeId>` If no `id` passed, all records returned. If yes, then the record matching that `id` is returned |
| `GET`  | `producttypes`    | `/<typeId>` If no `id` passed, all records returned. If yes, then the record matching that `id` is returned |
| `GET`  | `users`           | `/<userId>` If no `id` passed, all records returned. If yes, then the record matching that `id` is returned |


#### Responses
##### Structure
**Top Elements**
- `jsonapi` Contains the `version` of the API as a sub element
- `data` Data returned. Is not present if the `errors` is present
- `errors` Collection of errors that occurred in this request. Is not present if the `data` is present
- `meta` Contains `timestamp` and `hash` of the `json_encode($data)` or `json_encode($errors)` 

After a `GET` the API will always return a collection of records, even if there is only one returned. If no data is found, an empty resultset will be returned.

Each endpoint returns records that follow this structure:
```json
{
  "id": 1051,
  "type": "users",
  "attributes": {
    "status": 1,
    "username": "niden",
    "issuer": "https:\/\/niden.net",
    "tokenPassword": "11110000",
    "tokenId": "11001100"
  }
}
```

The record always has `id` and `type` present at the top level. `id` is the unique id of the record in the database. `type` is a string representation of what the object is. In the above example it is a `users` record. Additional data from each record are under the `attributes` node.

#### Samples
**404**
```json
{
  "jsonapi": {
    "version": "1.0"
  },
  "errors": {
    "404 not found"
  },
  "meta": {
    "timestamp": "2018-06-08T15:04:34+00:00",
    "hash": "e6d4d57162ae0f220c8649ae50a2b79fd1cb2c60"
  }
}
```

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

##### Success
```json
{
  "jsonapi": {
    "version": "1.0"
  },
  "data": [
  ],
  "meta": {
    "timestamp": "2018-06-08T15:04:34+00:00",
    "hash": "e6d4d57162ae0f220c8649ae50a2b79fd1cb2c60"
  }
}
```

`POST /login`
```
"username" => "niden"
"password" => "110011"
```

```json
{
  "jsonapi": {
    "version": "1.0"
  },
  "data": {
    "token": "aa.bb.cc"
  ],
  "meta": {
    "timestamp": "2018-06-08T15:07:35+00:00",
    "hash": "6219ae83afaebc08da4250c4fd23ea1b4843d"
  }
}
```
                                                     
`GET /users/get/1051`
```json
{
  "jsonapi": {
    "version": "1.0"
  },
  "data": [
    {
      "id": 1051,
      "type": "users",
      "attributes": {
        "status": 1,
        "username": "niden",
        "issuer": "https:\/\/niden.net",
        "tokenPassword": "11110000",
        "tokenId": "11001100"
      }
    }
  ],
  "meta": {
    "timestamp": "2018-06-08T15:07:35+00:00",
    "hash": "6219ae83afaebc08da4250c4fd23ea1b4843d"
  }
}
```
                                                     
`GET /users/get`
```json
{
  "jsonapi": {
    "version": "1.0"
  },
  "data": [
    {
      "id": 1051,
      "type": "users",
      "attributes": {
        "status": 1,
        "username": "niden",
        "issuer": "https:\/\/niden.net",
        "tokenPassword": "11110000",
        "tokenId": "11001100"
      }
    },
    {
      "id": 1244,
      "type": "users",
      "attributes": {
        "status": 1,
        "username": "phalcon",
        "issuer": "https:\/\/phalconphp.com",
        "tokenPassword": "00001111",
        "tokenId": "99009900"
      }
    }
  ],
  "meta": {
    "timestamp": "2018-06-08T15:07:35+00:00",
    "hash": "6219ae83afaebc08da4250c4fd23ea1b4843d"
  }
}
```
                                                     
### TODO
- Work on companies `GET`
- Work on relationships and data returned
- Write examples of code to send to the client
- Work on pagination
- Perhaps add a new claim to the token tied to the device? `setClaim('deviceId', 'Web-Server')`. This will allow the client application to invalidate access to a device that has already been logged in.
