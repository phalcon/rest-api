<?php

declare(strict_types=1);

namespace Niden\Mvc\Model;

use Monolog\Logger;
use Niden\Exception\ModelException;
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
        $this->keepSnapshots(true);
        $this->useDynamicUpdate(true);

        $eventsManager = $this->getDI()->getShared('eventsManager');
        $this->setEventsManager($eventsManager);
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
     * @return string
     */
    abstract public function getTablePrefix(): string;

    /**
     * Sets a field in the model sanitized
     *
     * @param string $field    The name of the field
     * @param mixed  $value    The value of the field
     * @param bool   $sanitize Whether to sanitize input or not
     *
     * @return AbstractModel
     * @throws ModelException
     */
    public function set($field, $value, $sanitize = true): AbstractModel
    {
        $this->getSetFields('set', $field, $value, $sanitize);

        return $this;
    }

    /**
     * Gets or sets a field and sanitizes it if necessary
     *
     * @param string $type
     * @param string $field
     * @param mixed  $value
     * @param bool   $sanitize
     *
     * @return mixed
     * @throws ModelException
     */
    private function getSetFields(string $type, string $field, $value = '', bool $sanitize = true)
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
            $return = $this->sanitize($this->$field, $filter, $sanitize);
        } else {
            $this->$field = $this->sanitize($value, $filter, $sanitize);
        }

        return $return;
    }

    /**
     * Uses the Phalcon Filter to sanitize the variable passed
     *
     * @param mixed        $value  The value to sanitize
     * @param string|array $filter The filter to apply
     * @param bool         $sanitize
     *
     * @return mixed
     */
    private function sanitize($value, $filter, bool $sanitize = true)
    {
        /** @var Filter $filterService */
        $filterService = $this->getDI()->get('filter');

        if (true === $sanitize) {
            return $filterService->sanitize($value, $filter);
        } else {
            return $value;
        }
    }
}
