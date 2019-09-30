# phalcon-api
Sample API using Phalcon

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phalcon/phalcon-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phalcon/phalcon-api/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/phalcon/phalcon-api/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/phalcon/phalcon-api/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/phalcon/phalcon-api/badges/build.png?b=master)](https://scrutinizer-ci.com/g/phalcon/phalcon-api/build-status/master)


Implementation of an API application using the Phalcon Framework [https://phalcon.io](https://phalcon.io)

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

`/companies/<number>?included=<individuals>,<products>`

`individuals/<number>?included=<companies>,<individual-types>`

`individual-types/<number>?included=<individuals>`

`products/<number>?included=<companies>,<product-types>`

`product-types/<number>?included=<products>`                                             
                                             
#### Fields

`/companies?fields[<relationship>]=<field>,<field>&fields[<relationship>]=<field>,<field>`

#### Sorting

`/companies?sort=<[-]id>,<[-]status>,<[-]username>,<[-]issuer>`

`individuals?sort=<[-]id',<[-]companyId>,<[-]typeId>,<[-]prefix>,<[-]first>,<[-]middle>,<[-]last>,<[-]suffix'>,`

`individual-types?sort=<[-]id>,<[-]name>`

`products?sort=<[-]id',<[-]typeId>,<[-]name>,<[-]quantity>,<[-]price>`

`product-types?sort=<[-]id>,<[-]name>`                                             

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

`/products/1134?includes=companies,product-types`

```json
{
  "jsonapi": {
    "version": "1.0"
  },
  "data": [
    {
      "type": "products",
      "id": "1134",
      "attributes": {
        "typeId": 890,
        "name": "prd-a-5b64af7e70741",
        "description": "5b64af7e7074a",
        "quantity": 25,
        "price": "19.99"
      },
      "links": {
        "self": "http:\/\/api.phalcon.ld\/products\/1134"
      },
      "relationships": {
        "companies": {
          "links": {
            "self": "http:\/\/api.phalcon.ld\/products\/1134\/relationships\/companies",
            "related": "http:\/\/api.phalcon.ld\/products\/1134\/companies"
          },
          "data": [
            {
              "type": "companies",
              "id": "1430"
            },
            {
              "type": "companies",
              "id": "1431"
            }
          ]
        },
        "product-types": {
          "links": {
            "self": "http:\/\/api.phalcon.ld\/products\/1134\/relationships\/product-types",
            "related": "http:\/\/api.phalcon.ld\/products\/1134\/product-types"
          },
          "data": {
            "type": "product-types",
            "id": "890"
          }
        }
      }
    }
  ],
  "included": [
    {
      "type": "companies",
      "id": "1430",
      "attributes": {
        "name": "com-a5b64af7e6c846",
        "address": "5b64af7e6c84f",
        "city": "5b64af7e6c855",
        "phone": "5b64af7e6c85c"
      },
      "links": {
        "self": "http:\/\/api.phalcon.ld\/companies\/1430"
      }
    },
    {
      "type": "companies",
      "id": "1431",
      "attributes": {
        "name": "com-b5b64af7e6e3d3",
        "address": "5b64af7e6e3dc",
        "city": "5b64af7e6e3e2",
        "phone": "5b64af7e6e3e9"
      },
      "links": {
        "self": "http:\/\/api.phalcon.ld\/companies\/1431"
      }
    },
    {
      "type": "product-types",
      "id": "890",
      "attributes": {
        "name": "prt-a-5b64af7e6f638",
        "description": "5b64af7e6f641"
      },
      "links": {
        "self": "http:\/\/api.phalcon.ld\/product-types\/890"
      }
    }
  ],
  "meta": {
    "timestamp": "2018-08-03T19:39:42+00:00",
    "hash": "384c6b3772727b1a9532865d2ae2d51c095c0fd9"
  }
}
```

For more information regarding responses, please check [JSON API](https://jsonapi.org)
                                                    
### TODO
- ~~Work on companies GET~~
- ~~Work on included data~~
- ~~Work on sorting~~
- Write examples of code to send to the client
- Create docs endpoint
- Work on relationships
- Work on pagination
- Work on filters
- Sorting on related resources
- Perhaps add a new claim to the token tied to the device? `setClaim('deviceId', 'Web-Server')`. This will allow the client application to invalidate access to a device that has already been logged in.

## Sponsors

Become a sponsor and get your logo on our README on Github with a link to your site. [[Become a sponsor](https://opencollective.com/phalcon#sponsor)]

<a href="https://opencollective.com/phalcon/#contributors">
<img src="https://opencollective.com/phalcon/tiers/sponsors.svg?avatarHeight=48&width=800">
</a>

## Backers

Support us with a monthly donation and help us continue our activities. [[Become a backer](https://opencollective.com/phalcon#backer)]

<a href="https://opencollective.com/phalcon/#contributors">
<img src="https://opencollective.com/phalcon/tiers/backers.svg?avatarHeight=48&width=800&height=200">
</a>

