# Usage

The API follows the [JSON:API](https://jsonapi.org) standard. Every response carries the
same envelope, and reads always return a **collection** - even a single record comes back
as a one-element `data` array. Requests and responses are JSON; send `Content-Type:
application/json`.

There are no bundled fixtures, so a fresh install has no data and no users. Records are
created through the API (`POST /companies`) once you hold a token, and the test suite
(`tests/Api`) demonstrates the full flow end to end.

## Authentication

The API is protected by [JSON Web Tokens](https://jwt.io). Authenticate once at `/login`
and send the returned token as a bearer credential on every subsequent request:

```http
POST /login
Content-Type: application/json

{ "username": "your-user", "password": "your-password" }
```

The response carries a `token`. Send it on protected routes:

```http
GET /companies
Authorization: Bearer <token>
```

Token lifetime, issuer, and audience are configured in `.env` (`TOKEN_*`). A missing,
malformed, expired, or unverifiable token is answered with `Invalid Token`.

## Endpoints

| Method | Route | Parameters | Description |
| --- | --- | --- | --- |
| `POST` | `/login` | `username`, `password` | Authenticate and receive a JWT |
| `POST` | `/companies` | `name`, `address`, `city`, `phone` | Create a company |
| `GET` | `/users` | | List users |
| `GET` | `/users/{id}` | numeric id | A user by id (`404` if absent) |
| `GET` | `/companies` | | List companies |
| `GET` | `/companies/{id}` | numeric id | A company by id (`404` if absent) |
| `GET` | `/individuals` | | List individuals |
| `GET` | `/individuals/{id}` | numeric id | An individual by id (`404` if absent) |
| `GET` | `/individual-types` | | List individual types |
| `GET` | `/individual-types/{id}` | numeric id | An individual type by id (`404` if absent) |
| `GET` | `/products` | | List products |
| `GET` | `/products/{id}` | numeric id | A product by id (`404` if absent) |
| `GET` | `/product-types` | | List product types |
| `GET` | `/product-types/{id}` | numeric id | A product type by id (`404` if absent) |

A `GET` on a collection returns an empty `data` array when nothing matches. Each `GET {id}`
resource also exposes its relationships at `/{resource}/{id}/relationships/{relationship}`.

## Query parameters

### Includes (related data)

Ask for related resources with `includes`, comma-separated. Each included resource is
returned in a top-level `included` array (a compound document):

```
GET /companies/1?includes=individuals,products
GET /individuals/1?includes=companies,individual-types
GET /individual-types/1?includes=individuals
GET /products/1?includes=companies,product-types
GET /product-types/1?includes=products
```

### Sparse fieldsets

Limit the attributes returned per resource type with `fields[type]`. A field outside the
model's published set is silently ignored - the API never exposes a column a model does not
declare public:

```
GET /companies?fields[companies]=name,city
```

### Sorting

Sort with `sort`, comma-separated; prefix a field with `-` for descending order. Only the
fields below are sortable per resource:

| Resource | Sortable fields |
| --- | --- |
| `companies` | `id`, `name`, `address`, `city`, `phone` |
| `individuals` | `id`, `companyId`, `typeId`, `prefix`, `first`, `middle`, `last`, `suffix` |
| `individual-types` | `id`, `name` |
| `products` | `id`, `typeId`, `name`, `quantity`, `price` |
| `product-types` | `id`, `name` |
| `users` | `id`, `status`, `username`, `issuer` |

```
GET /products?sort=-price,name
```

## Response format

Every response is wrapped in this envelope:

* `jsonapi` - the API `version`.
* `data` - the returned records. Absent when `errors` is present.
* `errors` - an array of error messages. Absent when `data` is present.
* `meta` - a `timestamp` and a `hash` (sha1 of the timestamp and the body), for integrity.

Each record carries `id` and `type` at the top level; its columns live under `attributes`,
and its related resources under `relationships`. `id` is the database id, `type` the
resource name (e.g. `users`).

```json
{
  "type": "users",
  "id": "1051",
  "attributes": {
    "status": 1,
    "username": "niden",
    "issuer": "https://niden.net",
    "tokenId": "11001100"
  }
}
```

> `Users` publishes only `status`, `username`, `issuer`, and `tokenId`. The password hash
> and token secret are never sent, even when requested explicitly through a sparse fieldset.

## Samples

### Success (collection)

```json
{
  "jsonapi": { "version": "1.0" },
  "data": [],
  "meta": {
    "timestamp": "2018-06-08T15:04:34+00:00",
    "hash": "e6d4d57162ae0f220c8649ae50a2b79fd1cb2c60"
  }
}
```

### Errors

```json
{
  "jsonapi": { "version": "1.0" },
  "errors": [
    "Description of the error no 1",
    "Description of the error no 2"
  ],
  "meta": {
    "timestamp": "2018-06-08T15:04:34+00:00",
    "hash": "e6d4d57162ae0f220c8649ae50a2b79fd1cb2c60"
  }
}
```

### Compound document (`includes`)

`GET /products/1134?includes=companies,product-types`

```json
{
  "jsonapi": { "version": "1.0" },
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
        "self": "http://localhost:8080/products/1134"
      },
      "relationships": {
        "companies": {
          "links": {
            "self": "http://localhost:8080/products/1134/relationships/companies",
            "related": "http://localhost:8080/products/1134/companies"
          },
          "data": [
            { "type": "companies", "id": "1430" },
            { "type": "companies", "id": "1431" }
          ]
        },
        "product-types": {
          "links": {
            "self": "http://localhost:8080/products/1134/relationships/product-types",
            "related": "http://localhost:8080/products/1134/product-types"
          },
          "data": { "type": "product-types", "id": "890" }
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
      "links": { "self": "http://localhost:8080/companies/1430" }
    },
    {
      "type": "product-types",
      "id": "890",
      "attributes": {
        "name": "prt-a-5b64af7e6f638",
        "description": "5b64af7e6f641"
      },
      "links": { "self": "http://localhost:8080/product-types/890" }
    }
  ],
  "meta": {
    "timestamp": "2018-08-03T19:39:42+00:00",
    "hash": "384c6b3772727b1a9532865d2ae2d51c095c0fd9"
  }
}
```

For the full specification, see [JSON:API](https://jsonapi.org).
