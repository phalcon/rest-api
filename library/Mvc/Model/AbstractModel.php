<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Mvc\Model;

use Monolog\Logger;
use Phalcon\Api\Exception\ModelException;
use Phalcon\Filter;
use Phalcon\Mvc\Model as PhModel;

abstract class AbstractModel extends PhModel
{
    /**
     * Master initializer
     */
    public function initialize()
    {
        $this->setup(
            [
                'phqlLiterals'       => false,
                'notNullValidations' => false,
            ]
        );
    }

    /**
     * Gets a field from this model
     *
     * @param string $field The name of the field
     *
     * @return mixed
     * @throws ModelException
     */
    public function get($field)
    {
        return $this->getSetFields('get', $field);
    }

    /**
     * Returns an array of the fields/filters for this model
     *
     * @return array<string,string>
     */
    abstract public function getModelFilters(): array;

    /**
     * Returns model messages
     *
     * @param Logger|null $logger
     *
     * @return  string
     */
    public function getModelMessages(Logger $logger = null): string
    {
        $error = '';
        foreach ($this->getMessages() as $message) {
            $error .= $message->getMessage() . '<br />';
            if (null !== $logger) {
                $logger->error($message->getMessage());
            }
        }

        return $error;
    }

    /**
     * Sets a field in the model sanitized
     *
     * @param string $field The name of the field
     * @param mixed  $value The value of the field
     *
     * @return AbstractModel
     * @throws ModelException
     */
    public function set($field, $value): AbstractModel
    {
        $this->getSetFields('set', $field, $value);

        return $this;
    }

    /**
     * Gets or sets a field and sanitizes it if necessary
     *
     * @param string $type
     * @param string $field
     * @param mixed  $value
     *
     * @return mixed
     * @throws ModelException
     */
    private function getSetFields(string $type, string $field, $value = '')
    {
        $return      = null;
        $modelFields = $this->getModelFilters();
        $filter      = $modelFields[$field] ?? false;

        if (false === $filter) {
            throw new ModelException(
                sprintf(
                    'Field [%s] not found in this model',
                    $field
                )
            );
        }

        if ('get' === $type) {
            $return = $this->sanitize($this->$field, $filter);
        } else {
            $this->$field = $this->sanitize($value, $filter);
        }

        return $return;
    }

    /**
     * Uses the Phalcon Filter to sanitize the variable passed
     *
     * @param mixed        $value  The value to sanitize
     * @param string|array $filter The filter to apply
     *
     * @return mixed
     */
    private function sanitize($value, $filter)
    {
        /** @var Filter $filterService */
        $filterService = $this->getDI()->get('filter');

        return $filterService->sanitize($value, $filter);
    }
}
