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

namespace Phalcon\Api\Tests\Api\Companies;

use Phalcon\Api\Constants\Relationships;
use Phalcon\Api\Tests\Support\Data;

use function json_decode;
use function sprintf;

/**
 * The two path forms of a relationship.
 *
 * Both routes were registered and both are advertised in every response's
 * `links`, but neither was ever exercised: the handler took one parameter, so
 * the relationship segment was parsed and then dropped, and the endpoints
 * answered with the plain record.
 *
 * Asserted against the `?includes=` form rather than a hand-built document, so
 * the two ways of asking for the same thing cannot drift apart.
 */
final class GetRelationshipsTest extends AbstractGetTestCase
{
    public function testRelatedRouteReturnsTheRelationship(): void
    {
        [$com] = $this->addRecords();
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $expected = $this->documentFor(
            $token,
            sprintf(
                Data::$companiesRecordIncludesUrl,
                $com->get('id'),
                Relationships::PRODUCTS
            )
        );
        $actual = $this->documentFor(
            $token,
            sprintf(
                '/%s/%s/%s',
                Relationships::COMPANIES,
                $com->get('id'),
                Relationships::PRODUCTS
            )
        );

        $this->assertArrayHasKey('included', $actual);
        $this->assertSame($expected, $actual);
    }

    public function testRelationshipsRouteReturnsTheRelationship(): void
    {
        [$com] = $this->addRecords();
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $expected = $this->documentFor(
            $token,
            sprintf(
                Data::$companiesRecordIncludesUrl,
                $com->get('id'),
                Relationships::INDIVIDUALS
            )
        );
        $actual = $this->documentFor(
            $token,
            sprintf(
                '/%s/%s/relationships/%s',
                Relationships::COMPANIES,
                $com->get('id'),
                Relationships::INDIVIDUALS
            )
        );

        $this->assertArrayHasKey('included', $actual);
        $this->assertSame($expected, $actual);
    }

    /**
     * Several at once, the way the route regex allows.
     */
    public function testRelatedRouteAcceptsSeveralRelationships(): void
    {
        [$com] = $this->addRecords();
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $both = Relationships::INDIVIDUALS . ',' . Relationships::PRODUCTS;

        $expected = $this->documentFor(
            $token,
            sprintf(Data::$companiesRecordIncludesUrl, $com->get('id'), $both)
        );
        $actual = $this->documentFor(
            $token,
            sprintf(
                '/%s/%s/%s',
                Relationships::COMPANIES,
                $com->get('id'),
                $both
            )
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * A relationship this resource does not have is a URL that does not exist.
     * `?includes=unknown` stays silent - that is a request for something extra
     * - but a path names the resource itself.
     */
    public function testUnknownRelatedRouteIsNotFound(): void
    {
        $company = $this->addCompanyRecord('com-a-');
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $this->sendGetAs(
            $token,
            sprintf(
                '/%s/%s/unicorns',
                Relationships::COMPANIES,
                $company->get('id')
            )
        );

        $this->assertResponseIs404();
    }

    public function testUnknownRelationshipsRouteIsNotFound(): void
    {
        $company = $this->addCompanyRecord('com-a-');
        $this->addApiUserRecord();
        $token = $this->apiLogin();

        $this->sendGetAs(
            $token,
            sprintf(
                '/%s/%s/relationships/unicorns',
                Relationships::COMPANIES,
                $company->get('id')
            )
        );

        $this->assertResponseIs404();
    }

    /**
     * The response body without the per-request envelope, so two requests can
     * be compared.
     *
     * @param string $token
     * @param string $url
     *
     * @return array<string, mixed>
     */
    private function documentFor(string $token, string $url): array
    {
        $this->sendGetAs($token, $url);
        $this->assertResponseIsSuccessful();

        /** @var array<string, mixed> $response */
        $response = json_decode($this->grabResponse(), true);

        unset($response['meta'], $response['jsonapi']);

        return $response;
    }
}
