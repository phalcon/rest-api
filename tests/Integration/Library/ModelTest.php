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

namespace Phalcon\Api\Tests\Integration\Library;

use Phalcon\Api\Exception\ModelException;
use Phalcon\Api\Models\Users;
use Phalcon\Api\Tests\Integration\AbstractIntegrationTestCase;
use Phalcon\Logger\Logger;
use Phalcon\Messages\Message;

use function Phalcon\Api\Core\appPath;

final class ModelTest extends AbstractIntegrationTestCase
{
    public function testModelGetSetFields(): void
    {
        $this->haveRecordWithFields(
            Users::class,
            [
                'username'      => 'testusername',
                'password'      => 'testpass',
                'status'        => 1,
                'issuer'        => 'phalcon.io',
                'tokenPassword' => '12345',
                'tokenId'       => '00110011',
            ]
        );
    }

    public function testModelSetNonExistingFields(): void
    {
        $this->expectException(ModelException::class);

        $fixture = new Users();
        $fixture->set('id', 1000)
                ->set('some_field', true)
                ->save()
        ;
    }

    public function testModelGetNonExistingFields(): void
    {
        /** @var Users $user */
        $user = $this->haveRecordWithFields(
            Users::class,
            [
                'username'      => 'testusername',
                'password'      => 'testpass',
                'status'        => 1,
                'issuer'        => 'phalcon.io',
                'tokenPassword' => '12345',
                'tokenId'       => '00110011',
            ]
        );

        $this->expectException(ModelException::class);

        $user->get('some_field');
    }

    public function testModelUpdateFields(): void
    {
        /** @var Users $user */
        $user = $this->haveRecordWithFields(
            Users::class,
            [
                'username'      => 'testusername',
                'password'      => 'testpass',
                'status'        => 1,
                'issuer'        => 'phalcon.io',
                'tokenPassword' => '12345',
                'tokenId'       => '00110011',
            ]
        );

        $user->set('username', 'testusername')
             ->save()
        ;

        $this->assertSame($user->get('username'), 'testusername');
        $this->assertSame($user->get('password'), 'testpass');
        $this->assertSame($user->get('issuer'), 'phalcon.io');
        $this->assertSame($user->get('tokenPassword'), '12345');
        $this->assertSame($user->get('tokenId'), '00110011');
    }

    public function testModelUpdateFieldsNotSanitized(): void
    {
        /** @var Users $user */
        $user = $this->haveRecordWithFields(
            Users::class,
            [
                'username'      => 'testusername',
                'password'      => 'testpass',
                'status'        => 1,
                'issuer'        => 'phalcon.io',
                'tokenPassword' => '12345',
                'tokenId'       => '00110011',

            ]
        );

        $user->set('password', 'abcde\nfg')
             ->save()
        ;
        $this->assertSame($user->get('password'), 'abcde\nfg');

        /** Not sanitized */
        $user->set('password', 'abcde\nfg', false)
             ->save()
        ;
        $this->assertSame($user->get('password'), 'abcde\nfg');
    }

    public function testCheckModelMessages(): void
    {
        $user = $this->mockWithConstructor(
            Users::class,
            [],
            [
                'save'        => false,
                'getMessages' => [
                    new Message('error 1'),
                    new Message('error 2'),
                ],
            ]
        );

        $result = $user
            ->set('username', 'test')
            ->save()
        ;
        $this->assertFalse($result);

        $this->assertSame('error 1<br />error 2<br />', $user->getModelMessages());
    }

    public function testCheckModelMessagesWithLogger(): void
    {
        /** @var Logger $logger */
        $logger = $this->grabFromDi('logger');
        $user   = $this->mockWithConstructor(
            Users::class,
            [],
            [
                'save'        => false,
                'getMessages' => [
                    new Message('error 1'),
                    new Message('error 2'),
                ],
            ]
        );

        $fileName = appPath('storage/logs/api.log');
        $result   = $user
            ->set('username', 'test')
            ->save()
        ;
        $this->assertFalse($result);
        $this->assertSame('error 1<br />error 2<br />', $user->getModelMessages());

        $user->getModelMessages($logger);

        $this->assertFileContentsContains($fileName, "error 1\n");
        $this->assertFileContentsContains($fileName, "error 2\n");
    }
}
