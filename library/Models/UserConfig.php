<?php
declare(strict_types=1);

namespace Gewaer\Models;

class UserConfig extends \Baka\Auth\Models\UserConfig
{
    /**
     *
     * @var integer
     */
    public $users_id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $value;

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
     * Initialize method for model.
     */
    public function initialize()
    {
        parent::initialize();

        $this->setSource('user_config');
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource(): string
    {
        return 'user_config';
    }
}
