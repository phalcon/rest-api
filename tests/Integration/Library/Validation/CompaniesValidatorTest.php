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

namespace Phalcon\Api\Tests\Integration\Library\Validation;

use Phalcon\Api\Tests\Integration\AbstractIntegrationTestCase;
use Phalcon\Api\Validation\CompaniesValidator;

use function count;

final class CompaniesValidatorTest extends AbstractIntegrationTestCase
{
    public function testValidator(): void
    {
        $validation = new CompaniesValidator();
        $_POST      = [
            'name'    => '',
            'address' => '123 Phalcon way',
            'city'    => 'World',
            'phone'   => '555-999-4444',
        ];
        $messages   = $validation->validate($_POST);
        $this->assertSame(1, count($messages));
        $this->assertSame('The company name is required', $messages[0]->getMessage());
    }
}
