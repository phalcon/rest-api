<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Constants;

/**
 * One name, five meanings - deliberately.
 *
 * Each constant below is at once the URL path segment (`/companies`), the
 * model relationship alias (`Companies::initialize()`), the JSON:API resource
 * type, the `?includes=` name, and the `fields[...]` key. Stating it once is
 * what keeps those five agreeing with each other.
 *
 * The cost is worth knowing: this application publishes its persistence names.
 * A caller sorting by `?sort=name`, selecting `?fields[companies]=name,city`,
 * or asking for `?includes=individuals` is naming a model property or a
 * relationship alias directly - there is no translation layer between the two.
 * Renaming a model property, a relationship alias or a table therefore changes
 * the public contract, and clients are the one thing here that cannot be
 * redeployed alongside the change.
 *
 * That is a fair trade for a reference application - the wiring stays legible,
 * which is the point of it - but it is a trade, not an accident. An API with
 * real consumers would want a field map between the two vocabularies.
 */
class Relationships
{
    public const COMPANIES        = 'companies';
    public const INDIVIDUAL_TYPES = 'individual-types';
    public const INDIVIDUALS      = 'individuals';
    public const PRODUCT_TYPES    = 'product-types';
    public const PRODUCTS         = 'products';
    public const USERS            = 'users';
}
