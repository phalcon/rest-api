# Phalcon REST API

[![Latest Version][packagist-version-badge]][packagist-version-link]
[![PHP Version][php-version-badge]][packagist-version-link]
[![Total Downloads][packagist-downloads-badge]][packagist-downloads-link]
[![License][license-badge]][license-link]

[![REST API CI][ci-badge]][ci-link]
[![Quality Gate Status][sonar-quality-badge]][sonar-link]
[![Coverage][sonar-coverage-badge]][sonar-link]
[![PDS Skeleton][pds-skeleton-badge]][pds-skeleton-link]

[![Discord][discord-badge]][discord-link]
[![Contributors][contributors-badge]][contributors-link]
[![OpenCollective Backers][oc-backers-badge]][oc-backers-link]
[![OpenCollective Sponsors][oc-sponsors-badge]][oc-sponsors-link]

A sample REST API for the [Phalcon Framework](https://github.com/phalcon/cphalcon).
It showcases JWT authentication, a [JSON:API](https://jsonapi.org) response layer
(sparse fieldsets, includes, sorting), a lazy middleware chain, and the model /
repository / transformer split behind it.

It runs on **Phalcon v5** (the C extension, default) and on **Phalcon v6**
(the `phalcon/phalcon` package) from the same source.

## Requirements

* PHP 8.1 or newer
* MySQL 8.0 and Redis (both provided by the Docker stack)
* Docker + Docker Compose (recommended), or a local PHP with the Phalcon extension
  (see [docs/installation.md](docs/installation.md))

## Quick start (Docker)

```bash
cp resources/.env.example .env
docker compose up -d --build

# Create the database schema (migrations are not run on boot)
docker compose exec app composer install
docker compose exec app composer migrate
```

The API is then served at <http://localhost:8080>. There are no bundled fixtures, so
every collection starts empty; see [docs/usage.md](docs/usage.md) for the request and
response format and how authentication works.

> **Note:** `app` is the Compose *service* name, used as-is by `docker compose exec`.
> The running container is named `${PROJECT_PREFIX}-app` (`rest-api-app` by default,
> set via `PROJECT_PREFIX` in `.env`). If you address it with plain `docker exec`, use
> the container name, e.g. `docker exec rest-api-app composer migrate`.

### Choosing the Phalcon and PHP versions

```bash
docker compose up -d --build                      # v5 (C extension, default)
PHALCON_VARIANT=v6 docker compose up -d --build   # v6 (phalcon/phalcon)

PHP_VERSION=8.1 docker compose up -d --build       # pick a PHP version (8.1+)
```

The two Phalcon variants are mutually exclusive: the v5 image installs the C extension,
the v6 image installs the pure-PHP package instead. `APP_PORT` in `.env` changes the host
port, so this app can run alongside the other Phalcon sample applications.
[docs/installation.md](docs/installation.md) covers running several versions side by side.

## Quick start (Composer)

Prefer a local PHP over Docker? Bootstrap a fresh copy straight from Packagist:

```bash
composer create-project phalcon/rest-api rest-api
cd rest-api
```

The post-create hook copies `resources/.env.example` to `.env` and prints the next steps.
[docs/installation.md](docs/installation.md) has the full local walkthrough (installing the
Phalcon extension with [PIE](https://github.com/php/pie), the database, and serving).

## Composer scripts

Run them inside the container, e.g. `docker compose exec app composer cs`:

| Script | Description |
| --- | --- |
| `composer cs` | PHP_CodeSniffer (PSR-12) |
| `composer cs-fix` | Auto-fix coding standard issues (phpcbf) |
| `composer cs-fixer` | PHP CS Fixer (dry-run) |
| `composer cs-fixer-fix` | Apply PHP CS Fixer |
| `composer analyze` | PHPStan static analysis (level 8) |
| `composer test` | The default (unit) PHPUnit suite via `vendor/bin/talon` |
| `composer test-coverage` | PHPUnit + Clover coverage (`tests/_output/coverage.xml`) |
| `composer test-mutation` | Infection mutation testing (see below) |
| `composer migrate` | Run database migrations (Phinx) |

> `composer analyze` resolves Phalcon classes from the `phalcon/phalcon` (v6) source, so
> run it where the v5 C extension is **not** loaded (the CI `quality` job, or a plain host).
> The coding-standard and test scripts are unaffected.

## Features

* **JWT authentication** — [JSON Web Tokens](https://jwt.io) let a client authenticate once
  at `/login` and then carry a bearer token; the token lifetime is configurable in `.env`.
* **JSON:API responses** — every response follows the [JSON:API](https://jsonapi.org)
  standard: a uniform envelope, compound documents, includes (related data), sparse
  fieldsets, and sorting.
* **A lazy middleware chain** — `NotFound`, `Authentication`, and `Response`, each attached to
  the Micro application and resolved only when a request reaches it.
* **Public fields are opt-in per model** — a model declares exactly which columns the API may
  publish, so adding a column never exposes it by accident (`Users`, for instance, never
  returns its password or token secrets).

See [docs/usage.md](docs/usage.md) for the endpoints, query parameters, and response format.

## Running the tests

The suite is split into four PHPUnit testsuites — `unit`, `integration`, `api`, and `cli` —
orchestrated by [`phalcon/talon`](https://github.com/phalcon/talon). The `api` suite drives
the running application over real HTTP, so it needs the web server up (nginx in the Docker
stack, reached via `TALON_REST_URL` in `tests/.env.test`).

```bash
docker compose exec app composer migrate    # once - create the schema
docker compose exec app composer test       # the default (unit) suite
docker compose exec app vendor/bin/talon run all   # every suite, one process each
```

### Mutation testing

`composer test-mutation` runs [Infection](https://infection.github.io/). The dependency is
**not** shipped — it pulls `thecodingmachine/safe` at `dev-master`, which deprecation-warns on
newer PHP — so install it on demand:

```bash
docker compose exec app composer require --dev infection/infection
docker compose exec app composer test-mutation
docker compose exec app composer remove --dev infection/infection
```

The configuration in `resources/infection.json5` stays in the repository. Runs are
single-threaded on purpose: the suite shares one MySQL database, so parallel mutants would
corrupt each other's data.

## Project layout

Follows the [PDS skeleton](https://github.com/php-pds/skeleton):

```
bin/        command-line entry point (bin/cli)
public/     web server root (index.php)
resources/  tooling configs, docker, phinx, migrations
src/        application source
tests/      PHPUnit suites (unit, integration, api, cli)
storage/    runtime cache and logs
```

## Documentation

* [docs/installation.md](docs/installation.md) — Docker and local (non-Docker) setup
* [docs/usage.md](docs/usage.md) — endpoints, includes, sparse fields, sorting, and the
  response format

## License

Phalcon REST API is open-sourced software licensed under the New BSD License.
See [LICENSE](LICENSE).

<!-- Badges -->
[packagist-version-badge]:   https://img.shields.io/packagist/v/phalcon/rest-api?include_prereleases&style=flat-square&logo=packagist&logoColor=white
[packagist-version-link]:    https://packagist.org/packages/phalcon/rest-api
[packagist-downloads-badge]: https://img.shields.io/packagist/dt/phalcon/rest-api?style=flat-square&logo=packagist&logoColor=white
[packagist-downloads-link]:  https://packagist.org/packages/phalcon/rest-api/stats
[php-version-badge]:         https://img.shields.io/packagist/php-v/phalcon/rest-api?style=flat-square&logo=php&logoColor=white
[license-badge]:             https://img.shields.io/github/license/phalcon/rest-api?style=flat-square&logo=opensourceinitiative&logoColor=white
[license-link]:              https://github.com/phalcon/rest-api/blob/master/LICENSE
[ci-badge]:                  https://github.com/phalcon/rest-api/actions/workflows/main.yml/badge.svg?branch=master
[ci-link]:                   https://github.com/phalcon/rest-api/actions/workflows/main.yml
[sonar-quality-badge]:       https://sonarcloud.io/api/project_badges/measure?project=phalcon_rest-api&metric=alert_status
[sonar-coverage-badge]:      https://sonarcloud.io/api/project_badges/measure?project=phalcon_rest-api&metric=coverage
[sonar-link]:                https://sonarcloud.io/summary/new_code?id=phalcon_rest-api
[pds-skeleton-badge]:        https://img.shields.io/badge/pds-skeleton-blue.svg?style=flat-square
[pds-skeleton-link]:         https://github.com/php-pds/skeleton
[discord-badge]:             https://img.shields.io/discord/310910488152375297?label=Discord&logo=discord&style=flat-square
[discord-link]:              https://phalcon.io/discord
[contributors-badge]:        https://img.shields.io/github/contributors/phalcon/rest-api?style=flat-square&logo=github&logoColor=white
[contributors-link]:         https://github.com/phalcon/rest-api/graphs/contributors
[oc-backers-badge]:          https://img.shields.io/opencollective/backers/phalcon?style=flat-square&logo=opencollective&logoColor=white
[oc-backers-link]:           https://opencollective.com/phalcon
[oc-sponsors-badge]:         https://img.shields.io/opencollective/sponsors/phalcon?style=flat-square&logo=opencollective&logoColor=white
[oc-sponsors-link]:          https://opencollective.com/phalcon
