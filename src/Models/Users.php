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

namespace Phalcon\Api\Models;

use Phalcon\Api\Mvc\Model\AbstractModel;
use Phalcon\Filter\Filter;

/**
 * The user record, and nothing about tokens.
 *
 * Issuing a token and validating one are TokenService's, which reads the
 * fields below rather than the record building and checking its own
 * credentials.
 */
class Users extends AbstractModel
{
    /**
     * Model filters
     *
     * @return array<string,string>
     */
    public function getModelFilters(): array
    {
        return [
            'id'            => Filter::FILTER_ABSINT,
            'status'        => Filter::FILTER_ABSINT,
            'username'      => Filter::FILTER_STRING,
            'password'      => Filter::FILTER_STRING,
            'issuer'        => Filter::FILTER_STRING,
            'tokenPassword' => Filter::FILTER_STRING,
            'tokenId'       => Filter::FILTER_STRING,
        ];
    }

    /**
     * `password` and `tokenPassword` are deliberately absent. `tokenPassword`
     * is the passphrase the token signature is verified against - publishing it
     * would let any caller forge a token for this user.
     *
     * @return array<int, string>
     */
    public function getPublicFields(): array
    {
        return [
            'id',
            'status',
            'username',
            'issuer',
            'tokenId',
        ];
    }

    /**
     * `tokenId` is published but not sortable: it is an opaque identifier, so
     * ordering by it says nothing, and every field that leaves this list stops
     * being a way to probe the table.
     *
     * @return array<int, string>
     */
    public function getSortableFields(): array
    {
        return [
            'id',
            'status',
            'username',
            'issuer',
        ];
    }

    /**
     * Returns the source table from the database
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->setSource('co_users');
    }

    /**
     * Never. A row of this table carries the password hash and the token
     * passphrase, and the cache is a shared Redis that outlives the process.
     * It is also keyed on the query, so a cached row would keep answering with
     * a stale hash - rejecting a new password and accepting the old one for as
     * long as the entry lived.
     *
     * The cost is one indexed lookup per authenticated request.
     *
     * @return bool
     */
    public function isCacheable(): bool
    {
        return false;
    }
}
