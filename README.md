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
    - NotFound - 404 when the resource requested is not found
    - Payload  - Check the posted JSON string if it is correct
    - Authentication - After a `/login` checks the `Authentication` header
    - TokenUser      - When a token is supplied, check if it corresponds to a user in the database
    - TokenVerification - When a token is supplied, check if it is correctly signed
    - TokenValidation   - When a token is supplied, check if it is valid (`issuedAt`, `notBefore`, `expires`) 

### Usage

#### Requests
All requests to the API have be submitted using `POST`. All requests must send a JSON string with one root element `data`. Data needed for the request must be under the `data` element 

The endpoints are:

`/login`

| Method | Payload                                                |
|--------|--------------------------------------------------------|
| `POST` | `{"data": {"username": "niden", "password": "12345"}}` |

`/user/get`

| Method | Payload                                             |
|--------|-----------------------------------------------------|
| `POST` | `{"data": {"userId": 1}}` | `["token": "ab.cd.ef"]` |
                                                                                
`/usesr/get`

| Method | Payload |
|--------|---------|
| `POST` | Empty   |
                                                                                
#### Responses
##### Structure
```json
{
  "jsonapi": {
    "version": "1.0"  // Version of the API
  },
  "data": [
                      // Payload returned
  ],
  "errors": {
    "code": 2000,     // 2000 success; 3000 error
    "detail": "Error description"
  },
  "meta": {
    "timestamp": "2018-06-08T15:04:34+00:00",           // Timestamp of the response
    "hash": "e6d4d57162ae0f220c8649ae50a2b79fd1cb2c60"  // Hash of the timestamp and payload
  }
}
```
##### 404
```json
{
  "jsonapi": {
    "version": "1.0"
  },
  "data": [],
  "errors": {
    "code": 3000,
    "detail": "404 Not Found"
  },
  "meta": {
    "timestamp": "2018-06-08T15:04:34+00:00",
    "hash": "e6d4d57162ae0f220c8649ae50a2b79fd1cb2c60"
  }
}
```

##### Error
```json
{
  "jsonapi": {
    "version": "1.0"
  },
  "data": [],
  "errors": {
    "code": 3000,
    "detail": "Description of the error"
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
    // Data returned
  ],
  "errors": {
    "code": 2000,
    "detail": ""
  },
  "meta": {
    "timestamp": "2018-06-08T15:04:34+00:00",
    "hash": "e6d4d57162ae0f220c8649ae50a2b79fd1cb2c60"
  }
}
```
                                                     
`/login`
```json
{
  "jsonapi": {
    "version": "1.0"
  },
  "data": {
    "token": "ab.cd.ef"
  },
  "errors": {
    "code": 2000,
    "detail": ""
  },
  "meta": {
    "timestamp": "2018-06-08T15:04:34+00:00",
    "hash": "e6d4d57162ae0f220c8649ae50a2b79fd1cb2c60"
  }
}
```

`/user/get`
```json
{
  "jsonapi": {
    "version": "1.0"
  },
  "data": [
    {
      "id": 1244,
      "status": 1,
      "username": "phalcon",
      "domainName": "https:\/\/phalconphp.com",
      "tokenPassword": "00001111",
      "tokenId": "99009900"
    }
  ],
  "errors": {
    "code": 2000,
    "detail": ""
  },
  "meta": {
    "timestamp": "2018-06-08T17:05:14+00:00",
    "hash": "344d9766003e14409ab08df863d37d1ef44e5b60"
  }
}
```
`/users/get`
```json
{
  "jsonapi": {
    "version": "1.0"
  },
  "data": [
    {
      "id": 1051,
      "status": 1,
      "username": "niden",
      "domainName": "https:\/\/niden.net",
      "tokenPassword": "11110000",
      "tokenId": "11001100"
    },
    {
      "id": 1244,
      "status": 1,
      "username": "phalcon",
      "domainName": "https:\/\/phalconphp.com",
      "tokenPassword": "00001111",
      "tokenId": "99009900"
    }
  ],
  "errors": {
    "code": 2000,
    "detail": ""
  },
  "meta": {
    "timestamp": "2018-06-08T15:07:35+00:00",
    "hash": "6219ae83afaebc08da4250c4fd23ea1b4843d"
  }
}
```
                                                     
### TODO
- Remove `/login` endpoint. Leave the generation of the JWT to the consumer
- Add max allowed token life for each consumer


