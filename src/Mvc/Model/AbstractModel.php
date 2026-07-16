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

namespace Phalcon\Api\Mvc\Model;

use Phalcon\Api\Exception\ModelException;
use Phalcon\Filter\Filter;
use Phalcon\Logger\Logger;
use Phalcon\Mvc\Model as PhModel;

use function sprintf;

abstract class AbstractModel extends PhModel
{
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
     * The fields this model is willing to publish through the API.
     *
     * Deliberately separate from getModelFilters(): that map answers how a
     * field is sanitised, which is not the same question as whether the world
     * may see it. Every model must answer this one for itself, so that adding
     * a column never publishes it by accident.
     *
     * @return array<int, string>
     */
    abstract public function getPublicFields(): array;

    /**
     * Master initializer
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->setup(
            [
                'phqlLiterals'       => false,
                'notNullValidations' => false,
            ]
        );
    }

    /**
     * Sets a field in the model.
     *
     * The value is stored as it was given. Sanitising is get()'s job - doing it
     * here as well escaped everything twice, so a stored '&' came back out as
     * '&amp;amp;'.
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
     * Gets or sets a field, sanitising on the way out only.
     *
     * Both directions used to sanitise, which escaped every value twice: set()
     * turned '&' into '&amp;' and get() then turned that into '&amp;amp;'. The
     * filter map is still consulted for both, so an unknown field is rejected
     * either way.
     *
     * @param string $type
     * @param string $field
     * @param mixed  $value
     *
     * @return mixed
     * @throws ModelException
     */
    private function getSetFields(string $type, string $field, $value = ''): mixed
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
            $this->$field = $value;
        }

        return $return;
    }

    /**
     * Uses the Phalcon Filter to sanitize the variable passed
     *
     * @param mixed                    $value  The value to sanitize
     * @param string|array<int, string> $filter The filter, or a chain of them
     *
     * @return mixed
     */
    private function sanitize($value, $filter): mixed
    {
        /** @var Filter $filterService */
        $filterService = $this->getDI()
                              ->get('filter')
        ;

        return $filterService->sanitize($value, $filter);
    }
}
