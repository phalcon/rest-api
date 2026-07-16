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

namespace Phalcon\Api\Tests\Integration;

use Phalcon\Api\Bootstrap\Api;
use Phalcon\Api\Models\Companies;
use Phalcon\Api\Models\CompaniesXProducts;
use Phalcon\Api\Models\Individuals;
use Phalcon\Api\Models\IndividualTypes;
use Phalcon\Api\Models\Products;
use Phalcon\Api\Models\ProductTypes;
use Phalcon\Api\Mvc\Model\AbstractModel;
use Phalcon\Config\Config as PhConfig;
use Phalcon\Di\DiInterface;
use Phalcon\Talon\PHPUnit\AbstractUnitTestCase;
use RuntimeException;

use function array_keys;
use function array_map;
use function array_merge;
use function call_user_func;
use function count;
use function implode;
use function is_array;
use function rtrim;
use function sprintf;
use function uniqid;

/**
 * Ported from the Codeception `Helper\Integration` module. Boots the application
 * container for each test and tracks the records a test creates so they can be
 * removed afterwards.
 */
abstract class AbstractIntegrationTestCase extends AbstractUnitTestCase
{
    protected ?DiInterface $diContainer = null;

    /** @var array<string, bool> */
    protected array $options = ['rollback' => false];

    /** @var array<string, array<string, mixed>> */
    protected array $savedModels = [];

    /** @var array<int, AbstractModel> */
    protected array $savedRecords = [];

    protected function setUp(): void
    {
        parent::setUp();

        $app               = new Api();
        $this->diContainer = $app->getContainer();

        if ($this->options['rollback']) {
            $this->diContainer->get('db')->begin();
        }

        $this->savedModels  = [];
        $this->savedRecords = [];
    }

    protected function tearDown(): void
    {
        if (!$this->options['rollback']) {
            foreach ($this->savedRecords as $record) {
                $record->delete();
            }
        } else {
            $this->diContainer->get('db')->rollback();
        }

        $this->diContainer->get('db')->close();

        parent::tearDown();
    }

    protected function addCompanyRecord(
        string $namePrefix = '',
        string $addressPrefix = '',
        string $cityPrefix = '',
        string $phonePrefix = ''
    ): Companies {
        return $this->haveRecordWithFields(
            Companies::class,
            [
                'name'    => uniqid($namePrefix),
                'address' => uniqid($addressPrefix),
                'city'    => uniqid($cityPrefix),
                'phone'   => uniqid($phonePrefix),
            ]
        );
    }

    protected function addCompanyXProduct(int $companyId, int $productId): CompaniesXProducts
    {
        return $this->haveRecordWithFields(
            CompaniesXProducts::class,
            [
                'companyId' => $companyId,
                'productId' => $productId,
            ]
        );
    }

    protected function addIndividualRecord(string $namePrefix = '', int $comId = 0, int $typeId = 0): Individuals
    {
        return $this->haveRecordWithFields(
            Individuals::class,
            [
                'companyId' => $comId,
                'typeId'    => $typeId,
                'prefix'    => uniqid(),
                'first'     => uniqid($namePrefix),
                'middle'    => uniqid(),
                'last'      => uniqid(),
                'suffix'    => uniqid(),
            ]
        );
    }

    protected function addIndividualTypeRecord(string $namePrefix = ''): IndividualTypes
    {
        return $this->haveRecordWithFields(
            IndividualTypes::class,
            [
                'name'        => uniqid($namePrefix),
                'description' => uniqid(),
            ]
        );
    }

    protected function addProductRecord(string $namePrefix = '', int $typeId = 0): Products
    {
        return $this->haveRecordWithFields(
            Products::class,
            [
                'name'        => uniqid($namePrefix),
                'typeId'      => $typeId,
                'description' => uniqid(),
                'quantity'    => 25,
                'price'       => 19.99,
            ]
        );
    }

    protected function addProductTypeRecord(string $namePrefix = ''): ProductTypes
    {
        return $this->haveRecordWithFields(
            ProductTypes::class,
            [
                'name'        => uniqid($namePrefix),
                'description' => uniqid(),
            ]
        );
    }

    /**
     * Returns the relationships that a model has.
     *
     * @return array<int, array<int, mixed>>
     */
    protected function getModelRelationships(string $class): array
    {
        /** @var AbstractModel $model */
        $model         = new $class();
        $manager       = $model->getModelsManager();
        $relationships = $manager->getRelations($class);

        $data = [];
        foreach ($relationships as $relationship) {
            $data[] = [
                $relationship->getType(),
                $relationship->getFields(),
                $relationship->getReferencedModel(),
                $relationship->getReferencedFields(),
                $relationship->getOptions(),
            ];
        }

        return $data;
    }

    /**
     * Get a record from $modelName with the fields provided.
     *
     * @param array<string, mixed> $fields
     *
     * @return bool|AbstractModel
     */
    protected function getRecordWithFields(string $modelName, array $fields = [])
    {
        $record = false;
        if (count($fields) > 0) {
            $conditions = '';
            $bind       = [];
            foreach ($fields as $field => $value) {
                $conditions   .= sprintf(
                    '%s = :%s: AND ',
                    $field,
                    $field
                );
                $bind[$field] = $value;
            }

            $conditions = rtrim($conditions, ' AND ');

            /** @var AbstractModel $record */
            $record = $modelName::findFirst(
                [
                    'conditions' => $conditions,
                    'bind'       => $bind,
                ]
            );
        }

        return $record;
    }

    protected function grabDi(): ?DiInterface
    {
        return $this->diContainer;
    }

    /**
     * @return mixed
     */
    protected function grabFromDi(string $name)
    {
        return $this->diContainer->get($name);
    }

    /**
     * @param array<string, mixed> $configData
     */
    protected function haveConfig(array $configData): void
    {
        $config = new PhConfig($configData);
        $this->diContainer->set('config', $config);
    }

    /**
     * Checks model fields.
     *
     * @param array<int, string> $fields
     */
    protected function haveModelDefinition(string $modelName, array $fields): void
    {
        /** @var AbstractModel $model */
        $model      = new $modelName();
        $metadata   = $model->getModelsMetaData();
        $attributes = $metadata->getAttributes($model);

        $this->assertEquals(
            count($fields),
            count($attributes),
            "Field count not correct for $modelName"
        );

        foreach ($fields as $value) {
            $this->assertContains(
                $value,
                $attributes,
                "Field not exists in $modelName"
            );
        }
    }

    /**
     * Create a record for $modelName with the fields provided.
     *
     * @param array<string, mixed> $fields
     *
     * @return mixed
     */
    protected function haveRecordWithFields(string $modelName, array $fields = [])
    {
        $record = new $modelName();
        foreach ($fields as $key => $val) {
            $record->set($key, $val);
        }

        $result                        = $record->save();
        $this->savedModels[$modelName] = $fields;
        $this->assertNotSame(false, $result);

        $this->savedRecords[] = $record;

        return $record;
    }

    /**
     * @param mixed $service
     */
    protected function haveService(string $name, $service): void
    {
        $this->diContainer->set($name, $service);
    }

    protected function removeService(string $name): void
    {
        if ($this->diContainer->has($name)) {
            $this->diContainer->remove($name);
        }
    }

    /**
     * Check that a record created with haveRecordWithFields can be fetched and
     * that all its fields contain valid values.
     *
     * @param string|array<int, string> $by
     * @param array<string, mixed>      $except
     *
     * @return mixed
     */
    protected function seeRecordFieldsValid(string $modelName, $by, array $except = [])
    {
        if (!isset($this->savedModels[$modelName])) {
            throw new RuntimeException(
                'Should be used after haveModelWithFields with ' . $modelName
            );
        }

        $fields = $this->savedModels[$modelName];
        if (!is_array($by)) {
            $by = [$by];
        }

        $bySelector = implode(
            ' AND ',
            array_map(
                static function ($key) {
                    return "$key = :$key:";
                },
                $by
            )
        );

        $byBind = [];
        foreach ($by as $byVal) {
            if (!isset($fields[$byVal])) {
                throw new RuntimeException("Field $byVal is not set");
            }
            $byBind[$byVal] = $fields[$byVal];
        }

        $record = call_user_func(
            [
                $modelName, 'findFirst',
            ],
            [
                'conditions' => $bySelector,
                'bind'       => $byBind,
            ]
        );

        if (!$record) {
            $this->fail("Record $modelName not found");
        }

        foreach ($fields as $key => $val) {
            if (isset($except[$key])) {
                continue;
            }

            $this->assertEquals(
                $val,
                $record->get($key),
                "Field in $modelName for $key not valid"
            );
        }

        return $record;
    }

    /**
     * Checks that a record exists and has the provided fields.
     *
     * @param array<string, mixed> $by
     * @param array<string, mixed> $fields
     */
    protected function seeRecordSaved(string $model, array $by, array $fields): void
    {
        $this->savedModels[$model] = array_merge($by, $fields);

        $record = $this->seeRecordFieldsValid(
            $model,
            array_keys($by),
            array_keys($by)
        );

        $this->savedRecords[] = $record;
    }
}
