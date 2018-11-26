<?php
declare(strict_types=1);

namespace Gewaer\Models;

class Roles extends AbstractModel
{
    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $name;

    /**
     *
     * @var integer
     */
    public $description;

    /**
     *
     * @var integer
     */
    public $scope;

    /**
     *
     * @var integer
     */
    public $company_id;

    /**
     *
     * @var string
     */
    public $created_at;

    /**
     *
     * @var string
     */
    public $updated_at;

    /**
     *
     * @var integer
     */
    public $is_deleted;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->setSource('roles');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'roles';
    }
}
