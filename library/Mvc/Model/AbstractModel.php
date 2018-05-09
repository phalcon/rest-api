<?php

namespace Niden\Mvc\Model;

use function call_user_func_array;
use Niden\Exception\ModelException;
use Niden\Logger;
use Phalcon\Filter;
use Phalcon\Mvc\Model as PhModel;

abstract class AbstractModel extends PhModel
{
    /** @var array */
    protected $belong = [];

    /** @var array */
    protected $many = [];

    /** @var array */
    protected $manyToMany = [];

    /** @var array */
    protected $one = [];

    /**
     * Master initializer
     */
    public function initialize()
    {
        $this->setup(
            [
                'phqlLiterals'       => true,
                'notNullValidations' => false,
            ]
        );
        $this->keepSnapshots(true);
        $this->useDynamicUpdate(true);

        $eventsManager = $this->getDI()->getShared('eventsManager');
        $this->setEventsManager($eventsManager);

        $this->setRelationships('hasOne', $this->one);
        $this->setRelationships('hasMany', $this->many);
        $this->setRelationships('belongsTo', $this->belong);
        $this->setRelationships('hasManyToMany', $this->manyToMany);
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
     * Returns an array with the options for a relationship
     *
     * @param string $relationship
     * @param array  $options
     *
     * @return array
     */
    protected function getRelationshipOptions(string $relationship, array $options = []): array
    {
        return array_merge(
            [
                'alias'    => $relationship,
                'reusable' => true,
            ],
            $options
        );
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

    /**
     * Loops through the set relationship arrays and calls the relevant function
     *
     * @param string $method
     * @param array  $relationships
     */
    private function setRelationships(string $method, array $relationships)
    {
        foreach ($relationships as $relationship) {
            $lastElement = end($relationship);
            $key         = key($relationship);

            $relationship[$key] = $this->getRelationshipOptions($lastElement);

            call_user_func_array([$this, $method], $relationship);
        }
    }
}
