# phalcon-api
Sample API using Phalcon

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phalcon/phalcon-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phalcon/phalcon-api/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/phalcon/phalcon-api/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/phalcon/phalcon-api/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/phalcon/phalcon-api/badges/build.png?b=master)](https://scrutinizer-ci.com/g/phalcon/phalcon-api/build-status/master)


Implementation of an API application using the Phalcon Framework [https://phalconphp.com](https://phalconphp.com)

### Installation
- Clone the project
- In the project folder run `nanobox run php-server`
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

##### JSONAPI
This implementation follows the [JSON API](https://jsonapi.org) standard. All responses are formatted according to the standard, which offers a uniformed way of presenting data, simple or compound documents, includes (related data), sparse fieldsets, sorting, patination and filtering.

### Usage

#### Requests
The routes available are:

| Method | Route              | Parameters                         | Action                                                   | 
|--------|--------------------|------------------------------------|----------------------------------------------------------|
| `POST` | `login`            | `username`, `password`             | Login - get Token                                        |
| `POST` | `companies`        | `name`, `address`, `city`, `phone` | Add a company record in the database                     |
| `GET`  | `companies`        |                                    | Get companies. Empty resultset if no data present        |
| `GET`  | `companies`        | Numeric Id                         | Get company by id. 404 if record does not exist          |
| `GET`  | `individuals`      |                                    | Get individuals. Empty resultset if no data present      |
| `GET`  | `individuals`      | Numeric Id                         | Get individual by id. 404 if record does not exist       |
| `GET`  | `individual-types` |                                    | Get individual types. Empty resultset if no data present |
| `GET`  | `individual-types` | Numeric Id                         | Get individual type by id. 404 if record does not exist  |
| `GET`  | `products`         |                                    | Get products. Empty resultset if no data present         |
| `GET`  | `products`         | Numeric Id                         | Get product by id. 404 if record does not exist          |
| `GET`  | `product-types`    |                                    | Get product types. Empty resultset if no data present    |
| `GET`  | `product-types`    | Numeric Id                         | Get product type by id. 404 if record does not exist     |
| `GET`  | `users`            |                                    | Get users. Empty resultset if no data present            |
| `GET`  | `users`            | Numeric Id                         | Get user by id. 404 if record does not exist             |
                                             
#### Relationships

`/companies/<number>/individuals`
`/companies/<number>/products`
`/companies/<number>/individuals,products`

`/companies/<number>/relationships/individuals`
`/companies/<number>/relationships/products`
`/companies/<number>/relationships/individuals,products`

`individuals/<number>/companies`
`individuals/<number>/individual-types`
`individuals/<number>/companies,individual-types`

`individuals/<number>/relationships/companies`
`individuals/<number>/relationships/individual-types`
`individuals/<number>/relationships/companies,individual-types`

`individual-types/<number>/individuals`
`individual-types/<number>/relationships/individuals`

`products/<number>/companies`
`products/<number>/product-types`
`products/<number>/companies,product-types`

`products/<number>/relationships/companies`
`products/<number>/relationships/product-types`
`products/<number>/relationships/companies,product-types`

`product-types/<number>/products`                                             
`product-types/<number>/relationships/products`                                             
                                             


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

For more information regarding responses, please check [JSON API](https://jsonapi.org)
                                                    
### TODO
- ~~Work on companies GET~~
- ~~Work on relationships and data returned~~
- Write examples of code to send to the client
- Create docs endpoint
- Work on pagination
- Work on filters
- Work on sorting
- Perhaps add a new claim to the token tied to the device? `setClaim('deviceId', 'Web-Server')`. This will allow the client application to invalidate access to a device that has already been logged in.
