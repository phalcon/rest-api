# Installation

## Quick start (Docker)

Everything runs in Docker — you need **nothing** on your host except Docker itself
(no PHP, no extensions, no database). From the project root:

```bash
cp resources/.env.example .env
docker compose up -d --build

# Install dependencies and create the schema (migrations are not run on boot)
docker compose exec app composer install
docker compose exec app composer migrate
```

The API is served at <http://localhost:8080>. There are no bundled fixtures, so every
collection starts empty — see [usage.md](usage.md) for the request/response format and
the authentication flow.

The stack is four services: `app` (PHP-FPM), `nginx` (the web server, published on the
host port), `mysql`, and `redis` (the data and metadata cache). The project directory is
mounted at `/srv` inside the container, which masks the `vendor/` baked into the image —
hence the `composer install` step. It only needs repeating when `composer.lock` changes.

To run on a specific PHP version (default is set in `.env`, supported `8.1`+):

```bash
PHP_VERSION=8.1 docker compose up -d --build   # or 8.2 / 8.3 / 8.4
```

### Choosing the Phalcon version

```bash
docker compose up -d --build                      # v5 (C extension, default)
PHALCON_VARIANT=v6 docker compose up -d --build   # v6 (phalcon/phalcon)
```

The two are mutually exclusive: the v5 image installs the C extension, the v6 image
installs the pure-PHP `phalcon/phalcon` package instead. When the extension is present,
PHP prefers it and the package is simply shadowed.

### Running several versions side by side

The container keeps the same name (`rest-api-app`), so each rebuild **replaces** the
previous one. To run several PHP or Phalcon versions at once, give each its own Compose
project, prefix, and host port:

```bash
PHP_VERSION=8.1 PROJECT_PREFIX=restapi81 APP_PORT=8081 \
  docker compose -p restapi81 up -d --build
# then: docker exec restapi81-app composer migrate
```

## Local (non-Docker) setup

Prefer to run the API directly on your host? Follow the steps below instead.

### Requirements

* PHP 8.1+ with the extensions: `openssl`, `mbstring`, `json`, `pdo_mysql`, `redis`
* MySQL 8.0 and Redis
* [Composer](https://getcomposer.org/)

### 1. Install the Phalcon extension (v5)

Use [PIE](https://github.com/php/pie), the official PHP extension installer. Unlike
pecl, it builds the extension from source and supports current PHP versions:

```bash
curl -fsSL https://github.com/php/pie/releases/latest/download/pie.phar -o pie.phar
sudo php pie.phar install phalcon/cphalcon:^5.0
php -m | grep -i phalcon
```

> To run on **v6** instead, skip this step — `composer install` pulls the
> `phalcon/phalcon` package, which the application uses when the extension is absent.

### 2. Install dependencies

```bash
composer install
```

### 3. Configure the environment

```bash
cp resources/.env.example .env
```

Edit `.env` and point the database and cache at your host (the Docker defaults use the
`mysql` and `redis` service names):

```dotenv
DATA_API_MYSQL_HOST=127.0.0.1
DATA_API_REDIS_HOST=127.0.0.1
```

### 4. Create the database

```bash
composer migrate
```

There are no seeds bundled, so the schema is created empty.

### 5. Serve the application

```bash
php -S localhost:8080 -t public .htrouter.php
```

The API is now at <http://localhost:8080>.

## Quality and tests

```bash
composer cs            # coding standard (PSR-12)
composer analyze       # static analysis (run without the v5 extension loaded)
composer cs-fixer      # PHP CS Fixer (dry-run)
composer test          # the default (unit) PHPUnit suite
vendor/bin/talon run all   # every suite: unit, integration, api, cli
composer test-coverage # PHPUnit + Clover coverage (tests/_output/coverage.xml)
```

The `api` suite drives the running application over real HTTP, so the web server must be
up and reachable at the URL in `tests/.env.test` (`TALON_REST_URL`, `http://nginx` in the
Docker stack). Mutation testing is covered in the [README](../README.md#mutation-testing).
